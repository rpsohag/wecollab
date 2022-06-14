<?php
namespace Modules\Assistenza\Http\Services;

use Modules\Assistenza\Entities\RichiesteIntervento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Tasklist\Entities\Timesheet;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\Auth;

class StatisticheService
{

    public function statsPerDipendente(Request $request)
    {
        if(empty($request->all()))
        {
            $res['stato'] = 1;
            $res['order']['by'] = 'created_at';
            $res['order']['sort'] = 'desc';
            $request->merge($res);
        }

        $richiesteintervento = RichiesteIntervento::filter($request->all())->get();

        $dettaglio = array();

        $dipendenti = User::all();
        $aree = Area::all();

        foreach($dipendenti as $dipendente) {
            foreach($aree as $area){
                if(empty($dettaglio[$dipendente->id]))
                    $dettaglio[$dipendente->id] = array(); 
                $dettaglio[$dipendente->id][$area->id] = array(); 
                $dettaglio[$dipendente->id][$area->id]['tempo_lavorazione'] = 0;
                $dettaglio[$dipendente->id][$area->id]['tickets'] = 0;
                foreach($richiesteintervento->where('area_id', $area->id) as $richiesta)
                {
                    if(!empty($richiesta->statistiche['tempo_lavorazione_operatori'][$dipendente->id]))
                    {
                        $dettaglio[$dipendente->id][$area->id]['tempo_lavorazione'] += (int) $richiesta->statistiche['tempo_lavorazione_operatori'][$dipendente->id];
                        $dettaglio[$dipendente->id][$area->id]['tickets']++;
                    }
                }
                if(empty($dettaglio[$dipendente->id][$area->id]['tempo_lavorazione']))
                {
                    unset($dettaglio[$dipendente->id][$area->id]);
                } else {
                    $dettaglio[$dipendente->id][$area->id]['titolo'] = $area->titolo;
                    $dettaglio[$dipendente->id][$area->id]['dipendente'] = $dipendente->full_name;  
                    $dettaglio[$dipendente->id][$area->id]['tempo_lavorazione_media'] = round($dettaglio[$dipendente->id][$area->id]['tempo_lavorazione'] / $dettaglio[$dipendente->id][$area->id]['tickets']);         
                }
                if(empty($dettaglio[$dipendente->id]))
                {
                    unset($dettaglio[$dipendente->id]);
                }
            }
        }
        return $dettaglio;
    }

    public function statsPerArea(Request $request)
    {
        if(empty($request->all()))
        {
            $res['stato'] = 1;
            $res['order']['by'] = 'created_at';
            $res['order']['sort'] = 'desc';
            $request->merge($res);
        }

        $richiesteintervento = RichiesteIntervento::filter($request->all())->get();

        $dettaglio = array();
        $aree = Area::all();

        foreach($aree as $area)
        {
            if(empty($dettaglio[$area->id]))
                $dettaglio[$area->id] = array();

            $dettaglio[$area->id]['titolo'] = $area->titolo;
            $dettaglio[$area->id]['aperti'] = $richiesteintervento->where('area_id', $area->id)->where('stato', '<>', 3)->count();
            $dettaglio[$area->id]['chiusi'] = $richiesteintervento->where('area_id', $area->id)->where('stato', 3)->count();
            $dettaglio[$area->id]['tickets'] = $richiesteintervento->where('area_id', $area->id)->count();
            $dettaglio[$area->id]['tempo_lavorazione_totale'] = 0;
            $dettaglio[$area->id]['tempo_risoluzione_totale'] = 0;
            $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] = 0;
            $dettaglio[$area->id]['tempo_lavorazione_media'] = 0;
            $dettaglio[$area->id]['tempo_risoluzione_media'] = 0;
            $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni_media'] = 0;

            foreach($richiesteintervento->where('area_id', $area->id) as $richiesta)
            {
                if(!empty($richiesta->statistiche))
                {
                    $dettaglio[$area->id]['tempo_lavorazione_totale'] += (int) $richiesta->statistiche['tempo_lavorazione_totale'];

                    if($dettaglio[$area->id]['chiusi'] > 0)
                        $dettaglio[$area->id]['tempo_risoluzione_totale'] += (int) $richiesta->statistiche['tempo_risoluzione'];
                        $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] += (int) $richiesta->statistiche['tempo_risoluzione_con_sospensioni'];
                }
            }

            if($dettaglio[$area->id]['tickets'] > 0)
                $dettaglio[$area->id]['tempo_lavorazione_media'] = round($dettaglio[$area->id]['tempo_lavorazione_totale'] / $dettaglio[$area->id]['tickets']);

            
            if($dettaglio[$area->id]['chiusi'] > 0)
                $dettaglio[$area->id]['tempo_risoluzione_media'] = round($dettaglio[$area->id]['tempo_risoluzione_totale'] / $dettaglio[$area->id]['chiusi']);

            if($dettaglio[$area->id]['chiusi'] > 0 && $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] > 0)            
                $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni_media'] = round($dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] / $dettaglio[$area->id]['chiusi']);

            if($dettaglio[$area->id]['tickets'] == 0)
                unset($dettaglio[$area->id]);
        }
        return $dettaglio;
    }




}