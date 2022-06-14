<?php

namespace Modules\Wecore\Entities;

use Illuminate\Database\Eloquent\Model;

class Metagable extends Model
{
    protected $table = 'wecore__metagable';

    protected $fillable = ['meta_id', 'metagable_id', 'metagable_type'];


    public function metas()
    {
        return $this->hasMany('Modules\Wecore\Entities\Meta');
    }
}
