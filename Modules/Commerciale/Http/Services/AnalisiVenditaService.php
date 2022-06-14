<?php
namespace Modules\Commerciale\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Commerciale\Entities\AnalisiVendita;

use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Entities\CensimentoCliente;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;
use Modules\Profile\Entities\Procedura;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Area;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\FiguraProfessionale;

class AnalisiVenditaService
{
    private $figure;
    private $aree;
    private $gruppi;
    private $procedure;

    public function __construct()
    {
        $this->figure = FiguraProfessionale::all();
        $this->aree = Area::all();
        $this->gruppi = Gruppo::all();
        $this->procedure = Procedura::all();
    }

    public function riepilogoFigure(AnalisiVendita $analisi) 
    {
        $dettaglio = [];
        $dettaglio['figure'] = [];
        $dettaglio['totali'] = [];

        $dettaglio['totali'] = ['costo_interno' => 0, 'importo_vendita' => 0, 'ore' => 0];

        foreach($analisi->attivita as $id_attivita => $attivita)
        {
            if(!empty($attivita->figure_professionali))
            {
                foreach($attivita->figure_professionali as $key => $figura_professionale)
                {
                    if(!empty($figura_professionale->figura_professionale_id))
                    {
                        $fp = $this->figure->find($figura_professionale->figura_professionale_id);

                        if(empty($dettaglio['figure'][$fp->id]['nome']))
                        {
                            $dettaglio['figure'][$fp->id]['nome'] = $this->figure->find($figura_professionale->figura_professionale_id)->descrizione;
                            $dettaglio['figure'][$fp->id]['costo_interno'] = 0;
                            $dettaglio['figure'][$fp->id]['importo_vendita'] = 0;
                            $dettaglio['figure'][$fp->id]['ore'] = 0;
                        }

                        $dettaglio['figure'][$fp->id]['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['figure'][$fp->id]['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['figure'][$fp->id]['ore'] += get_if_exist($figura_professionale->ore);

                        $dettaglio['totali']['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['totali']['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['totali']['ore'] += get_if_exist($figura_professionale->ore);

                    }
                }
            }
        }

        return $dettaglio;
    }

    public function riepilogoAree(AnalisiVendita $analisi) 
    {
        $dettaglio = [];
        $dettaglio['aree'] = [];
        $dettaglio['totali'] = [];

        $dettaglio['totali'] = ['costo_interno' => 0, 'importo_vendita' => 0, 'ore' => 0];

        foreach($analisi->attivita as $id_attivita => $attivita)
        {
            if(!empty($attivita->figure_professionali))
            {
                foreach($attivita->figure_professionali as $key => $figura_professionale)
                {
                    if(!empty($figura_professionale->figura_professionale_id))
                    {
                        $fp = $this->figure->find($figura_professionale->figura_professionale_id);
                        $area = $this->aree->find($this->gruppi->find($id_attivita)->area_id);

                        if(empty($dettaglio['aree'][$area->id]['nome']))
                        {
                            $dettaglio['aree'][$area->id]['nome'] = $area->titolo;
                            $dettaglio['aree'][$area->id]['costo_interno'] = 0;
                            $dettaglio['aree'][$area->id]['importo_vendita'] = 0;
                            $dettaglio['aree'][$area->id]['ore'] = 0;
                        }

                        $dettaglio['aree'][$area->id]['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['aree'][$area->id]['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['aree'][$area->id]['ore'] += get_if_exist($figura_professionale->ore);

                        $dettaglio['totali']['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['totali']['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['totali']['ore'] += get_if_exist($figura_professionale->ore);

                    }
                }
            }
        }

        return $dettaglio;
    }

    public function riepilogoAttivita(AnalisiVendita $analisi)
    {
        $dettaglio = [];
        $dettaglio['aree'] = [];

        foreach($analisi->attivita as $id_attivita => $attivita)
        {
            if(!empty($attivita->figure_professionali))
            {
                foreach($attivita->figure_professionali as $key => $figura_professionale)
                {
                    if(!empty($figura_professionale->figura_professionale_id))
                    {
                        $fp = $this->figure->find($figura_professionale->figura_professionale_id);
                        $area = $this->aree->find($this->gruppi->find($id_attivita)->area_id);
                        $gruppo = $this->gruppi->find($id_attivita);

                        if(empty($dettaglio['aree'][$area->id]['nome']))
                        {
                            $dettaglio['aree'][$area->id]['nome'] = $area->titolo;
                            $dettaglio['aree'][$area->id]['costo_interno'] = 0;
                            $dettaglio['aree'][$area->id]['importo_vendita'] = 0;
                            $dettaglio['aree'][$area->id]['ore'] = 0;
                            $dettaglio['aree'][$area->id]['gruppi'] = [];
                        }

                        $dettaglio['aree'][$area->id]['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['aree'][$area->id]['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['aree'][$area->id]['ore'] += get_if_exist($figura_professionale->ore);

                        if(empty($dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['nome']))
                        {
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['nome'] = $gruppo->nome;
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'] = [];
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['costo_interno'] = 0;
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['importo_vendita'] = 0;
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['ore'] = 0;
                        }

                        $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['ore'] += get_if_exist($figura_professionale->ore);

                        if(empty($dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['nome']))
                        {
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['nome'] = $fp->descrizione;
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['costo_interno'] = 0;
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['importo_vendita'] = 0;
                            $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['ore'] = 0;
                        }

                        $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['costo_interno'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->costo_interno);
                        $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['importo_vendita'] += get_if_exist($figura_professionale->ore) * clean_currency($fp->importo_vendita);
                        $dettaglio['aree'][$area->id]['gruppi'][$gruppo->id]['figure'][$fp->id]['ore'] += get_if_exist($figura_professionale->ore);
                    }
                }
            }
        }

        return $dettaglio;

    }

    public function riepilogoCostiFissi(AnalisiVendita $analisi)
    {
        $dettaglio = [];
        $dettaglio['items'] = [];
        $dettaglio['totali'] = [];
        $dettaglio['totali'] = ['costo_interno' => 0, 'costo_uscita' => 0];

        if(!empty($analisi->costi_fissi))
        {
            foreach($analisi->costi_fissi as $key => $item)
            {
                $dettaglio['items'][$key]['nome'] = get_if_exist($item, 'descrizione');
                $dettaglio['items'][$key]['quantita'] = get_if_exist($item, 'quantita');
                $dettaglio['items'][$key]['costo_unitario'] = clean_currency(get_if_exist($item, 'costo_unitario'));
                $dettaglio['items'][$key]['costo_di_uscita'] = clean_currency(get_if_exist($item, 'prezzo_di_uscita'));
                $dettaglio['items'][$key]['link'] = get_if_exist($item, 'link_di_acquisto');
                $dettaglio['items'][$key]['rincaro'] = '' . get_if_exist($item, 'percentuale_rincaro') . (get_if_exist($item, 'percentuale_rincaro') ? '%' : '0%');
                $dettaglio['items'][$key]['costo_totale'] = ((int)get_if_exist($item, 'quantita') * (float)clean_currency(get_if_exist($item, 'costo_unitario'))) + ((((int)get_if_exist($item, 'quantita') * (float)clean_currency(get_if_exist($item, 'costo_unitario'))) * (float)get_if_exist($item, 'percentuale_rincaro')) / 100 ?? 0);

                $dettaglio['totali']['costo_interno'] += (float)$dettaglio['items'][$key]['costo_unitario'] * (int)$dettaglio['items'][$key]['quantita'];
                $dettaglio['totali']['costo_uscita'] += (float)$dettaglio['items'][$key]['costo_totale'];
            }
        }

        return $dettaglio;

    }

}