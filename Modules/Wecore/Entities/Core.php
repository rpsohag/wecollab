<?php

namespace Modules\Wecore\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Core extends Model
{
    use Translatable;

    protected $table = 'wecore__cores';
    public $translatedAttributes = [];
    protected $fillable = [];
}
