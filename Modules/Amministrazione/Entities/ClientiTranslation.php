<?php

namespace Modules\Amministrazione\Entities;

use Illuminate\Database\Eloquent\Model;

class ClientiTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'amministrazione__clienti_translations';
}
