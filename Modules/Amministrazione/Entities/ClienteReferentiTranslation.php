<?php

namespace Modules\Amministrazione\Entities;

use Illuminate\Database\Eloquent\Model;

class ClienteReferentiTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'amministrazione__clientereferenti_translations';
}
