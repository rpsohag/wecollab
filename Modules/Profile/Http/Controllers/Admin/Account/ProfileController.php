<?php

namespace Modules\Profile\Http\Controllers\Admin\Account;

use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\User\Contracts\Authentication;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Repositories\UserRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\Profile;
use Modules\Profile\Entities\Autovettura;
use Adldap\Laravel\Facades\Adldap;
use Modules\Profile\Http\Controllers\Admin\Account\AutovetturaController;
use Request;
use Auth;

class ProfileController extends AdminBaseController
{
    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var Authentication
     */
    private $auth;

    public function __construct(UserRepository $user, Authentication $auth)
    {
        parent::__construct();
        $this->user = $user;
        $this->auth = $auth;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit()
    {
		$user = $this->auth->user();
		$autovetture = Autovettura::where('user_id',$user->id)->get();
		$utenti = User::all();

        $notifiche = $user->notifications()->paginate(10);

        return view('user::admin.account.profile.edit',compact('autovetture','utenti','notifiche'));
    }

    public function markAsRead()
    {
        Auth::user()->unreadNotifications()->get()->markAsRead();

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  UpdateProfileRequest $request
     *
     * @return Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $this->auth->user();

		//Verifico se sto in Modifica Password o se Sono in Autovetture

		if(!empty($request->password))//Modifica Password
		{
			$rules = Profile::getRules($user->id, $user->id);
			unset($rules['profile.username']);
			$this->validate($request, $rules);

			$res = $request->all();
			unset($res['first_name']);
			unset($res['last_name']);
			unset($res['email']);

			if(config('ldap.active')) // LDAP
			{
			$user_ldap = Adldap::search()->users()
										->where('sAMAccountName', $user->profile->username)
										->where('memberOf', config('ldap.connections.default.memberOf'))
										->first();

			// $user_ldap->userpassword = $res['password'];
			$user_ldap->setPassword($res['password']);
			$result = $user_ldap->save();

			if(!$result)
				redirect()->back()->withError('ATTENZIONE: modifica password non riuscita. Riprovare!');
			}
			else// NO LDAP 
			{
				$user = $this->user->update($user, $res);
			}
		}
		else //Autovettura Update
		{
			$extraController = new AutovetturaController($this->user,$this->auth);
			$extraController->update($request);
		}

        // Log
        activity(session('azienda'))
            ->performedOn($user)
            ->withProperties($request->all())
            ->log('updated');

        return redirect()->back()
            ->withSuccess(trans('user::messages.profile updated'));
    }
}
