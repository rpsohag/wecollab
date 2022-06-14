<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;

class OffertaTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'commerciale__offerta_translations';
}
