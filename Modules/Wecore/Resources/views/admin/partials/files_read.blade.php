@php
    if(empty($model))
        $model = (object) ['files' => []];

    if(empty($name))
    {
        $name_file_name = 'meta[file][name]';
        $name_file_file = 'meta[file][file]';
        $get_file_name = 'meta.file.name';
        $get_file_file = 'meta.file.file';
    }
    else
    {
        $name_file_name = $name . '[meta][file][name]';
        $name_file_file = $name . '[meta][file][file]';
        $get_file_name = $name . '.meta.file.name';
        $get_file_file = $name . '.meta.file.file';
    }
@endphp

{{-- ALLEGATI --}}
@if(!empty($model->files) && count($model->files) > 0)
    <div class="box box-success collapsed-box box-shadow allegati">
        <div class="box-header with-border">
        <h3 class="box-title">Allegati</h3>
        <div class="box-tools pull-right">
            <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ (!empty($model->files) ? count($model->files) : 0) }} Files">{{ (!empty($model->files) ? count($model->files) : 0) }}</span>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
        </div>
        </div>
        <div class="box-body">
            @foreach($model->files as $key => $file)
                @if(!empty($file->value))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-solid box-shadow">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <span class="fa-stack fa-2x">
                                            <i class="fa fa-square-o fa-stack-2x"></i>
                                            <i class="fa {{ file_icons($file->value->extension) }} fa-stack-1x"></i>
                                            </span>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a>
                                                </div>
                                                <div class="col-md-4">
                                                    <i class="fa fa-calendar"> </i> {{ $file->updated_at }}
                                                </div>
                                                <div class="col-md-8">
                                                    <i class="fa fa-save"> </i> {{ mb($file->value->size) }} MB
                                                </div>
                                                <div class="col-md-4">
                                                    <i class="fa fa-user"> </i> {{ $file->updatedUser->first_name }} {{ $file->updatedUser->last_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif