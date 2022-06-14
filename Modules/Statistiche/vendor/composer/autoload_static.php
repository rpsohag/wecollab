<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit59fe65ae7f9af41c9841cc2cbb75960c
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Statistiche\\' => 20,
            'Modules\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Statistiche\\' => 
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
        'Modules\\Statistiche\\Database\\Seeders\\StatisticheDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/StatisticheDatabaseSeeder.php',
        'Modules\\Statistiche\\Entities\\Statistica' => __DIR__ . '/../..' . '/Entities/Statistica.php',
        'Modules\\Statistiche\\Entities\\ViewRichiesteIntervento' => __DIR__ . '/../..' . '/Entities/ViewRichiesteIntervento.php',
        'Modules\\Statistiche\\Events\\Handlers\\RegisterstatisticheSidebar' => __DIR__ . '/../..' . '/Events/Handlers/RegisterStatisticheSidebar.php',
        'Modules\\Statistiche\\Http\\Controllers\\Admin\\StatisticaController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/StatisticaController.php',
        'Modules\\Statistiche\\Http\\Requests\\CreateStatisticaRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateStatisticaRequest.php',
        'Modules\\Statistiche\\Http\\Requests\\UpdateStatisticaRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateStatisticaRequest.php',
        'Modules\\Statistiche\\Jobs\\WeeklyReport' => __DIR__ . '/../..' . '/Jobs/WeeklyReport.php',
        'Modules\\Statistiche\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\Statistiche\\Providers\\StatisticheServiceProvider' => __DIR__ . '/../..' . '/Providers/StatisticheServiceProvider.php',
        'Modules\\Statistiche\\Repositories\\Cache\\CacheStatisticaDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheStatisticaDecorator.php',
        'Modules\\Statistiche\\Repositories\\Eloquent\\EloquentStatisticaRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentStatisticaRepository.php',
        'Modules\\Statistiche\\Repositories\\StatisticaRepository' => __DIR__ . '/../..' . '/Repositories/StatisticaRepository.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit59fe65ae7f9af41c9841cc2cbb75960c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit59fe65ae7f9af41c9841cc2cbb75960c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit59fe65ae7f9af41c9841cc2cbb75960c::$classMap;

        }, null, ClassLoader::class);
    }
}
