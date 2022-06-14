<?php

namespace Modules\Profile\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utente extends Model
{
    use SoftDeletes;
    
    protected $table = 'users';

    public function aree()
    {
        return $this->belongsTo('Modules\Profile\Entities\Area', 'profile__area_user');
    }

}
