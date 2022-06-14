<?php

namespace Modules\Amministrazione\Entities;

use Illuminate\Database\Eloquent\Model;

class clientiIndirizziTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'amministrazione__clientiindirizzi_translations';
}
