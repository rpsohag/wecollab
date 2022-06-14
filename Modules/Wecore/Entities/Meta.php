<?php

namespace Modules\Wecore\Entities;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Meta extends Model
{
    use Filterable;

    protected $table = 'wecore__metas';

    protected $fillable = ['name', 'value', 'created_user_id', 'updated_user_id'];

    public function getCreatedAtAttribute($value)
    {
        return get_date_hour_ita($value);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\DocumentiFilter::class);
    }

    public function getUpdatedAtAttribute($date)
    {
        return get_date_hour_ita($date);
    }

    public function getValueAttribute($value)
    {
        return (is_json($value) ? json_decode($value) : $value);
    }

    public function metagable()
    {
        return $this->belongsTo('Modules\Wecore\Entities\Metagable', 'id', 'meta_id');
    }

    public function createdUser()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id')->withTrashed();
    }
    public function updatedUser()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id')->withTrashed();
    }

    public function clienti()
    {
        return $this->morphedByMany('Modules\Amministrazione\Entities\Clienti', 'metagable', 'wecore__metagable');
    }

    public function offerte()
    {
        return $this->morphedByMany('Modules\Commerciale\Entities\Offerta', 'metagable', 'wecore__metagable');
    }
}
