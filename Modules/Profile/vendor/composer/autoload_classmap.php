<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Modules\\Profile\\Database\\Seeders\\ProfileDatabaseSeeder' => $baseDir . '/Database/Seeders/ProfileDatabaseSeeder.php',
    'Modules\\Profile\\Entities\\Area' => $baseDir . '/Entities/Area.php',
    'Modules\\Profile\\Entities\\FiguraProfessionale' => $baseDir . '/Entities/FiguraProfessionale.php',
    'Modules\\Profile\\Entities\\FiguraProfessionaleTranslation' => $baseDir . '/Entities/FiguraProfessionaleTranslation.php',
    'Modules\\Profile\\Entities\\Gruppo' => $baseDir . '/Entities/Gruppo.php',
    'Modules\\Profile\\Entities\\GruppoTranslation' => $baseDir . '/Entities/GruppoTranslation.php',
    'Modules\\Profile\\Entities\\Procedura' => $baseDir . '/Entities/Procedura.php',
    'Modules\\Profile\\Entities\\Profile' => $baseDir . '/Entities/Profile.php',
    'Modules\\Profile\\Entities\\ProfileTranslation' => $baseDir . '/Entities/ProfileTranslation.php',
    'Modules\\Profile\\Entities\\Utente' => $baseDir . '/Entities/Utente.php',
    'Modules\\Profile\\Events\\Handlers\\RegisterProfileSidebar' => $baseDir . '/Events/Handlers/RegisterProfileSidebar.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\Account\\ProfileController' => $baseDir . '/Http/Controllers/Admin/Account/ProfileController.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\AreaController' => $baseDir . '/Http/Controllers/Admin/AreaController.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\FiguraProfessionaleController' => $baseDir . '/Http/Controllers/Admin/FiguraProfessionaleController.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\GruppoController' => $baseDir . '/Http/Controllers/Admin/GruppoController.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\ProceduraController' => $baseDir . '/Http/Controllers/Admin/ProceduraController.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\ProfileController' => $baseDir . '/Http/Controllers/Admin/ProfileController.php',
    'Modules\\Profile\\Http\\Controllers\\Admin\\RolesController' => $baseDir . '/Http/Controllers/Admin/RolesController.php',
    'Modules\\Profile\\Http\\Requests\\CreateFiguraProfessionaleRequest' => $baseDir . '/Http/Requests/CreateFiguraProfessionaleRequest.php',
    'Modules\\Profile\\Http\\Requests\\CreateGruppoRequest' => $baseDir . '/Http/Requests/CreateGruppoRequest.php',
    'Modules\\Profile\\Http\\Requests\\CreateProfileRequest' => $baseDir . '/Http/Requests/CreateProfileRequest.php',
    'Modules\\Profile\\Http\\Requests\\UpdateFiguraProfessionaleRequest' => $baseDir . '/Http/Requests/UpdateFiguraProfessionaleRequest.php',
    'Modules\\Profile\\Http\\Requests\\UpdateGruppoRequest' => $baseDir . '/Http/Requests/UpdateGruppoRequest.php',
    'Modules\\Profile\\Http\\Requests\\UpdateProfileRequest' => $baseDir . '/Http/Requests/UpdateProfileRequest.php',
    'Modules\\Profile\\Providers\\ProfileServiceProvider' => $baseDir . '/Providers/ProfileServiceProvider.php',
    'Modules\\Profile\\Providers\\RouteServiceProvider' => $baseDir . '/Providers/RouteServiceProvider.php',
    'Modules\\Profile\\Repositories\\Cache\\CacheFiguraProfessionaleDecorator' => $baseDir . '/Repositories/Cache/CacheFiguraProfessionaleDecorator.php',
    'Modules\\Profile\\Repositories\\Cache\\CacheGruppoDecorator' => $baseDir . '/Repositories/Cache/CacheGruppoDecorator.php',
    'Modules\\Profile\\Repositories\\Cache\\CacheProfileDecorator' => $baseDir . '/Repositories/Cache/CacheProfileDecorator.php',
    'Modules\\Profile\\Repositories\\Eloquent\\EloquentFiguraProfessionaleRepository' => $baseDir . '/Repositories/Eloquent/EloquentFiguraProfessionaleRepository.php',
    'Modules\\Profile\\Repositories\\Eloquent\\EloquentGruppoRepository' => $baseDir . '/Repositories/Eloquent/EloquentGruppoRepository.php',
    'Modules\\Profile\\Repositories\\Eloquent\\EloquentProfileRepository' => $baseDir . '/Repositories/Eloquent/EloquentProfileRepository.php',
    'Modules\\Profile\\Repositories\\FiguraProfessionaleRepository' => $baseDir . '/Repositories/FiguraProfessionaleRepository.php',
    'Modules\\Profile\\Repositories\\GruppoRepository' => $baseDir . '/Repositories/GruppoRepository.php',
    'Modules\\Profile\\Repositories\\ProfileRepository' => $baseDir . '/Repositories/ProfileRepository.php',
);
