@php
    $currentUser->profile = get_profile_user($currentUser->id);
    $azienda = get_azienda_dati();
@endphp

<!DOCTYPE html>
<html>
<head>
    <base href="{{ URL::asset('/') }}" />
    <meta charset="UTF-8">
    <title>
        @section('title')
            @setting('core::site-name') | Admin
        @show
    </title>
    <meta id="token" name="token" value="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-api-token" content="{{ $currentUser->getFirstApiKey() }}">
    <meta name="current-locale" content="{{ locale() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">

    <link rel="icon" href="{{ url('/favicon.ico') }}">

    @foreach($cssFiles as $css)
        <link media="all" type="text/css" rel="stylesheet" href="{{ URL::asset($css) }}">
    @endforeach
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" />
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="{{ asset('css/wecom.css') }}" rel="stylesheet" type="text/css" >
    {!! Theme::script('vendor/jquery/jquery.min.js') !!}
    @include('partials.asgard-globals')
    @section('styles')
    @show
    @stack('css-stack')
    @stack('translation-stack')

    <script>
        $.ajaxSetup({
            headers: { 'Authorization': 'Bearer {{ $currentUser->getFirstApiKey() }}' }
        });
        var AuthorizationHeaderValue = 'Bearer {{ $currentUser->getFirstApiKey() }}';
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @routes
</head>
<body class="{{ config('asgard.core.core.skin', 'skin-blue') }} sidebar-mini" style="padding-bottom: 0 !important;">
<div class="wrapper" id="app">
    <header class="main-header">
        <a href="{{ route('dashboard.index') }}" class="logo">
            <span class="logo-mini">
                <small>I</small> - {{ session('azienda') }}
            </span>
            <span class="logo-lg">
              <img class="" src="@setting('core::logo')" style="height:35px">
              {{-- <small>@setting('core::site-name')</small> --}}
              <small>{{ config('app.name') }}</small>
              {{-- <img class="img-thumbnail" src="{{ asset($azienda->logo) }}" style="height:35px"> --}}
            </span>
        </a>
        @include('partials.top-nav')
    </header>
    @include('partials.sidebar-nav')

    <aside class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            @yield('content-header')
        </section>

        <!-- Main content -->
        <section class="content">
            @include('partials.notifications')
            @yield('content')
            <router-view></router-view>
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
    @include('partials.footer')
    @include('partials.right-sidebar')
</div><!-- ./wrapper -->


<div class="modal fade" id="modal-default" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Loading...</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Chiudi</button>
            </div> --}}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@include('core::partials.delete-modal')

@foreach($jsFiles as $js)
    <script src="{{ URL::asset($js) }}" type="text/javascript"></script>
@endforeach

<script>
    window.AsgardCMS = {
        translations: {!! $staticTranslations !!},
        locales: {!! json_encode(LaravelLocalization::getSupportedLocales()) !!},
        currentLocale: '{{ locale() }}',
        editor: '{{ $activeEditor }}',
        adminPrefix: '{{ config('asgard.core.core.admin-prefix') }}',
        hideDefaultLocaleInURL: '{{ config('laravellocalization.hideDefaultLocaleInURL') }}',
        filesystem: '{{ config('asgard.media.config.filesystem') }}'
    };
</script>

{{-- <script src="{{ mix('js/app.js') }}"></script> --}}

