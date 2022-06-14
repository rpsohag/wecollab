<div class="modal fade modal-danger" id="modal-reject-confirmation" tabindex="-1" role="dialog" aria-labelledby="reject-confirmation-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="reject-confirmation-title">{{ trans('core::core.modal.title') }}</h4>
            </div>
            <div class="modal-body">
                <div class="default-message">
                    @isset($message)
                        {!! $message !!}
                    @else
                       Sicuro di voler Rifiutare questa segnalazione di opportutinà
                    @endisset
                </div>
                <div class="custom-message"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">{{ trans('core::core.button.cancel') }}</button>
                {!! Form::open(['method' => 'reject', 'class' => 'pull-left']) !!}
                <button type="submit" class="btn btn-outline btn-flat"><i class="fa fa-trash"></i> Rifiuta la Segnalazione</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@push('js-stack')
<script>
    $( document ).ready(function() {
        $('#modal-reject-confirmation').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var actionTarget = button.data('action-target');
            var modal = $(this);
            modal.find('form').attr('action', actionTarget);

            if (button.data('message') === undefined) {
            } else if (button.data('message') != '') {
                modal.find('.custom-message').show().empty().append(button.data('message'));
                modal.find('.default-message').hide();
            } else {
                modal.find('.default-message').show();
                modal.find('.custom-message').hide();
            }

            if (button.data('remove-submit-button') === true) {
                modal.find('button[type=submit]').hide();
            } else {
                modal.find('button[type=submit]').show();
            }
        });
    });
</script>
@endpush
