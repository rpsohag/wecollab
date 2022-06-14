{{-- NOTE --}}
@php
    if(empty($model))
        $model = (object) ['notes' => []];

    if(empty($name))
    {
        $name_note = 'meta[note]';
        // $get_note = 'meta.note';
    }
    else
    {
        $name_note = $name . '[meta][note]';
        // $get_note = $name . '.meta.note';
    }
@endphp
@php $url = explode("/", Request::path() ) @endphp
<!-- DIRECT CHAT SUCCESS -->
@if(count($model->notes) > 0 || end($url) !== 'read')
  <div class="box box-success direct-chat direct-chat-success {{ (count($model->notes) > 0) ? '' : 'collapsed-box' }} box-shadow">
      <div class="box-header with-border">
        <h3 class="box-title">Note</h3>

        <div class="box-tools pull-right">
          <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ count($model->notes) }} Messaggi">{{ count($model->notes) }}</span>
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa {{ (count($model->notes) > 0) ? 'fa-minus' : 'fa-plus' }}"></i>
          </button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
            @foreach ($model->notes as $key => $note) 
            
              <!-- Message. Default to the left -->
              <div class="direct-chat-msg {{ (Auth::id() === $note->createdUser->id) ? 'right' : '' }}">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-left">{{ $note->createdUser->first_name }} {{ $note->createdUser->last_name }}</span>
                  <span class="direct-chat-timestamp pull-right">{{ $note->created_at }}</span>
                </div>
                <!-- /.direct-chat-info -->
                <img class="direct-chat-img" src="{{ set_via_placeholder(128) }}" alt="Message User Image"><!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  {{ $note->value }} 
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->
            @endforeach
        </div>
        <!--/.direct-chat-messages-->
      </div>
      <!-- /.box-body -->
      @if(end($url) !== 'read')
        <div class="box-footer">
          <div class="input-group">
            <input name="{{ $name_note }}" placeholder="Scrivi nota ..." class="form-control" type="text">
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-success btn-flat">Salva</button>
                </span>
          </div>
        </div>
      @endif
      <!-- /.box-footer-->
  </div>
  <!--/.direct-chat -->
@endif
