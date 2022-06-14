<?php

namespace Modules\Profile\Http\Controllers\Admin\Account;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\User\Contracts\Authentication;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Repositories\UserRepository;

use Modules\Profile\Entities\Autovettura;

class AutovetturaController extends AdminBaseController
{
    private $user;

    private $auth;

    public function __construct(UserRepository $user, Authentication $auth)
    {
        parent::__construct();
        $this->user = $user;
        $this->auth = $auth;
    }

	public function create(Request $request)
    {
		$user = $this->auth->user();
		//Crea Autovettura
		$res = $request->all();
		$res['user_id'] = $user->id;
		Autovettura::create($res);

        // Log
        activity(session('azienda'))
            ->performedOn($user)
            ->withProperties($request->all())
            ->log('updated');

        return redirect()->back()->withSuccess(trans('user::messages.profile updated'));
	}

    public function update(Request $request)
    {
		$cars = $request->auto;

		foreach ($cars as $id_auto => $car)
		{
			$autovettura = Autovettura::find($id_auto);
			$autovettura->update($car);	
		}
	}

	public function delete(Autovettura $autovettura)
    {
		$auto = Autovettura::find($autovettura->id);
		$auto->delete();

        return redirect()->back()->withSuccess(trans('user::messages.profile updated'));
	}
}