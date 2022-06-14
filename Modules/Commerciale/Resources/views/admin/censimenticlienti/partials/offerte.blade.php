@php
$stati_filter = ['' => ''];
$stati_filter = $stati_filter + config('commerciale.offerte.stati');

$stati = config('commerciale.offerte.stati');
$stati[101] = 'Accettata senza Determina/ODA';
$stati[102] = 'Accettata senza Ordine e Determina';

$stati = collect($stati)->sort()->toArray();

$stati_colori = json_decode(setting('commerciale::offerte::stati_colori'), true);
@endphp

<div class="box-body">
	<div class="box box-primary box-shadow">
	    <div class="box-header with-border">
			<h3><strong>Elenco delle offerte del cliente</strong></h3>
			<br>
			<div>
			<h4>Legenda colori:</h4>
			@foreach ($stati as $key => $stato)
				@if(!empty($stati_colori[$key]))
					<div class="col-md-1" style="{{($key === 0) ? 'border: 1px solid black' : ''}};height:15px; background-color:{{ $stati_colori[$key] }};">  </div>
					<div class="col-md-3">{{ $stato }}</div>
				@endif
			@endforeach
			</div>
	    </div>
	    @if(!empty($offerte) && $offerte->count() > 0)
	    	<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped">
						<tr>
						<th>Codice</th>
						<th>Data</th>
						<th>Importo Iva Esclusa</th>
						<th>Importo Iva Inclusa</th>
						<th>Oggetto</th>
						<th>Stato</th>
						<th>Fatturata</th>
						<th>Azioni <small>(Voci)</small></th>
						</tr>
						<tbody>
						@foreach ($offerte as $offerta)
						    @php
							if(is_numeric($offerta->stato))
							{
								$color = "#3c8dbc";
								$color_bg = '';

								if($offerta->stato !== 0 && $offerta->stato !== 102 && !empty($stati_colori[$offerta->stato]))
								$color = "#FFF";

								if($offerta->stato == 1 && $offerta->oda_determina_ids->isEmpty())
								$offerta->stato = 101;

								if(strtolower($offerta->cliente->tipologia) == 'pubblico' && $offerta->stato == 1 && empty($offerta->ordine_mepa_id) && $offerta->oda_determina_ids->isEmpty())
								{
								$color = '#000';
								$offerta->stato = 102;
								}

								if(!empty($stati_colori[$offerta->stato]))
								$color_bg = $stati_colori[$offerta->stato];
							}
							@endphp
					     	<tr style="background-color: {{ $color_bg }} ;">
								<td>{{ $offerta->numero_offerta() }}</td>
								<td>{{ $offerta->data_offerta }}</td>
								<td>{{ get_currency($offerta->importo_esente) }}</td>
								<td>{{ get_currency($offerta->importo_iva) }}</td>
								<td>{{ $offerta->oggetto }}</td>
								<td>
									@if(is_numeric($offerta->stato))
									    {{ $stati[$offerta->stato] }}
									@endif
								</td>
								<td>{!! sn_icon($offerta->fatturata()) !!}</td>
								<td><div class="text-center"><button type="button" class="btn btn-success btn-flat voci-button" data-offerta_id="{{ $offerta->id }}"><i class="fa fa-eye"></i></button></div></td>
					    	</tr>
						@endforeach
						</tbody>
					</table>
					<div class="text-right pagination-container">
						{{ $offerte->links() }}
					</div>
				</div>
		    </div>
	    @endif
	</div>
</div>

<!-- Modal Voci -->
<div class="modal fade" id="modal-voci" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
	    <div class="modal-content">
	    	<div class="modal-header"><h3 class="modal-title">Voci dell'offerta</h3></div>
	    	<div class="modal-body">
	    	</div>
	    	<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button></div>
	    </div>
	</div>
</div>

@push('js-stack')
<script>
  $(document).ready(function(){
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      $(".voci-button").click(function(e){
		  e.preventDefault();
          var offerta_id = $(this).data('offerta_id');
          var modal = $('#modal-voci');

          $.ajax({
              url: "{{ route('admin.commerciale.censimentocliente.read.offerta.voci') }}",
              type: 'POST',
              data: {_token: CSRF_TOKEN, offerta_id: offerta_id},
              dataType: 'JSON' 
          }).done(function(data) {
              var res = $.parseJSON(data);
              var html = '<div class="callout callout-info"><h4>Avviso!</h4><p>L\'offerta non ha alcuna voce.</p></div>';

              if(res.empty != 1) {
				html = '';
				html += '<div class="row"><div class="col-xs-12 table-responsive"><table class="table table-striped voci"><thead><tr>';
				html += '<th>Descrizione</th><th style="width:10%;" class="text-center">Quantità</th><th style="width:15%;" class="text-right">Importo Singolo</th><th style="width:10%;" class="text-right">';
				html += 'IVA</th><th style="width:15%;" class="text-right">Importo</th><th style="width:13%;" class="text-right">Importo con IVA</th><th style="width:2%;">Esente IVA</th><th style="width:2%;">Accettata</th>';
                html += '</tr></thead><tbody>'

                for (var key in res.values) {
                  var data = res.values[key];
				  
				  html += '<tr><td>' +  data.descrizione + '</td>';
				  html += '<td class="text-center">' + data.quantita + '</td>';
				  html += '<td class="text-right">' + data.importo_singolo + '</td>';
				  html += '<td class="text-right">' + data.iva + '</td>';
				  html += '<td class="text-right">' + data.importo + '</td>';
				  html += '<td class="text-right">' + data.importo_iva + '</td>';
				  html += '<td>' + data.esente_iva + '</td>';
				  html += '<td>' + data.accettata + '</td></tr>';
				}
				html += '</tbody></table>'
              }

              modal.find('.modal-body').html(html);
              modal.modal('show');
          }); 
      });
 });    
</script>
@endpush
