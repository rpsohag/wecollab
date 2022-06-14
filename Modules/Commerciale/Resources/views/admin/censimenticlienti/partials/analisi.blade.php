<h3>Analisi di Vendita Collegate</h3>

<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">

			 <table class="data-table table table-bordered table-hover">
          <thead>
          <tr>
              <th>Titolo</th>
              <th>Cliente</th>
							<th>Lavorata da</th>
              <th>{{ trans('core::core.table.created at') }}</th>
              <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
          </tr>
          </thead>
          <tbody>
          <?php if (isset($analisivendite)): ?>
          <?php foreach ($analisivendite as $analisivendita): ?>
          <tr>
              <td>
                  <a href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id]) }}">
                      {{ $analisivendita->titolo }}
                  </a>
              </td>
              <td>
                  <a href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id]) }}">
                      {{ $analisivendita->censimento_cliente->cliente }}
                  </a>
              </td>
							<td>
                  <a href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id]) }}">
										@if(!empty($analisivendita->segnalazioni->first()))
                      {{ $analisivendita->segnalazioni->first()->commerciale->full_name }}
										@endif
                  </a>
              </td>
              <td>
                  <a href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id]) }}">
                      {{ get_date_hour_ita($analisivendita->created_at) }}
                  </a>
              </td>
              <td>
                  <div class="btn-group">
                      <a href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                   </div>
              </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
          <tfoot>
          <tr>
              <th>Titolo</th>
              <th>Cliente</th>
              <th>{{ trans('core::core.table.created at') }}</th>
              <th>{{ trans('core::core.table.actions') }}</th>
          </tr>
          </tfoot>
      </table>
		</div>
		{{-- end nav-tabs-custom --}}
	</div>
</div>
