<?php

namespace Modules\Profile\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Profile\Entities\Profile;
use Modules\Profile\Http\Requests\CreateProfileRequest;
use Modules\Profile\Http\Requests\UpdateProfileRequest;
use Modules\Profile\Repositories\ProfileRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\User\Http\Controllers\Admin\BaseUserModuleController;
use Modules\User\Repositories\UserRepository;
use Modules\User\Contracts\Authentication;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Http\Requests\CreateUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\Profile\Repositories\GruppoRepository;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Area;
use Modules\User\Entities\Sentinel\User;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Str;

class ProfileController extends BaseUserModuleController
{
    /**
     * @var ProfileRepository
     */
    private $profile;
    /**
     * @var UserRepository
     */
    private $user;
    /**
     * @var RoleRepository
     */
    private $role;

    /**
     * @var Authentication
     */
    private $auth;

    public function __construct(
        ProfileRepository $profile,
        UserRepository $user,
        PermissionManager $permissions,
        RoleRepository $role,
        Authentication $auth
      )
    {
        parent::__construct();

        $this->user = $user;
        $this->profile = $profile;
        $this->permissions = $permissions;
        $this->role = $role;
        $this->auth = $auth;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //$userFilter = Auth::user()->isAdmin() ? AdminFilter::class : BasicUserFilter::class;
        if(empty($request->all()))
        {
          $res['order']['by'] = 'id';
          $res['order']['sort'] = 'asc';
          $request->merge($res);
        }

        $users = Profile::filter($request->all())
                        ->join('users', 'user_id', '=', 'users.id')
                        ->whereHas('user', function($query) use ($request) {
                            if(!empty($request->deleted) && $request->deleted == 1)
                                $query->onlyTrashed();
                        })
                        ->paginateFilter(config('wecore.pagination.limit'));

        $request->flash();

        return view('user::admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = $this->role->all();

        $gruppi = Gruppo::pluck('nome', 'id')
                        ->toArray();

        $utenti = [''] + User::all()->pluck('full_name', 'id')->toArray();

        $selected_groups_ldap = ['Dipendenti', 'IntranetUsers', 'Wi-Fi-Users'];

        return view('user::admin.users.create', compact('roles' , 'gruppi', 'utenti', 'selected_groups_ldap'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateProfileRequest $request
     * @return Response
     */
    public function store(CreateProfileRequest $request)
    {
        // Validate
        $rules = Profile::getRules();
        $this->validate($request, $rules);

        // Permissions
        $data = $this->mergeRequestWithPermissions($request);

        // User with Roles
        $data['password'] = (!empty($data['password'])) ? $data['password'] : '!Dh4'.Str::random(8);
        $data['permissions'] = !empty($data['permissions']) ? $data['permissions'] : null;
        $user = $this->user->createWithRoles($data, $request->roles, true);

        // Profile
        $data['profile']['user_id'] = $user->id;
        $profile = Profile::create($data['profile']);

        // Groups
        $utente = User::findOrFail($user->id);
        if(!empty($data['gruppi']))
            $utente->gruppi()->sync($data['gruppi']);
        else
            $utente->gruppi()->sync([]);


        if(config('ldap.active'))
        {
          // Add user LDAP
          // Construct a new User model instance.
          $user_ldap = Adldap::make()->user();

          // Create the users distinguished name.
          // We're adding an OU onto the users base DN to have it be saved in the specified OU.
          //$dn = $user_ldap->getDnBuilder()->addOu('WeCom'); // Built DN will be: "CN=John Doe,OU=Users,DC=acme,DC=org";

          // Set the users DN, account name.
          $user_ldap->setDn('cn=' . $utente->full_name . ',' . config('ldap.connections.default.ou'));
          $user_ldap->setAccountName($profile->username);
          $user_ldap->setCommonName($utente->full_name);
          $user_ldap->setFirstName($utente->first_name);
          $user_ldap->setLastName($utente->last_name);
          $user_ldap->setEmail($utente->email);

          // Set the users password.
          // NOTE: This password must obey your AD servers password requirements
          // (including password history, length, special characters etc.)
          // otherwise saving will fail and you will receive an
          // "LDAP Server is unwilling to perform" message.
          $user_ldap->setPassword($data['password']);

          // Get a new account control object for the user.
          $ac = $user_ldap->getUserAccountControlObject();

          // // Mark the account as enabled (normal).
          $ac->accountIsNormal();

          // // Set the account control on the user and save it.
          $user_ldap->setUserAccountControl($ac);

          // Save the user.
          $user_ldap->save();
          // $user_ldap->setUserAccountControl(66048)->save();

          // Gruppi LDAP
          $current_groups_list = gruppi_ldap_user($user->id);
          $res_gruppi_ldap = $request->gruppi_ldap;

          // Add Group LDAP
          if(!empty($res_gruppi_ldap))
          {
            foreach($res_gruppi_ldap as $key => $group)
            {
              $gruppo_ldap = Adldap::search()->groups()->find($group);

              if(!in_array($gruppo_ldap->getCommonName(), $current_groups_list))
              {
                if($gruppo_ldap->addMember($user_ldap))
                   $data['gruppi_ldap']['add'][$gruppo_ldap->getCommonName()] = 'ok';
                else
                   $data['gruppi_ldap']['add'][$gruppo_ldap->getCommonName()] = 'ko';
              }
            }
          }
        }

        // Log
        activity(session('azienda'))
            ->performedOn($user)
            ->withProperties($data)
            ->log('created');

        // Email
        $senders = $utente->email;
        $oggetto = 'NUOVO UTENTE - ' . $utente->first_name . ' ' . $utente->last_name;
        $testo = '<p>Ciao ' . $utente->first_name . ' ' . $utente->last_name . ',</p>'
                . 'il tuo account per accedere all\'intranet ' . (!empty($utente->profile->azienda) ? $utente->profile->azienda : '') . ' è stato creato.<br><br>'
                . 'Di seguito le credenziali di accesso:<br>'
                . 'Email: <strong>' . $utente->email . '</strong><br>'
                . 'Password: <strong>' . $data['password'] . '</strong><br>'
                . '<hr><br>'
                . 'Puoi eseguire l\'accesso dal seguente link: <a href="' . url('/') . '">' . url('/') . '</a>';

        mail_send($senders, $oggetto, $testo, null, $utente->profile->azienda);

        return redirect()->route('admin.user.user.index')
            ->withSuccess(trans('user::messages.user created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->user->find($id);

        $roles = $this->role->all();

        $gruppi = [];

        foreach(Area::all() as $k => $area)
        {
          foreach(Gruppo::where('area_id',$area->id)->get() as $kk => $attivita)
          {
            $gruppi[$area->titolo][$attivita->id] = $attivita->nome;
          }
        }

        $utenti = [''] + User::all()->pluck('full_name', 'id')->toArray();

        $selected_groups_ldap = [];
        $user_ldap = '';
        if(config('ldap.active')) // LDAP
        {
          $selected_groups_ldap = gruppi_ldap_user($id);

          $user_ldap = Adldap::search()->users()->find(get_profile_user($user->id)->username);
        }

        return view('user::admin.users.edit', compact('user', 'roles' ,'gruppi', 'utenti', 'user_ldap', 'selected_groups_ldap'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  UpdateProfileRequest $request
     * @return Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $user_id = $request['_id'];
        $profile = Profile::where('user_id', $user_id)->first();

        $rules = Profile::getRules($user_id, $profile->id);
        $this->validate($request, $rules);

        $data = $this->mergeRequestWithPermissions($request);

        $this->user->updateAndSyncRoles($user_id, $data, $request->roles);

        if(empty($data['profile']['partner']))
            $data['profile']['partner'] = [];

		if(empty($request->approvatori_fpm))
		{
			 $data['profile']['approvatori_fpm'] = [];
		}
		else 
		{
			$data['profile']['approvatori_fpm'] = $request->approvatori_fpm;
		}
           

		if(empty($request->approvatori_rimborsi))
		{
			$data['profile']['approvatori_rimborsi'] = [];
		}
		else
		{
			$data['profile']['approvatori_rimborsi'] = $request->approvatori_rimborsi;
		}
           

		if(empty($request->visualizzatori))
		{
			$data['profile']['visualizzatori'] = [];
		}
        else 
		{
			$data['profile']['visualizzatori']  = $request->visualizzatori;
		}

        Profile::updateOrCreate(
            ['user_id' => $user_id],
            $data['profile']
        );

        $utente = User::findOrFail($user_id);

        // Attività 
        if(!empty($data['gruppi']))
            $utente->gruppi()->sync($data['gruppi']);
        else
            $utente->gruppi()->sync([]);

        // LDAP
        if(config('ldap.active'))
        {
          $user_ldap = Adldap::search()->users()
                              ->where('sAMAccountName', $profile->username)
                              //->where('memberOf', config('ldap.connections.default.memberOf'))
                              ->first();

          // Password
          if(!empty($request->password))
          {
            $user_ldap->setPassword($request->password);
            $user_ldap->save();
          }

          // Active
          if($request->activated)
            $user_ldap->setUserAccountControl(66048)->save();
          else
            $user_ldap->setUserAccountControl(514)->save();

          // Gruppi LDAP
          $current_groups = gruppi_ldap_user($user_id, true);
          $current_groups_list = gruppi_ldap_user($user_id);
          $res_gruppi_ldap = $request->gruppi_ldap;

          // Remove group LDAP
          foreach($current_groups as $key => $group)
          {
             // Check if current group is not in remote user groups, then remove it
             if(!in_array($group->getCommonName(), $res_gruppi_ldap) && $group->getCommonName() != 'Domain Users')
             {
                if($group->removeMember($user_ldap))
                   $data['gruppi_ldap']['remove'][$group->getCommonName()] = 'ok';
                else
                   $data['gruppi_ldap']['remove'][$group->getCommonName()] = 'ko';
             }
          }

          // Add Group LDAP
          if(!empty($res_gruppi_ldap))
          {
            foreach($res_gruppi_ldap as $key => $group)
            {
              $gruppo_ldap = Adldap::search()->groups()->find($group);

              if(!in_array($gruppo_ldap->getCommonName(), $current_groups_list))
              {
                if($gruppo_ldap->addMember($user_ldap))
                   $data['gruppi_ldap']['add'][$gruppo_ldap->getCommonName()] = 'ok';
                else
                   $data['gruppi_ldap']['add'][$gruppo_ldap->getCommonName()] = 'ko';
              }
            }
          }
        }

        // Log
        activity(session('azienda'))
            ->performedOn($utente)
            ->withProperties($data)
            ->log('updated');

        if ($request->get('button') === 'index')
            return redirect()->route('admin.user.user.index')
                ->withSuccess(trans('user::messages.user updated'));

        return redirect()->back()
            ->withSuccess(trans('user::messages.user updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Profile $profile
     * @return Response
     */
    public function destroy(Profile $profile)
    {
        $this->profile->destroy($profile);

        if(config('ldap.active'))
        {
          $user_ldap = Adldap::search()->users()
                              ->where('sAMAccountName', get_profile_user($profile->id)->username)
                              ->first();

          // Disable
          $user_ldap->setUserAccountControl(514)->save();
        }

        // Log
        activity(session('azienda'))
            ->performedOn($profile)
            ->withProperties(json_encode($profile))
            ->log('destroyed');

        return redirect()->route('admin.user.user.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('profile::profiles.title.profiles')]));
    }

    public function restore($id)
    {
        User::onlyTrashed()
            ->where('id', $id)
            ->restore();

        $utente = User::findOrFail($id);

        if(config('ldap.active'))
        {
          $user_ldap = Adldap::search()->users()
                              ->where('sAMAccountName', get_profile_user($id)->username)
                              ->first();

          // Enable
          $user_ldap->setUserAccountControl(66048)->save();
        }

        // Log
        activity(session('azienda'))
            ->performedOn($utente)
            ->withProperties(json_encode($utente))
            ->log('restored');

        return redirect()->route('admin.user.user.index')
            ->withSuccess('Utente ripristinato con successo.');
    }

    public function sendResetPassword($user, Authentication $auth)
    {
        $user = $this->user->find($user);
        $code = $auth->createReminderCode($user);

        event(new UserHasBegunResetProcess($user, $code));

        return redirect()->route('admin.profile.profile.edit', $user->id)
            ->withSuccess(trans('user::auth.reset password email was sent'));
    }

    // Switch azienda
    public function switchAzienda($azienda)
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        set_azienda($azienda);

        return back()->withInput();
    }

    // Activation and reset password users
    public function activeResetPwdAll()
    {
      exit('Disattivata');
      if(config('ldap.active'))
      {
        $users = User::all();
        //$users = User::where('id', 63)->get();

        foreach ($users as $key => $u)
        {
            $profile = get_profile_user($u->id);

            // User ldap
            // $user_ldap = Adldap::search()->users()
            //                     ->where('sAMAccountName', $profile->username)
            //                     ->first();
            //
            // if(empty($user_ldap))
            // {
            //   // Create user LDAP
            //   $user_ldap = Adldap::make()->user();
            //
            //   $user_ldap->setDn('cn=' . $u->full_name . ',' . config('ldap.connections.default.ou'));
            //   $user_ldap->setAccountName($profile->username);
            //   $user_ldap->setCommonName($u->full_name);
            //   $user_ldap->setFirstName($u->first_name);
            //   $user_ldap->setLastName($u->last_name);
            //   $user_ldap->setEmail($u->email);
            //   $user_ldap->setPassword(Str::random(8) . '!');
            //
            //   $user_ldap->save();
            //   $user_ldap->setUserAccountControl(66048)->save();
            //
            //   if(!empty($user_ldap))
            //   {
            //     $current_groups_list = ['Dipendenti', 'IntranetUsers', 'Wi-Fi-Users'];
            //
            //     foreach($current_groups_list as $key => $group)
            //     {
            //       $gruppo_ldap = Adldap::search()->groups()->find($group);
            //       $gruppo_ldap->addMember($user_ldap);
            //     }
            //   }
            // }

            // Email sendResetPassword
            $this->sendResetPassword($u->id, $this->auth);

            sleep(1);
        }

        echo 'ok';
      }
      else
      {
        exit('Disattivata');
        $users = User::all();
        // $users = User::whereNotIn('id', [1, 2, 3, 5, 9, 10, 21, 24, 29, 53, 56, 63, 79, 82, 87])
        //                 ->get();

        foreach ($users as $key => $u)
        {
            $user = \Sentinel::findById($u->id);

            $activation = \Activation::create($user);
            \Activation::complete($user, $activation->code);

            // Email sendResetPassword
            $this->sendResetPassword($user->id, $this->auth);

            sleep(1);
        }
      }
    }
}
