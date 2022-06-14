<div class="box-body">
	<div class="box box-primary box-shadow">
	    <div class="box-header with-border">
            <h3><strong>Elenco degli ordinativi del cliente</strong></h3>
            <br>
	    </div>
	    @if(!empty($ordinativi) && $ordinativi->count() > 0)
	    	<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped">
						<tr>
                            <th>Oggetto</th>
                            <th>Cliente</th>
                            <th>Data Inizio</th>
                            <th>Data Fine</th>
						</tr>
						<tbody>
						@foreach ($ordinativi as $ordinativo)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.commerciale.ordinativo.read', ['ordinativo' => $ordinativo->id]) }}">
                                        {{ $ordinativo->oggetto }}
                                    </a>
                                </td>
                                <td>
                                    @if(!empty($ordinativo->cliente()->ragione_sociale))
                                        {{ $ordinativo->cliente()->ragione_sociale }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.ordinativo.read', ['ordinativo' => $ordinativo->id]) }}">
                                        {{ $ordinativo->data_inizio }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.ordinativo.read', ['ordinativo' => $ordinativo->id]) }}">
                                        {{ $ordinativo->data_fine }}
                                    </a>
                                </td>
                            </tr>
						@endforeach
						</tbody>
					</table>
					<div class="text-right pagination-container">
						{{ $ordinativi->links() }}
					</div>
				</div>
		    </div>
	    @endif
	</div>
</div>