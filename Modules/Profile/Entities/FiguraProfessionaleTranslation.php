<?php

namespace Modules\Profile\Entities;

use Illuminate\Database\Eloquent\Model;

class FiguraProfessionaleTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'profile__figuraprofessionale_translations';
}
