<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\User\Exceptions\InvalidOrExpiredResetCode;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Http\Requests\ResetCompleteRequest;
use Modules\User\Http\Requests\ResetRequest;
use Modules\User\Services\UserRegistration;
use Modules\User\Services\UserResetter;

use Modules\Profile\Entities\Profile;
use Illuminate\Support\Facades\Validator;
use Adldap\Laravel\Facades\Adldap;
use Auth;

class AuthController extends BasePublicController
{
    use DispatchesJobs;

    public function __construct()
    {
        parent::__construct();
    }

    public function getLogin()
    {
        return view('user::public.login');
    }

    public function postLogin(LoginRequest $request)
    {
        // LDAP
        if(config('ldap.active'))
        {
          $username = str_replace(['@we-com.it', '@digitconsulting.it'], '', $request->email);
          $user = Adldap::search()->users()
                                  ->where('sAMAccountName', $username)
                                  ->where('memberOf', config('ldap.connections.default.memberOf'))
                                  ->first();

          if(empty($user))
            return redirect()->back()->withInput()->withError('ATTENZIONE: username o password non corretta.');

          $login = Adldap::auth()->attempt("we-com\\$username", $request->password);

          if(!$login)
            return redirect()->back()->withInput()->withError('ATTENZIONE: username o password non corretta.');

          // LOGIN
          $remember = (bool) $request->get('remember_me', false);
          $profile = Profile::where('username', $username)->first();

          if($profile->count() == 0)
            return redirect()->back()->withInput()->withError('ATTENZIONE: username o password non corretta.');

          //$error = Auth::login($profile->user, $remember);
          $credentials = [
            'email' => $profile->user->email,
            'password' => $request->password
          ];
          //dd($credentials);
          $error = Auth::login($profile->user);

          if ($error) {
              return redirect()->back()->withInput()->withError($error);
          }

          return redirect()->intended(route(config('asgard.user.config.redirect_route_after_login')))
                  ->withSuccess(trans('user::messages.successfully logged in'));
        }
        else
        {
          // LOGIN NO LDAP
          $credentials = [
              'email' => $request->email,
              'password' => $request->password,
          ];

          $remember = (bool) $request->get('remember_me', false);

          $error = $this->auth->login($credentials, $remember);

          if ($error) {
              return redirect()->back()->withInput()->withError($error);
          }

          return redirect()->intended(route(config('asgard.user.config.redirect_route_after_login')))
                  ->withSuccess(trans('user::messages.successfully logged in'));
        }
    }

    public function getRegister()
    {
        return view('user::public.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        app(UserRegistration::class)->register($request->all());

        return redirect()->route('register')
            ->withSuccess(trans('user::messages.account created check email for activation'));
    }

    public function getLogout()
    {
        $this->auth->logout();

        return redirect()->route('login');
    }

    public function getActivate($userId, $code)
    {
        if ($this->auth->activate($userId, $code)) {
            return redirect()->route('login')
                ->withSuccess(trans('user::messages.account activated you can now login'));
        }

        return redirect()->route('register')
            ->withError(trans('user::messages.there was an error with the activation'));
    }

    public function getReset()
    {
        return view('user::public.reset.begin');
    }

    public function postReset(ResetRequest $request)
    {
        // Validate
        $rs  = Profile::getRules();
        $rules['password'] = $rs['password'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()
                ->withError('ATTENZIONE: la password inserita non è valida.');
        }

        try {
            app(UserResetter::class)->startReset($request->all());
        } catch (UserNotFoundException $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.no user found'));
        }

        return redirect()->route('reset')
            ->withSuccess(trans('user::messages.check email to reset password'));
    }

    public function getResetComplete()
    {
        return view('user::public.reset.complete');
    }

    public function postResetComplete($userId, $code, ResetCompleteRequest $request)
    {
        // Validate
        $rs  = Profile::getRules();
        $rules['password'] = $rs['password'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()
                ->withError('ATTENZIONE: la password inserita non è valida.');
        }

        try {
            $user = app(UserResetter::class)->finishReset(
                array_merge($request->all(), ['userId' => $userId, 'code' => $code])
            );

            if(config('ldap.active'))
            {
              $user_ldap = Adldap::search()->users()
                                          ->where('sAMAccountName', $user->profile->username)
                                          ->where('memberOf', config('ldap.connections.default.memberOf'))
                                          ->first();

              if(!empty($user_ldap))
              {
                $user_ldap->setPassword($request->password);
                $result = $user_ldap->save();

                if(!$result)
                  redirect()->back()->withInput()->withError('ATTENZIONE: modifica password non riuscita. Riprovare!');
              }
              else
                return redirect()->back()->withInput()
                  ->withError(trans('user::messages.user no longer exists'));
            }
        } catch (UserNotFoundException $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.user no longer exists'));
        } catch (InvalidOrExpiredResetCode $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.invalid reset code'));
        }

        return redirect()->route('login')
            ->withSuccess(trans('user::messages.password reset'));
    }
}
