@php
    $gruppi = (empty($gruppi)) ? [] : $gruppi;
    $attivita_list = [''] + $ordinativo->attivita->pluck('oggetto', 'id')->toArray();
@endphp

<div class="box-body">

@foreach ($gruppi as $key => $gruppo)
    @php
        $giornate = $ordinativo->get_giornate_by_gruppo($gruppo->id);
        $interventi_sum = $ordinativo->interventi_sum_by_gruppo($gruppo->id)
    @endphp

    <div class="col-md-3">
        <div class="box box-info box-solid {{ (get_if_exist($giornate, 'quantita') > 0) ? '' : 'collapsed-box' }}">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $gruppo->nome }}</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa {{ (get_if_exist($giornate, 'quantita') > 0) ? 'fa-minus' : 'fa-plus' }}"></i>
                    </button>
                </div>
            <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body text-center">
                <div class="row">
                    <div class="col-md-5">
                        {{ Form::weInt('giornate['.$gruppo->id.'][quantita]', 'Quantità', $errors, get_if_exist($giornate, 'quantita'), ['class' => "form-control text-center", 'placeholder' => '']) }}
                    </div>
                    <div class="col-md-7">
                        {{ Form::weSelect('giornate['.$gruppo->id.'][tipo]', 'Tipo', $errors, config('commerciale.interventi.tipi'), get_if_exist($giornate, 'tipo')) }}
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                      {{ Form::weInt('giornate['.$gruppo->id.'][quantita_gia_effettuate]', 'Giornate/Ore già effettuate', $errors, get_if_exist($giornate, 'quantita_gia_effettuate'), ['class' => "form-control text-center", 'placeholder' => '']) }}
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 border-right">
                    <div class="description-block">
                      <h5 class="description-header text-success">Effettuate</h5>
                      <span class="description-text">{{ $interventi_sum }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="description-block">
                      <h5 class="description-header text-warning">Residue</h5>
                      <span class="description-text">
                          {{ get_if_exist($giornate, 'quantita_residue') ? $giornate->quantita_residue : 0 }}
                      </span>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        {{ Form::weSelectSearch('giornate['.$gruppo->id.'][attivita]', 'Attività', $errors, $attivita_list, get_if_exist($giornate, 'attivita'), ['style' => 'width: 100%;']) }}
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
@endforeach

</div>
