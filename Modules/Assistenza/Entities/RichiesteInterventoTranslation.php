<?php

namespace Modules\Assistenza\Entities;

use Illuminate\Database\Eloquent\Model;

class RichiesteInterventoTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'assistenza__richiesteintervento_translations';
}
