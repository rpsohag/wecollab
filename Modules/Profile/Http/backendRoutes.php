<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/profile'], function (Router $router) {
    $router->bind('profile', function ($id) {
        return app('Modules\Profile\Repositories\ProfileRepository')->find($id);
    });
    // $router->get('profiles', [
    //     'as' => 'admin.profile.profile.index',
    //     'uses' => 'ProfileController@index',
    //     'middleware' => 'can:profile.profiles.index'
    // ]);
    $router->get('profiles/create', [
        'as' => 'admin.profile.profile.create',
        'uses' => 'ProfileController@create',
        'middleware' => 'can:profile.profiles.create'
    ]);
    $router->post('profiles', [
        'as' => 'admin.profile.profile.store',
        'uses' => 'ProfileController@store',
        'middleware' => 'can:profile.profiles.create'
    ]);
    $router->get('profiles/{id}/edit', [
        'as' => 'admin.profile.profile.edit',
        'uses' => 'ProfileController@edit',
        'middleware' => 'can:profile.profiles.edit'
    ]);
    $router->put('profiles/{profile}', [
        'as' => 'admin.profile.profile.update',
        'uses' => 'ProfileController@update',
        'middleware' => 'can:profile.profiles.edit'
    ]);
    $router->delete('profiles/{profile}', [
        'as' => 'admin.profile.profile.destroy',
        'uses' => 'ProfileController@destroy',
        'middleware' => 'can:profile.profiles.destroy'
    ]);
    $router->get('profiles/{azienda}/switch', [
        'as' => 'admin.profile.profile.switchAzienda',
        'uses' => 'ProfileController@switchAzienda'
    ]);
    $router->get('profiles/{users}/sendResetPassword', [
        'as' => 'admin.profile.profile.sendResetPassword',
        'uses' => 'ProfileController@sendResetPassword',
        'middleware' => 'can:profile.profiles.edit',
    ]);
    $router->get('profiles/{id}/restore', [
        'as' => 'admin.profile.profile.restore',
        'uses' => 'ProfileController@restore',
        'middleware' => 'can:profile.profiles.restore'
    ]);
    // $router->get('profiles/resetall', [
    //     'as' => 'admin.profile.profile.resetall',
    //     'uses' => 'ProfileController@activeResetPwdAll',
    //     'middleware' => 'can:profile.profiles.restore'
    // ]);

    $router->bind('gruppo', function ($id) {
        return app('Modules\Profile\Repositories\GruppoRepository')->find($id);
    });
    $router->get('gruppi', [
        'as' => 'admin.profile.gruppo.index',
        'uses' => 'GruppoController@index',
        'middleware' => 'can:profile.gruppi.index'
    ]);
    $router->get('gruppi/create', [
        'as' => 'admin.profile.gruppo.create',
        'uses' => 'GruppoController@create',
        'middleware' => 'can:profile.gruppi.create'
    ]);
    $router->post('gruppi', [
        'as' => 'admin.profile.gruppo.store',
        'uses' => 'GruppoController@store',
        'middleware' => 'can:profile.gruppi.create'
    ]);
    $router->get('gruppi/{gruppo}/edit', [
        'as' => 'admin.profile.gruppo.edit',
        'uses' => 'GruppoController@edit',
        'middleware' => 'can:profile.gruppi.edit'
    ]);
    $router->put('gruppi/{gruppo}', [
        'as' => 'admin.profile.gruppo.update',
        'uses' => 'GruppoController@update',
        'middleware' => 'can:profile.gruppi.edit'
    ]);
    $router->delete('gruppi/{gruppo}', [
        'as' => 'admin.profile.gruppo.destroy',
        'uses' => 'GruppoController@destroy',
        'middleware' => 'can:profile.gruppi.destroy'
    ]);
    $router->post('gruppo/users', [
        'as' => 'admin.profile.gruppo.users',
        'uses' => 'GruppoController@users',
        //'middleware' => 'can:profile.gruppi.create'
    ]);

    $router->get('aree', [
        'as' => 'admin.profile.aree.index',
        'uses' => 'AreaController@index',
        'middleware' => 'can:profile.area.index'
    ]);
    $router->get('area/create', [
        'as' => 'admin.profile.area.create',
        'uses' => 'AreaController@create',
        'middleware' => 'can:profile.area.create'
    ]);
    $router->post('area', [
        'as' => 'admin.profile.area.store',
        'uses' => 'AreaController@store',
        'middleware' => 'can:profile.area.create'
    ]);
    $router->get('area/{area}/edit', [
        'as' => 'admin.profile.area.edit',
        'uses' => 'AreaController@edit',
        'middleware' => 'can:profile.area.edit'
    ]);
    $router->put('area/{area}', [
        'as' => 'admin.profile.area.update',
        'uses' => 'AreaController@update',
        'middleware' => 'can:profile.area.edit'
    ]);
    $router->delete('area/{area}', [
        'as' => 'admin.profile.area.destroy',
        'uses' => 'AreaController@destroy',
        'middleware' => 'can:profile.area.destroy'
    ]);

    $router->get('procedure', [
        'as' => 'admin.profile.procedure.index',
        'uses' => 'ProceduraController@index',
        'middleware' => 'can:profile.procedura.index'
    ]);
    $router->get('procedura/create', [
        'as' => 'admin.profile.procedura.create',
        'uses' => 'ProceduraController@create',
        'middleware' => 'can:profile.procedura.create'
    ]);
    $router->post('procedura', [
        'as' => 'admin.profile.procedura.store',
        'uses' => 'ProceduraController@store',
        'middleware' => 'can:profile.procedura.create'
    ]);
    $router->get('procedura/{procedura}/edit', [
        'as' => 'admin.profile.procedura.edit',
        'uses' => 'ProceduraController@edit',
        'middleware' => 'can:profile.procedura.edit'
    ]);
    $router->put('procedura/{procedura}', [
        'as' => 'admin.profile.procedura.update',
        'uses' => 'ProceduraController@update',
        'middleware' => 'can:profile.procedura.edit'
    ]);
    $router->delete('procedura/{procedura}', [
        'as' => 'admin.profile.procedura.destroy',
        'uses' => 'ProceduraController@destroy',
        'middleware' => 'can:profile.procedura.destroy'
    ]);
    $router->bind('figuraprofessionale', function ($id) {
        return app('Modules\Profile\Repositories\FiguraProfessionaleRepository')->find($id);
    });
    $router->get('figureprofessionali', [
        'as' => 'admin.profile.figuraprofessionale.index',
        'uses' => 'FiguraProfessionaleController@index',
        'middleware' => 'can:profile.figureprofessionali.index'
    ]);
    $router->get('figureprofessionali/create', [
        'as' => 'admin.profile.figuraprofessionale.create',
        'uses' => 'FiguraProfessionaleController@create',
        'middleware' => 'can:profile.figureprofessionali.create'
    ]);
    $router->post('figureprofessionali', [
        'as' => 'admin.profile.figuraprofessionale.store',
        'uses' => 'FiguraProfessionaleController@store',
        'middleware' => 'can:profile.figureprofessionali.create'
    ]);
    $router->get('figureprofessionali/{figuraprofessionale}/edit', [
        'as' => 'admin.profile.figuraprofessionale.edit',
        'uses' => 'FiguraProfessionaleController@edit',
        'middleware' => 'can:profile.figureprofessionali.edit'
    ]);
    $router->put('figureprofessionali/{figuraprofessionale}', [
        'as' => 'admin.profile.figuraprofessionale.update',
        'uses' => 'FiguraProfessionaleController@update',
        'middleware' => 'can:profile.figureprofessionali.edit'
    ]);
    $router->delete('figureprofessionali/{figuraprofessionale}', [
        'as' => 'admin.profile.figuraprofessionale.destroy',
        'uses' => 'FiguraProfessionaleController@destroy',
        'middleware' => 'can:profile.figureprofessionali.destroy'
    ]);
