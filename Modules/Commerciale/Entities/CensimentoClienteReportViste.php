<?php
namespace Modules\Commerciale\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;



class CensimentoClienteReportViste extends Model
{
    use Filterable;

    protected $table = 'commerciale_censimenticlienti_report_viste';
    // public $translatedAttributes = [];
    protected $fillable = [
        'data',
        'descrizione',
        'cliente_id',
        'UtenteName',
     
    ];

    public static function getRules()
    {
        return [
            'data' => 'required',
            'descrizione' => 'required',
            'UtenteName' => 'required',
           
        ];
    }

   


}
