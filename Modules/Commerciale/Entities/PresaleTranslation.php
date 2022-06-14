<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;

class CensimentoClienteTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'commerciale__censimentocliente_translations';
}
