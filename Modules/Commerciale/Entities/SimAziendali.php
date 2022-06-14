<?php
namespace Modules\Commerciale\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use EloquentFilter\Filterable;

class SimAziendali extends Model
{
    use Filterable;

    protected $table = 'commerciale__simaziendali';
    // public $translatedAttributes = [];
    protected $fillable = [
        'azienda',
        'numero_contratto',
        'operatore',
        'telefono',
        'assegnatario',
        'tipo_sim',
        'cod_esim',
        'iccid',
        'profilo',
        'puk',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'numero_contratto' => 'required|numeric',
            'operatore' => 'required',
            'telefono' => 'required|integer',
            'tipo_sim' => 'required',
            'operatore' => 'required|nullable',
            'assegnatario' => 'required|integer|min:1',
            'iccid' => 'required'
        ];
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\SimAziendaliFilter::class);
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function applicato()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'assegnatario' );
    }

}
