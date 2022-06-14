<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Entities\Richieste;

class Approvazioni extends Model
{
    protected $table = 'richieste__approvazioni';

    protected $fillable = [
		'id', 
		'richiesta_id', 
		'approvatore_id',
		'stato'
	];

	public function user()
    {
        return $this->hasOne(User::class,'id','approvatore_id');
    }

	public function richiesta()
    {
        return $this->hasOne(Richieste::class,'id','richiesta_id');
    }
}