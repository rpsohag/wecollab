<?php
namespace Modules\Commerciale\Entities;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Fatturazione extends Model
{
    use Filterable;

    protected $table = 'commerciale__fatturazioni';
    protected $fillable = [
        'azienda',
        'oggetto',
        'n_fattura',
        'codice_univoco',
        'cig',
        'rda',
        'rda_data',
        'data',
        'cliente_id',
        'id_tipologia_fornitura',
        'indirizzo',
        'fepa',
        'fattura_pa',
        'nota_di_credito',
        'iva_erario',
        'n_giorni',
        'tipo_pagamento',
        'acconto',
        'totale_netto',
        'iva',
        'iva_natura',
        'riferimento_normativo',
        'totale_fattura',
        'totale_importo_dovuto',
        'iva_esigibile',
        'anticipata_id',
        'iban',
        'ordinativo_id',
        'anticipata',
        'consegnata',
        'pagata',
        'note',
        'attivita_svolta',
        'nota_di_credito_interna',
        'macrocategoria',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'oggetto' => 'required',
            'n_fattura' => 'required',
            'data' => 'required|date_format:"d/m/Y"',
            'cliente_id' => 'required|integer|min:1',
            'indirizzo' =>'required',
            'fepa' => 'boolean',
            'iva_erario' => 'required|boolean',
            'n_giorni' => 'required|integer',
            'totale_netto' => 'required|numeric',
            'iva' => 'required|numeric',
            'totale_fattura' => 'required|numeric',
            'totale_importo_dovuto' => 'required|numeric',
            'anticipata_id' => 'integer',
            'ordinativo_id' => 'integer'
        ];
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\FatturazioneFilter::class);
    }

    public function getDataAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getIvaAttribute($iva)
    {
      return (float)$iva;
    }

    public function setDataAttribute($date)
    {
        $this->attributes['data'] = set_date_ita($date);
    }

    public function getRdaDataAttribute($date)
    {
        return get_date_ita($date);
    }

    public function setRdaDataAttribute($date)
    {
        $this->attributes['rda_data'] = set_date_ita($date);
    }

    public function getTotaleNettoAttribute($valore)
    {
        return get_currency($valore);
    }

    public function getTotaleFatturaAttribute($valore)
    {
        return get_currency($valore, '€');
    }

    public function getTotaleImportoDovutoAttribute($valore)
    {
        return get_currency($valore, '€');
    }

    public function getAccontoAttribute($valore)
    {
        return get_currency($valore);
    }

    public function stato() {
      if($this->pagata == 1)
        return 'Pagata';
      elseif($this->anticipata == 1)
        return 'Anticipata';
      elseif($this->scaduta())
        return 'Scaduta';
      elseif($this->consegnata == 1)
        return 'Consegnata';
      else
        return 'Emessa';
    }

    public function stato_colore() {
      $stati_colori = json_decode(setting('commerciale::fatturazione::colori'));

      if($this->pagata === 1)
      {
          return [
            'background' => $stati_colori->pagata,
            'text' => '#fff'
          ];
      }
      // elseif($this->scaduta())
      // {
      //   return [
      //     'background' => $stati_colori->scaduta,
      //     'text' => '#fff'
      //   ];
      // }
      elseif($this->anticipata === 1)
      {
        return [
          'background' => $stati_colori->anticipata,
          'text' => ''
        ];
      }
      elseif($this->consegnata === 1) {
        return [
          'background' => $stati_colori->consegnata,
          'text' => ''
        ];
      }
      else
      {
        return [
          'background' => $stati_colori->default,
          'text' => '#fff'
        ];
      }
    }

    public static function get_next_numero_nota_di_credito_interna()
    {
      $numero_fattura = Fatturazione::where('azienda', session('azienda'))
                              ->where('nota_di_credito_interna', 1)
                              ->whereYear('data', '=', date("Y"))
                              ->max('n_fattura');

       return ($numero_fattura + 1);
    } 

    public function get_numero_fattura($numero_fattura = NULL, $fattura_pr = false, $nota_di_credito_interna = false)
    {
        $numero_fattura = (empty($this) || !empty($numero_fattura)) ? $numero_fattura : $this->n_fattura;

        if($this->nota_di_credito_interna == 1 || $nota_di_credito_interna == true)
            $sigla_fattura = 'NC';
        else
        {
          if(is_numeric($this->fattura_pa))
            $fattura_pr = $this->fattura_pa == 1 ? false : true;

          if($fattura_pr === true)
              $sigla_fattura = 'FPR';
          else
              $sigla_fattura = 'FPA';
        }

        return $sigla_fattura . ' ' . $numero_fattura . '/' . (empty($this->created_at) ? date('Y') : date('Y', strtotime($this->created_at)));
    }

    public static function get_next_numero_fattura($fattura_pr = false)
    {
        $numero_fattura = Fatturazione::where('azienda', session('azienda'))
                                ->where('fattura_pa', ($fattura_pr) ? 0 : 1)
                                ->whereYear('data', '=', date("Y"))
                                ->max('n_fattura');

        if($numero_fattura == null){
          $numero_fattura = 0;
        }
        return ($numero_fattura + 1);
    }

    public function imposta()
    {
        return clean_currency($this->totale_fattura) - clean_currency($this->totale_netto);
    }

    public function scaduta()
    {
        //$today = new Carbon;
        //$ctrl_date = Carbon::createFromFormat('d/m/Y', $this->data)->addDays($this->n_giorni);

        $today = date("Y/m/d", strtotime(now()));
        $data = set_date_ita($this->data);
        $ctrl_date = date("Y/m/d", strtotime("+" . $this->n_giorni . " day", strtotime($data)));

        if($today > $ctrl_date)
          return true;
        else
          return false;
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id');
    }

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id');
    }

    public function voci()
    {
        return $this->hasMany('Modules\Commerciale\Entities\FatturazioneVoce', 'fatturazione_id');
    }

    public function scadenza()
    {
        return $this->hasOne('Modules\Commerciale\Entities\FatturazioneVoce', 'fatturazione_id');
    }
}
