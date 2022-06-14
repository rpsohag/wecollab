<?php

namespace Modules\Wecore\Entities;

use Illuminate\Database\Eloquent\Model;

class CoreTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'wecore__core_translations';
}
