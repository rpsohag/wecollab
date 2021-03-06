<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdef57093af388c87ba65fbf13ccad092
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Wecore\\' => 15,
            'Modules\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Wecore\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
            1 => __DIR__ . '/../..' . '/',
        ),
        'Modules\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Modules',
        ),
    );

    public static $classMap = array (
        'Modules\\Wecore\\Database\\Seeders\\WecoreDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/WecoreDatabaseSeeder.php',
        'Modules\\Wecore\\Entities\\Core' => __DIR__ . '/../..' . '/Entities/Core.php',
        'Modules\\Wecore\\Entities\\CoreTranslation' => __DIR__ . '/../..' . '/Entities/CoreTranslation.php',
        'Modules\\Wecore\\Entities\\Meta' => __DIR__ . '/../..' . '/Entities/Meta.php',
        'Modules\\Wecore\\Entities\\Metagable' => __DIR__ . '/../..' . '/Entities/Metagable.php',
        'Modules\\Wecore\\Events\\Handlers\\RegisterWecoreSidebar' => __DIR__ . '/../..' . '/Events/Handlers/RegisterWecoreSidebar.php',
        'Modules\\Wecore\\Http\\Controllers\\Admin\\CoreController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/CoreController.php',
        'Modules\\Wecore\\Http\\Controllers\\WecoreController' => __DIR__ . '/../..' . '/Http/Controllers/WecoreController.php',
        'Modules\\Wecore\\Http\\Requests\\CreateCoreRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateCoreRequest.php',
        'Modules\\Wecore\\Http\\Requests\\UpdateCoreRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateCoreRequest.php',
        'Modules\\Wecore\\Jobs\\Holidays' => __DIR__ . '/../..' . '/Jobs/Holidays.php',
        'Modules\\Wecore\\Mail\\MailBase' => __DIR__ . '/../..' . '/Mail/MailBase.php',
        'Modules\\Wecore\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\Wecore\\Providers\\WecoreServiceProvider' => __DIR__ . '/../..' . '/Providers/WecoreServiceProvider.php',
        'Modules\\Wecore\\Repositories\\Cache\\CacheCoreDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheCoreDecorator.php',
        'Modules\\Wecore\\Repositories\\CoreRepository' => __DIR__ . '/../..' . '/Repositories/CoreRepository.php',
        'Modules\\Wecore\\Repositories\\Eloquent\\EloquentCoreRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentCoreRepository.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdef57093af388c87ba65fbf13ccad092::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdef57093af388c87ba65fbf13ccad092::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdef57093af388c87ba65fbf13ccad092::$classMap;

        }, null, ClassLoader::class);
    }
}
