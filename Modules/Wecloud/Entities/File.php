<?php

namespace Modules\Wecloud\Entities;

use Illuminate\Database\Eloquent\Model;

use EloquentFilter\Filterable;

class File extends Model
{
    use Filterable;

    protected $table = 'wecloud__files';

    protected $fillable = ['name', 'value', 'uploaded_user_id'];

    public function getCreatedAtAttribute($value)
    {
        return get_date_hour_ita($value);
    }

    public function getUpdatedAtAttribute($date)
    {
        return get_date_hour_ita($date);
    }

    public function user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'uploaded_user_id');
    }

    public function getValueAttribute($value)
    {
        return json_decode($value);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Wecloud\FileFilter::class);
    }

}