<script>
function bootJs() {
    // Modal ajax
    $('#modal-default.form-ajax .btn-danger').click(function(e) {
        e.preventDefault();

        $('#modal-default').modal('hide');
    });
    $('#modal-default.form-ajax form').submit(function(e) {
        e.preventDefault();

        var form = $(this);
        var action = form.attr('action');
        var inputs = new FormData(this);

        $.ajax({
            url: action,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        })
        .fail(function(data) {
            form.find('.has-error').removeClass('has-error');
            form.find('.help-block').remove();

            var errors = $.parseJSON(data.responseText);

            $.each(errors.errors, function(key, error) {
                form.find('[name="' + key + '"]').parent().addClass('has-error');
                form.find('[name="' + key + '"]').after('<span class="help-block">' + error[0] + '</span>');
            });
        })
        .done(function(data) {
            if($.isEmptyObject(data.errors)) {
                location.reload();
            } else {
                form.find('.has-error').removeClass('has-error');
                form.find('.help-block').remove();

                $.each(data.errors, function(key, error) {
                    form.find('[name="' + key + '"]').parent().addClass('has-error');
                    form.find('[name="' + key + '"]').after('<span class="help-block">' + error[0] + '</span>');
                });
            }
        });

        return;
    });

    $('#toggle').bootstrapToggle({
      on: 'SI',
      off: 'No'
    });

    // Currency
    $('.currency').focusout(function() {
        currency(this);
    });

    // Wetags
    $('.tags').selectize({
        delimiter: ',',
        plugins: ['remove_button'],
        persist: false,
        create: function(input) {
            return {
                value: input,
                text: input
            }
        }
    });

    // Datepicker
    $('.datepicker').datetimepicker({
        format: 'DD/MM/YYYY',
        locale: moment.locale('it')
    });

    // Datetimepicker
    $('.datetimepicker').datetimepicker({
        format: 'DD/MM/YYYY - HH:mm',
        locale: moment.locale('it')
    });

    // Timepicker
    $('.timepicker').datetimepicker({
        format: 'HH:mm',
        locale: moment.locale('it')
    });

	// Timepicker richieste
    $('.timepicker_richieste').datetimepicker({
        format: 'HH:mm',
        locale: moment.locale('it'),
		stepping: 30,
		disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 18, 19, 20, 21, 22, 23, 24],
		enabledHours: [8, 9, 10, 11, 12, 13, 14, 15, 16 ,17]
    });

    // Select2
    $('.select2').select2({
        "language": "it-IT"
    });

    // Colorpicker
    $('.colorpicker').colorpicker()

    // Bootstrap slider
    $('.slider').slider({
        tooltip: 'always'
    });

    // Get gruppo users
    $('.get-gruppo-users').click(function(e) {
        e.preventDefault();

        var token = $('input[name="_token"]').val();

        var btn = $(this);
        var gruppo_id = btn.data('id');

        var select = $(btn.data('select'));

        console.log(btn.data('select'));

        var $select = select.selectize();
        var selectize = $select[0].selectize;

        $.post('{{ route('admin.profile.gruppo.users') }}', { _token: token, gruppo_id: gruppo_id })
          .done(function(data) {
            var users = $.parseJSON(data);

            users.forEach(function(user) {
                selectize.addOption({value: user.id, text: user.first_name + ' ' + user.last_name});
                selectize.addItem(user.id);
            });
          });
    });

    // TinyMCE
    var editor_config = {
        selector: "textarea.tinymce",
        urlconverter_callback : 'tinymceURLConverter',
        language: 'it',
        plugins: [
             "advlist autolink link image lists charmap print preview hr anchor pagebreak",
             "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
             "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
        ],
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
        image_advtab: true,

        external_filemanager_path:"{{ env('FILE_MANAGER_PATH') }}",
        filemanager_title:"Filemanager",
        filemanager_access_key:"{{ env('FILE_MANAGER_APP_KEY') }}",
        external_plugins: { "filemanager" : "plugins/responsivefilemanager/plugin.min.js"}
    };

    tinymce.init(editor_config);


    /* ATTIVITA' */
    // Ctrl stato
    var stato_val = $("select[name='stato']").val();

    if(stato_val != 0)
         $("#percentuale_completamento").slider("disable");

    // Percentuale
    $("#percentuale_completamento").change(function() {
        var stato = $("select[name='stato']");

        if($(this).val() == 100)
            stato.val(2);
        else
            stato.val(0);
    });

    // Stato
    $("select[name='stato']").on('change', function(e) {
        var percentuale = $("#percentuale_completamento").slider();
        var vociSlider = $('.attivita-voce input.slider');
        var vociStati = $('.attivita-voce .voce-stato');

        if($(this).val() == 2) { // COMPLETATA
            percentuale.slider("setAttribute", "value", 100);
            percentuale.slider("refresh");
            $("#percentuale_completamento").slider("disable");

            vociSlider.each(function(i, voce) {
                var voce = $(voce);

                voce.slider("setAttribute", "value", 100);
                voce.slider("refresh");
            });

            vociStati.val($(this).val());
        }
        else if($(this).val() == 0 && stato_val == 2) { // IN LAVORAZIONE DA COMPLETATA
            percentuale.slider("setAttribute", "value", 90);
            percentuale.slider("refresh");
        }
        else if($(this).val() == 0) { // IN LAVORAZIONE
            $("#percentuale_completamento").slider("enable");
        }
        else if($(this).val() == 3 || $(this).val() == 1 ) { // ANNULLATTA E IN ATTESA
            $("#percentuale_completamento").slider("disable");
        }
    });

    // Change assegnatari
    getAttivitaAssegnatari(true);
    setInterval(function() {getAttivitaAssegnatari()}, 1000);

    // var $selectAssegnatari = $('select[name="assegnatari_id[]"]').selectize();
    // var selectizeControlAssegnatari = $selectAssegnatari[0].selectize;
    // selectizeControlAssegnatari.on('change', getAttivitaAssegnatari());

    //$('select[name="assegnatari_id[]"]').on('mouseout', getAttivitaAssegnatari());

    // Add voce
    $('#add-voce').click(function() {
        var voce = $('.attivita-voce').last().clone();
        var lastKey = voce.data('key');
        var key = lastKey + 1;

        voce.attr('data-key', key);
        voce.find('input').val('');
        voce.find('.selectize-control').remove();
        voce.find('.slider-horizontal, #aqua').remove();
        voce.find('.allegati .box-footer').remove();

        voce.find('input, select').each(function(index) {
            var attr = $(this).attr('name');
            var id = $(this).attr('id');

            if(attr) {
                var attr = attr.replace('[' + lastKey + ']', '[' + key + ']');
                $(this).attr('name', attr);

                if(id) {
                    var id = id.replace('[' + lastKey + ']', '[' + key + ']');
                    $(this).attr('id', id);
                }
            }
        });

        voce.insertBefore('#container-btn-add-voce');

        $('[name="voci[' + key + '][users][]"]').selectize({
            delimiter: ',',
            plugins: ['remove_button'],
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });

        $('[name="voci[' + key + '][percentuale_completamento]"]').slider({
            tooltip: 'always'
        });

        $('[name="voci[' + key + '][data_inizio]"]').datetimepicker({
            format: 'DD/MM/YYYY',
            locale: moment.locale('it')
        });
        $('[name="voci[' + key + '][data_fine]"]').datetimepicker({
            format: 'DD/MM/YYYY',
            locale: moment.locale('it')
        });

        getAttivitaAssegnatari();
    });
}

// Login Urbi
function loginUrbi(cliente_id) {
  myWindow = window.open('https://cloud.urbi.it/urbi/progs/main/logout.sto' , "_blank", "width=200, height=100");
  setTimeout(function() {
    var url = '{{ route("admin.amministrazione.clienti.login.urbi", ":id") }}';
    url = url.replace(':id', cliente_id);

    myWindow.close();
    window.open(url);
  }, 2000);
}
</script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<?php if (is_module_enabled('Notification')): ?>
    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>
    <script src="{{ Module::asset('notification:js/pusherNotifications.js') }}"></script>
    <script>
        $(".notifications-list").pusherNotifications({
            pusherKey: '{{ config('broadcasting.connections.pusher.key') }}',
            loggedInUserId: {{ $currentUser->id }}
        });
    </script>
<?php endif; ?>

<?php if (config('asgard.core.core.ckeditor-config-file-path') !== ''): ?>
    <script>
        $('.ckeditor').each(function() {
            CKEDITOR.replace($(this).attr('name'), {
                customConfig: '{{ config('asgard.core.core.ckeditor-config-file-path') }}'
            });
        });
    </script>
<?php endif; ?>
@section('scripts')
@show
@stack('js-stack')
</body>
</html>
