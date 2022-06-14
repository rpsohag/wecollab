{{-- FILES --}}
<div class="box box-success box-shadow">
    <div class="box-header with-border">
      <h3 class="box-title">Files</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
        </button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="file-loading">
            <input id="dropzone" type="file" name="file" multiple class="file" data-overwrite-initial="false" data-min-file-count="1">
        </div>
    </div>
</div>
<!--/.files -->
@push('js-stack')
<script type="text/javascript">
    $("#dropzone").fileinput({
        theme: 'fa',
        uploadUrl: "{{ route('admin.wecloud.uploadFiles') }}",
            uploadExtraData: function() {
                return {
                    _token: $("input[name='_token']").val(),
                };
            },
        overwriteInitial: false,
        browseOnZoneClick: true,
        //maxFileSize:0,
        maxFilesNum: 10,
        slugCallback: function (filename) {
            return filename.replace('(', '_');
        }
    });

    //Update files section on files upload
    $('#dropzone').on('fileuploaded', function(event) {
        $('#uploadFile').modal('toggle');
        $("#allegati").load(location.href + " #allegati");
    }); 
</script>
@endpush