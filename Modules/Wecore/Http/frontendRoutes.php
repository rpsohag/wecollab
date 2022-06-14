<?php

use Illuminate\Routing\Router;

/** @var Router $router */
if (! App::runningInConsole()) {
    $router->get('/', function () {
        return redirect('admin');
    });

    $router->get('rubrica_wecom', [
        'as' => 'admin.wecore.rubrica_wecom',
        'uses' => 'WecoreController@rubricaWecom',
     ]);
}
