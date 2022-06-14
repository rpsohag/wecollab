<?php

namespace Modules\Amministrazione\Entities;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Auth;
use Modules\User\Entities\Sentinel\User;

class Workflow extends Model
{

    use Filterable;

    protected $table = 'amministrazione__workflow';

    protected $fillable = [
      'user_id',
      'nodo_finale',
      'parent_id',
      'type',
      'azienda',
    ];

    public static function getRules()
    {
        return [
            'user_id' => 'integer|required',
            'nodo_finale' => 'required|boolean',
            'parent_id' => 'integer|required',
            'type' => 'integer|required',
            'azienda' => 'required',
        ];
    }

    public function superiore()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'user_id');
    }

    public function inferiore()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'parent_id');
    }

    public function scopeWeCom()
    {
        return $this->where('azienda', 'We-COM');
    }

    public function scopeDigit()
    {
        return $this->where('azienda', 'Digit Consulting');
    }

}
