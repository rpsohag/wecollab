<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;

class SegnalazioneOpportunitaTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'commerciale__segnalazioneopportunita_translations';
}
