<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;

class OrdinativoTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'commerciale__ordinativo_translations';
}
