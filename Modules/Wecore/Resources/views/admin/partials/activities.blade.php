@php
  $log_description = config('wecore.log.description');
@endphp

<!-- Log attività -->
<div class="box box-info collapsed-box box-shadow">
    <div class="box-header with-border">
      <h3 class="box-title">Timeline</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
        </button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      @php $activity_pre = []; @endphp

      <ul class="timeline">
        @foreach ($activities as $key => $activity)
          @if(!empty($activity_pre))
            @php $diff = array_undot(collection_diff($activity_pre, $activity->properties)); @endphp

            @if(!empty($diff))
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-green">
                  {{ get_date_ita($activity->created_at) }}
                </span>
              </li>
              <!-- /.timeline-label -->

              <!-- timeline item -->
              <li>
                <!-- timeline icon -->
                <i class="fa fa-indent bg-blue"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> {{ get_hour($activity->created_at) }}</span>

                    <h3 class="timeline-header">
                      <strong>{{ ucfirst($log_description[$activity->description]) }} da</strong>
                     {{ $activity->causer->first_name }} {{ $activity->causer->last_name }}
                   </h3>

                  <div class="timeline-body">
                    <ul>
                      @foreach ($diff as $column => $value)
                        <li>
                        @if(is_array($value))
                          <strong>{{ ucfirst(log_clean_column($column)) }}</strong>
                          <ul>
                            @foreach($value as $col => $val)
                              <li>
                                @if(is_array($val))
                                  <strong>{{ ucfirst(log_clean_column($col)) }}</strong>
                                  <ul>
                                    @foreach($val as $co => $va)
                                      <li>
                                        @if(is_array($va))
                                          <strong>{{ ucfirst(log_clean_column($co)) }}</strong>
                                          <ul>
                                            <li>
                                              @foreach($va as $c => $v)
                                                @if(is_array($v))
                                                  <strong>{{ ucfirst(log_clean_column($c)) }}</strong>
                                                  <ul>
                                                    @foreach($v as $cc => $vv)
                                                      <li>
                                                        <strong>{{ ucfirst(log_clean_column($cc)) }}</strong> : {{ log_clean_value($vv, $c, $cc) }}
                                                      </li>
                                                    @endforeach
                                                  </ul>
                                                @else
                                                  <strong>{{ ucfirst(log_clean_column($c)) }}</strong> : {{ log_clean_value($v, $co, $c) }}
                                                @endif
                                              @endforeach
                                            </li>
                                          </ul>
                                        @else
                                          <strong>{{ ucfirst(log_clean_column($co)) }}</strong> : {{ log_clean_value($va, $col, $co) }}
                                        @endif
                                      </li>
                                    @endforeach
                                  </ul>
                                @else
                                  <strong>{{ ucfirst(log_clean_column($col)) }}</strong>: {{ log_clean_value($val, $column, $col) }}
                                @endif
                              </li>
                            @endforeach
                          </ul>
                        @else
                          <strong>{{ ucfirst(log_clean_column($column)) }}</strong>: {{ log_clean_value($value, $column) }}
                        @endif
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </li>
            @endif
          @endif

          @php $activity_pre = $activity->properties; @endphp
        @endforeach
      </ul>
    </div>
    <!-- /.box-body -->
</div>
<!--/.direct-chat -->
