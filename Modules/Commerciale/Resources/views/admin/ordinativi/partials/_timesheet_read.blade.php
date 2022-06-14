@php

    $gruppi = (empty($gruppi)) ? [] : $gruppi;
    $attivita_list = [''] + $ordinativo->attivita->pluck('oggetto', 'id')->toArray();
    	 
@endphp

<div class="box-body">
    <table class="table">
        <thead>
        <tr>
            <th>Area di intervento</th>
            <th>Gruppi</th>
            <th>Utenti</th>
            <th>Durata</th>
        </tr>
        </thead>
        <tbody>
        @foreach($aree as $area)
            <tr>
                <td>{{  $area->titolo }}</td>
                <td>{{ $area->getTimesheetsGroups() }}</td>
                <td>{{ $area->getTimesheetsUsers() }}</td>
                <td>{{ $area->getTimesheetsDuration() }}</td>
            </tr>
        @endforeach

        </tbody>

    </table>
</div>
