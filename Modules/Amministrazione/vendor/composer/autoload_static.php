<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfd0a8e9b39933d9665313baaa0f4f8b8
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Amministrazione\\' => 24,
            'Modules\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Amministrazione\\' => 
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
        'Modules\\Amministrazione\\Database\\Seeders\\AmministrazioneDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/AmministrazioneDatabaseSeeder.php',
        'Modules\\Amministrazione\\Entities\\ClienteAmbienti' => __DIR__ . '/../..' . '/Entities/ClienteAmbienti.php',
        'Modules\\Amministrazione\\Entities\\ClienteIndirizzi' => __DIR__ . '/../..' . '/Entities/ClienteIndirizzi.php',
        'Modules\\Amministrazione\\Entities\\ClienteReferenti' => __DIR__ . '/../..' . '/Entities/ClienteReferenti.php',
        'Modules\\Amministrazione\\Entities\\ClienteReferentiTranslation' => __DIR__ . '/../..' . '/Entities/ClienteReferentiTranslation.php',
        'Modules\\Amministrazione\\Entities\\Clienti' => __DIR__ . '/../..' . '/Entities/Clienti.php',
        'Modules\\Amministrazione\\Entities\\ClientiTranslation' => __DIR__ . '/../..' . '/Entities/ClientiTranslation.php',
        'Modules\\Amministrazione\\Entities\\clientiIndirizziTranslation' => __DIR__ . '/../..' . '/Entities/ClienteIndirizziTranslation.php',
        'Modules\\Amministrazione\\Events\\Handlers\\RegisterAmministrazioneSidebar' => __DIR__ . '/../..' . '/Events/Handlers/RegisterAmministrazioneSidebar.php',
        'Modules\\Amministrazione\\Http\\Controllers\\Admin\\ClientiController' => __DIR__ . '/../..' . '/Http/Controllers/Admin/ClientiController.php',
        'Modules\\Amministrazione\\Http\\Requests\\CreateClienteReferentiRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateClienteReferentiRequest.php',
        'Modules\\Amministrazione\\Http\\Requests\\CreateClientiRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateClientiRequest.php',
        'Modules\\Amministrazione\\Http\\Requests\\CreateclientiIndirizziRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateclientiIndirizziRequest.php',
        'Modules\\Amministrazione\\Http\\Requests\\UpdateClienteReferentiRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateClienteReferentiRequest.php',
        'Modules\\Amministrazione\\Http\\Requests\\UpdateClientiRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateClientiRequest.php',
        'Modules\\Amministrazione\\Http\\Requests\\UpdateclientiIndirizziRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateclientiIndirizziRequest.php',
        'Modules\\Amministrazione\\Providers\\AmministrazioneServiceProvider' => __DIR__ . '/../..' . '/Providers/AmministrazioneServiceProvider.php',
        'Modules\\Amministrazione\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\Amministrazione\\Repositories\\Cache\\CacheClienteReferentiDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheClienteReferentiDecorator.php',
        'Modules\\Amministrazione\\Repositories\\Cache\\CacheClientiDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheClientiDecorator.php',
        'Modules\\Amministrazione\\Repositories\\Cache\\CacheClientiIndirizziDecorator' => __DIR__ . '/../..' . '/Repositories/Cache/CacheClientiIndirizziDecorator.php',
        'Modules\\Amministrazione\\Repositories\\ClienteReferentiRepository' => __DIR__ . '/../..' . '/Repositories/ClienteReferentiRepository.php',
        'Modules\\Amministrazione\\Repositories\\ClientiIndirizziRepository' => __DIR__ . '/../..' . '/Repositories/ClienteIndirizziRepository.php',
        'Modules\\Amministrazione\\Repositories\\ClientiRepository' => __DIR__ . '/../..' . '/Repositories/ClientiRepository.php',
        'Modules\\Amministrazione\\Repositories\\Eloquent\\EloquentClienteReferentiRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentClienteReferentiRepository.php',
        'Modules\\Amministrazione\\Repositories\\Eloquent\\EloquentClientiIndirizziRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentClientiIndirizziRepository.php',
        'Modules\\Amministrazione\\Repositories\\Eloquent\\EloquentClientiRepository' => __DIR__ . '/../..' . '/Repositories/Eloquent/EloquentClientiRepository.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfd0a8e9b39933d9665313baaa0f4f8b8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfd0a8e9b39933d9665313baaa0f4f8b8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfd0a8e9b39933d9665313baaa0f4f8b8::$classMap;

        }, null, ClassLoader::class);
    }
}
