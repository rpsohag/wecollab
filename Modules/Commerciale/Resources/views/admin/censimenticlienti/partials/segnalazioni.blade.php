<h3>Elenco Delle Segnalazioni Opportunità Aperte</h3>

<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">

			<table class="data-table table table-bordered table-hover">
				<thead>
					<tr>
						<th>Numero</th>
						{{-- <th>Cliente</th> --}}
						<th>Oggetto</th>
						<th>Creato da</th>
						<th>{{ trans('core::core.table.created at') }}</th>
						<th>Stato</th>
						<th>Commerciale</th>
						<th>Analisi di Vendita</th>
						<th>Visualizza</th>

					<!--	<th data-sortable="false">{{ trans('core::core.table.actions') }}</th>-->
					</tr>
				</thead>
				<tbody>
					@if(!empty($segnalazioniopportunita))
					@foreach ($segnalazioniopportunita as $segnalazioneopportunita)
					<tr>
						<td><a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}"> {{ $segnalazioneopportunita->numero() }} </a></td>
						{{-- <td><a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}"> {{ $segnalazioneopportunita->cliente }} </a></td> --}}
						<td><a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}"> {{ $segnalazioneopportunita->oggetto }} </a></td>
						<td><a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}"> {{ $segnalazioneopportunita->created_user->first_name }} {{ $segnalazioneopportunita->created_user->last_name }} </a></td>
						<td><a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}"> {{ $segnalazioneopportunita->created_at }} </a></td>
						<!-- <td><a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}"> {{ $segnalazioneopportunita->stato() }} </a></td>-->
						<td>
						{{ Form::weSelect('stato_id['.$segnalazioneopportunita->id.']', '', $errors, config('commerciale.segnalazioneopportunita.stati'), get_if_exist($segnalazioneopportunita->stato_id, 'stato_id') , array('class'=>'update-id_state')) }}
						</td>

						<td>
							{{ Form::weSelect('commerciale_id['.$segnalazioneopportunita->id.']', '', $errors, $commerciali, get_if_exist($segnalazioneopportunita->commerciale_id, 'commerciale_id') , array('class'=>'update-id_commerciale')) }}
						</td>
						<td>
							@if(!empty($segnalazioneopportunita->analisi_vendita))
								<a id="links_{{$segnalazioneopportunita->id}}" href="{{ route('admin.commerciale.analisivendita.edit',  $segnalazioneopportunita->analisi_vendita->id ) }}" class="btn btn-primary btn-flat ">Vedi Analisi di Vendita</a>
							@else
								<a id="links_{{$segnalazioneopportunita->id}}" href="{{ route('admin.commerciale.analisivendita.create', ['censimentocliente_id' => $censimentocliente->id , 'segnalazioni_id' => $segnalazioneopportunita->id, 'commerciale_id' => get_if_exist($segnalazioneopportunita->commerciale_id, 'commerciale_id')] ) }}" class="btn btn-success btn-flat">Crea un'Analisi di Vendita</a>
							@endif
						</td>

						<td>
							<div class="btn-group">
								<button type="button" class="btn btn-md btn-flat btn-info" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Vedi" data-action="{{ route('admin.commerciale.segnalazioneopportunita.edit', $segnalazioneopportunita->id) }}" data-element="#tab_1" data-form-disabled="true">
									<i class="fa fa-eye"> </i>
								</button>
								@if(count($segnalazioneopportunita->attivita) > 0)
									<a class="btn btn-default btn-flat" href="{{ route('admin.tasklist.attivita.edit', $segnalazioneopportunita->attivita()->first()->id) }}" data-toggle="tooltip" title="Percentuale completamento: {{ $segnalazioneopportunita->attivita()->first()->percentuale_completamento() }}"><i class="fa fa-tasks"> </i></a>
								@endif
							</div>
						</td>

					</tr>
					@endforeach
					@endif
				</tbody>
				<tfoot>
					<tr>
						<th>Numero</th>
						{{-- <th>Cliente</th> --}}
						<th>Oggetto</th>
						<th>Creato da</th>
						<th>{{ trans('core::core.table.created at') }}</th>
						<th>Stato</th>
						<th>Commerciale</th>
						<th>Analisi di Vendita</th>
						<th>Visualizza</th>

					</tr>
				</tfoot>
			</table>
		</div>
		{{-- end nav-tabs-custom --}}
	</div>
</div>
