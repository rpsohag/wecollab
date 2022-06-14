<?php
return [
   /*
   |--------------------------------------------------------------------------
   | The prefix that'll be used for the administration
   |--------------------------------------------------------------------------
   */
    'admin-prefix' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Location where your themes are located
    |--------------------------------------------------------------------------
    */
    'themes_path' => base_path() . '/Themes',

    /*
    |--------------------------------------------------------------------------
    | Which administration theme to use for the back end interface
    |--------------------------------------------------------------------------
    */
    'admin-theme' => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | AdminLTE skin
    |--------------------------------------------------------------------------
    | You can customize the AdminLTE colors with this setting. The following
    | colors are available for you to use: skin-blue, skin-green,
    | skin-black, skin-purple, skin-red and skin-yellow.
    */
    'skin' => 'skin-red',

   /*
   |--------------------------------------------------------------------------
   | WYSIWYG Backend Editor
   |--------------------------------------------------------------------------
   | Define which editor you would like to use for the backend wysiwygs.
   | These classes are event handlers, listening to EditorIsRendering
   | you can define your own handlers and use them here
   | Options:
   | - \Modules\Core\Events\Handlers\LoadCkEditor::class
   | - \Modules\Core\Events\Handlers\LoadSimpleMde::class
   */
   'wysiwyg-handler' => \Modules\Core\Events\Handlers\LoadCkEditor::class,
    /*
    |--------------------------------------------------------------------------
    | Custom CKeditor configuration file
    |--------------------------------------------------------------------------
    | Define a custom CKeditor configuration file to instead of the one
    | provided by default. This is useful if you wish to customise
    | the toolbar and other possible options.
    */
    'ckeditor-config-file-path' => '',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    | You can customise the Middleware that should be loaded.
    | The localizationRedirect middleware is automatically loaded for both
    | Backend and Frontend routes.
    */
    'middleware' => [
       'backend' => [
           'auth.admin',
       ],
       'frontend' => [
       ],
       'api' => [
           'api',
       ],
    ],

   /*
   |--------------------------------------------------------------------------
   | Define which assets will be available through the asset manager
   |--------------------------------------------------------------------------
   | These assets are registered on the asset manager
   */
    'admin-assets' => [
        // Css
        'bootstrap.css' => ['theme' => 'vendor/bootstrap/dist/css/bootstrap.min.css'],
        // 'font-awesome.css' => ['theme' => 'vendor/font-awesome/css/font-awesome.min.css'],
        'font-awesome.css' => ['theme' => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'],
        'alertify.core.css' => ['theme' => 'css/vendor/alertify/alertify.core.css'],
        'alertify.default.css' => ['theme' => 'css/vendor/alertify/alertify.default.css'],
        'dataTables.bootstrap.css' => ['theme' => 'vendor/datatables.net-bs/css/dataTables.bootstrap.min.css'],
        'icheck.blue.css' => ['theme' => 'vendor/iCheck/skins/flat/blue.css'],
        'AdminLTE.css' => ['theme' => 'vendor/admin-lte/dist/css/AdminLTE.css'],
        'AdminLTE.all.skins.css' => ['theme' => 'vendor/admin-lte/dist/css/skins/_all-skins.min.css'],
        'style.css' => ['theme' => 'css/style.css'],
        //'gridstack.css' => ['module' => 'dashboard:vendor/gridstack/dist/gridstack.min.css'],
        'gridstack.css' => ['module' => 'dashboard:gridstack/gridstack.min.css'],
        'daterangepicker.css' => ['theme' => 'vendor/admin-lte/plugins/daterangepicker/daterangepicker-bs3.css'],
        'bootstrap-datetimepicker.min.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'],
        'selectize.css' => ['module' => 'core:vendor/selectize/dist/css/selectize.css'],
        'selectize-default.css' => ['module' => 'core:vendor/selectize/dist/css/selectize.default.css'],
        'animate.css' => ['theme' => 'vendor/animate.css/animate.min.css'],
        'pace.css' => ['theme' => 'vendor/admin-lte/plugins/pace/pace.min.css'],
        'simplemde.css' => ['theme' => 'vendor/simplemde/dist/simplemde.min.css'],
        'select2.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css'],
        'bootstrap-colorpicker.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css'],
        'bootstrap-slider.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/css/bootstrap-slider.min.css'],
        'fancybox.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css'],
        'jquery.steps' => ['theme' => 'css/vendor/jquery.steps.css'],
        'dropzone.min.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css'],
        'fileinput.css' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.9/css/fileinput.min.css'],
        'datarangepicker.css' => ['theme' => 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css'],
        // Javascript
        'bootstrap.js' => ['theme' => 'vendor/bootstrap/dist/js/bootstrap.min.js'],
        'mousetrap.js' => ['theme' => 'js/vendor/mousetrap.min.js'],
        'alertify.js' => ['theme' => 'js/vendor/alertify/alertify.js'],
        'icheck.js' => ['theme' => 'vendor/iCheck/icheck.min.js'],
        'jquery.dataTables.js' => ['theme' => 'vendor/datatables.net/js/jquery.dataTables.min.js'],
        'dataTables.bootstrap.js' => ['theme' => 'vendor/datatables.net-bs/js/dataTables.bootstrap.min.js'],
        'jquery.slug.js' => ['theme' => 'js/vendor/jquery.slug.js'],
        'app.js' => ['theme' => 'vendor/admin-lte/dist/js/app.js'],
        'keypressAction.js' => ['module' => 'core:js/keypressAction.js'],
        'ckeditor.js' => ['theme' => 'js/vendor/ckeditor/ckeditor.js'],
        'lodash.js' => ['module' => 'dashboard:vendor/lodash/lodash.min.js'],
        'jquery-ui-core.js' => ['module' => 'dashboard:vendor/jquery-ui/ui/minified/core.min.js'],
        'jquery-ui-widget.js' => ['module' => 'dashboard:vendor/jquery-ui/ui/minified/widget.min.js'],
        'jquery-ui-mouse.js' => ['module' => 'dashboard:vendor/jquery-ui/ui/minified/mouse.min.js'],
        'jquery-ui-draggable.js' => ['module' => 'dashboard:vendor/jquery-ui/ui/minified/draggable.min.js'],
        'jquery-ui-resizable.js' => ['module' => 'dashboard:vendor/jquery-ui/ui/minified/resizable.min.js'],
        //'gridstack.js' => ['module' => 'dashboard:vendor/gridstack/dist/gridstack.min.js'],
        'gridstack.js' => ['module' => 'dashboard:gridstack/gridstack.min.js'],
        'daterangepicker.js' => ['theme' => 'vendor/admin-lte/plugins/daterangepicker/daterangepicker.js'],
        'selectize.js' => ['module' => 'core:vendor/selectize/dist/js/standalone/selectize.min.js'],
        // 'bootstrap-datepicker.min.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js'],
        // 'bootstrap-datepicker.it.min.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.it.min.js'],
        'bootstrap-datetimepicker.min.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'],
        'adminlte.min.js' => ['theme' => 'js/adminlte.min.js'],
        'jquery-cookie' => ['theme' => 'https://cdn.jsdelivr.net/npm/jquery.cookie@1.4.1/jquery.cookie.min.js'],
        //'adminlte.js' => ['theme' => 'js/asgardcms.js'],
        'main.js' => ['theme' => 'js/main.js'],
        'chart.js' => ['theme' => 'vendor/admin-lte/plugins/chartjs/Chart.js'],
        'pace.js' => ['theme' => 'vendor/admin-lte/plugins/pace/pace.min.js'],
        // 'moment.js' => ['theme' => 'vendor/admin-lte/plugins/daterangepicker/moment.min.js'],
        'moment.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment-with-locales.min.js'],
        'clipboard.js' => ['theme' => 'vendor/clipboard/dist/clipboard.min.js'],
        'simplemde.js' => ['theme' => 'vendor/simplemde/dist/simplemde.min.js'],
        'select2.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js'],
        'select2it.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/i18n/it.js'],
        'bootstrap-colorpicker.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js'],
        'bootstrap-slider.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/bootstrap-slider.min.js'],
        'jquery-validate.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js'],
        'jquery-validate-it.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/localization/messages_it.js'],
        'tinymce.js' => ['theme' => 'js/vendor/tinymce/tinymce.min.js'],
        'fancybox.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js'],
        'highcharts' => ['theme' => 'https://code.highcharts.com/highcharts.js'],
        'highcharts-data' => ['theme' => 'https://code.highcharts.com/modules/data.js'],
        'highcharts-exporting' => ['theme' => 'https://code.highcharts.com/modules/exporting.js'],
        'highcharts-export-data' => ['theme' => 'https://code.highcharts.com/modules/export-data.js'],
        'jquery.steps.min' => ['theme' => 'js/vendor/jquery.steps.min.js'],
        'dropzone.min.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js'],
        'fileinput.js' => ['theme' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.9/js/fileinput.min.js'],
        'datarangepicker.js' => ['theme' => 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Define which default assets will always be included in your pages
    | through the asset pipeline
    |--------------------------------------------------------------------------
    */
    'admin-required-assets' => [
        'css' => [
            'bootstrap.css',
            'font-awesome.css',
            'alertify.core.css',
            'alertify.default.css',
            'dataTables.bootstrap.css',
            'icheck.blue.css',
            'AdminLTE.css',
            'AdminLTE.all.skins.css',
            'animate.css',
            'pace.css',
            'selectize-default.css',
            'select2.css',
            'bootstrap-colorpicker.css',
            'bootstrap-slider.css',
            'bootstrap-datetimepicker.min.css',
            'fancybox.css',
            'jquery.steps',
            //'dropzone.min.css',
            'fileinput.css',
            'style.css',
            'datarangepicker.css',
        ],
        'js' => [
            'bootstrap.js',
            'select2.js',
            'select2it.js',
            'mousetrap.js',
            'alertify.js',
            'icheck.js',
            'jquery.dataTables.js',
            'dataTables.bootstrap.js',
            'jquery.slug.js',
            'keypressAction.js',
            'app.js',
            'pace.js',
            'selectize.js',
            'moment.js',
            'bootstrap-datetimepicker.min.js',
            // 'bootstrap-datepicker.min.js',
            // 'bootstrap-datepicker.it.min.js',
            'jquery-cookie',
            'bootstrap-colorpicker.js',
            'bootstrap-slider.js',
            'jquery-validate.js',
            'jquery-validate-it.js',
            'tinymce.js',
            'fancybox.js',
            'highcharts',
            'highcharts-data',
            'highcharts-exporting',
            'highcharts-export-data',
            'jquery.steps.min',
            //'dropzone.min.js',
            'fileinput.js',
            //'adminlte.min.js',
            //'adminlte.js',
            'main.js',
            'datarangepicker.js',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable module view overrides at theme locations
    |--------------------------------------------------------------------------
    */
    'enable-theme-overrides' => false,

    /*
    |--------------------------------------------------------------------------
    | Check if asgard was installed
    |--------------------------------------------------------------------------
    */
    'is_installed' => env('INSTALLED', true),
];