// append




});


// User
$router->group(['prefix' => '/user'], function (Router $router) {
    $router->get('users', [
        'as' => 'admin.user.user.index',
        'uses' => 'ProfileController@index',
        'middleware' => 'can:user.users.index'
    ]);

    $router->get('roles', [
        'as' => 'admin.user.role.index',
        'uses' => 'RolesController@index',
        'middleware' => 'can:user.roles.index',
    ]);
    // $router->get('roles/create', [
    //     'as' => 'admin.user.role.create',
    //     'uses' => 'RolesController@create',
    //     'middleware' => 'can:user.roles.create',
    // ]);
    $router->get('roles/{roles}/edit', [
        'as' => 'admin.user.role.edit',
        'uses' => 'RolesController@edit',
        'middleware' => 'can:user.roles.edit',
    ]);
});


// Account
$router->group(['prefix' => '/account'], function (Router $router) {

    $router->get('profile', [
        'as' => 'admin.account.profile.edit',
        'uses' => 'Account\ProfileController@edit',
    ]);

    $router->put('profile', [
        'as' => 'admin.account.profile.update',
        'uses' => 'Account\ProfileController@update',
    ]);

	$router->post('profile/autovetture/create', [
        'as' => 'admin.account.profile.autovetture.create',
        'uses' => 'Account\AutovetturaController@create',
        'middleware' => 'can:user.users.edit',
    ]);

	$router->get('profile/notifiche/markAsRead', [
        'as' => 'admin.account.profile.notifiche.markasread',
        'uses' => 'Account\ProfileController@markAsRead',
    ]);

	$router->any('profile/autovettura/{autovettura}/delete', [
        'as' => 'admin.account.profile.autovetture.delete',
        'uses' => 'Account\AutovetturaController@delete',
        'middleware' => 'can:user.users.edit',
    ]);

});
