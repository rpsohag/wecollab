<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;

class FatturazioneTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'commerciale__fatturazione_translations';
}
