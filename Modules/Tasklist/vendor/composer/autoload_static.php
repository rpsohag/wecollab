<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit704ce8e68e006a0a4aaf5dce30792416
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Tasklist\\' => 17,
            'Modules\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Tasklist\\' => 
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
        'Modules\\Tasklist\\Database\\Seeders\\TasklistDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/TasklistDatabaseSeeder.php',
        'Modules\\Tasklist\\Entities\\Attivita' => __DIR__ . '/../..' . '/Entities/Attivita.php',
        'Modules\\Tasklist\\Entities\\AttivitaVoce' => __DIR__ . '/../..' . '/Entities/AttivitaVoce.php',
        'Modules\\Tasklist\\Entities\\Rinnovo' => __DIR__ . '/../..' . '/Entities/Rinnovo.php',
        'Modules\\Tasklist\\Entities\\RinnovoNotifica' => __DIR__ . '/../..' . '/Entities/RinnovoNotifica.php',
        'Modules\\Tasklist\\Entities\\Timesheet' => __DIR__ . '/../..' . '/Entities/Timesheet.php',
        'Modules\\Tasklist\\Events\\Handlers\\RegisterTasklistSidebar' => __DIR__ . '/../..' . '/Events/Handlers/RegisterTasklistSidebar.php',
        'Modules\\Tasklist\\Http\\Controllers\\Admin\\AttivitaController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/AttivitaController.php',
        'Modules\\Tasklist\\Http\\Controllers\\Admin\\AttivitaVociController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/AttivitaVociController.php',
        'Modules\\Tasklist\\Http\\Controllers\\Admin\\RinnovoController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/RinnovoController.php',
        'Modules\\Tasklist\\Http\\Controllers\\Admin\\TimesheetController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/TimesheetController.php',
        'Modules\\Tasklist\\Http\\Requests\\CreateAttivitaRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateAttivitaRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\CreateAttivitaVociRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateAttivitaVociRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\CreateRinnovoRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateRinnovoRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\CreateTimesheetRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateTimesheetRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\UpdateAttivitaRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateAttivitaRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\UpdateAttivitaVociRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateAttivitaVociRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\UpdateRinnovoRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateRinnovoRequest.php',
        'Modules\\Tasklist\\Http\\Requests\\UpdateTimesheetRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateTimesheetRequest.php',
        'Modules\\Tasklist\\Jobs\\Rinnovi' => __DIR__ . '/../..' . '/Jobs/Rinnovi.php',
        'Modules\\Tasklist\\Jobs\\Timesheets' => __DIR__ . '/../..' . '/Jobs/Timesheets.php',
        'Modules\\Tasklist\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\Tasklist\\Providers\\TasklistServiceProvider' => __DIR__ . '/../..' . '/Providers/TasklistServiceProvider.php',
        'Modules\\Tasklist\\Repositories\\AttivitaRepository' => __DIR__ . '/../..' . '/Repositories/AttivitaRepository.php',
        'Modules\\Tasklist\\Repositories\\AttivitaVociRepository' => __DIR__ . '/../..' . '/Repositories/AttivitaVociRepository.php',
        'Modules\\Tasklist\\Repositories\\Cache\\CacheAttivitaDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheAttivitaDecorator.php',
        'Modules\\Tasklist\\Repositories\\Cache\\CacheAttivitaVociDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheAttivitaVociDecorator.php',
        'Modules\\Tasklist\\Repositories\\Cache\\CacheRinnovoDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheRinnovoDecorator.php',
        'Modules\\Tasklist\\Repositories\\Cache\\CacheTimesheetDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheTimesheetDecorator.php',
        'Modules\\Tasklist\\Repositories\\Eloquent\\EloquentAttivitaRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentAttivitaRepository.php',
        'Modules\\Tasklist\\Repositories\\Eloquent\\EloquentAttivitaVociRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentAttivitaVociRepository.php',
        'Modules\\Tasklist\\Repositories\\Eloquent\\EloquentRinnovoRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentRinnovoRepository.php',
        'Modules\\Tasklist\\Repositories\\Eloquent\\EloquentTimesheetRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentTimesheetRepository.php',
        'Modules\\Tasklist\\Repositories\\RinnovoRepository' => __DIR__ . '/../..' . '/Repositories/RinnovoRepository.php',
        'Modules\\Tasklist\\Repositories\\TimesheetRepository' => __DIR__ . '/../..' . '/Repositories/TimesheetRepository.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit704ce8e68e006a0a4aaf5dce30792416::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit704ce8e68e006a0a4aaf5dce30792416::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit704ce8e68e006a0a4aaf5dce30792416::$classMap;

        }, null, ClassLoader::class);
    }
}
