<script type="text/javascript">
    $(document).ready(function() {
        $('#modal-default form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var action = form.attr('action');
            var post = form.serialize();

            $.post(action, post)
              .done(function(data) {
                  if(data.errors) {
                      form.find('.has-error').removeClass('has-error');
                      form.find('.help-block').remove();

                      $.each(data.errors, function( index, value ) {
                          form.find('[name="'+index+'"]').parent().addClass('has-error');
                          form.find('[name="'+index+'"]').after('<span class="help-block">'+value+'</span>');
                        });
                  } else {
                      if(data.redirect)
                        location.href = data.redirect;
                      else
                        window.location.reload();
                  }
              });
        });
    });
</script>
