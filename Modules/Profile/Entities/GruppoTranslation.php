<?php

namespace Modules\Profile\Entities;

use Illuminate\Database\Eloquent\Model;

class GruppoTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'profile__gruppo_translations';
}
