<?php

namespace Modules\Profile\Entities;

//use Astrotomic\Translatable\Translatable;
//

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Illuminate\Support\Carbon;


class Area extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'profile__aree';
    //public $translatedAttributes = [];
    protected $fillable = [
        'titolo',
        'procedura_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'titolo' => 'required',
            'procedura_id' => 'required',
        ];
    }


    public function users()
    {
        return $this->belongsToMany('Modules\User\Entities\Sentinel\User', 'profile__area_user');
    }

    public function procedura()
    {
        return $this->belongsTo('Modules\Profile\Entities\Procedura');
    }

    public function attivita()
    {
        return $this->hasMany('Modules\Profile\Entities\Gruppo');
    }

    public function timesheets()
    {
        return $this->hasMany('Modules\Tasklist\Entities\Timesheet');
    }

    public function timesheetsWithAllUsers()
    {
        return $this->hasMany('Modules\Tasklist\Entities\Timesheet')->withoutGlobalScope('user');
    }

    public function getTimesheetsDuration()
    {
        $timesheets = $this->timesheetsWithAllUsers;

        $duration = 0;

        foreach($timesheets as $timesheet) {

            $duration += $timesheet->durata_time();

        }

        $duration = get_seconds_to_his($duration);

        return $duration;
    }

    public function getTimesheetsGroups()
    {
        $timesheets = $this->timesheetsWithAllUsers;

        $allGroups = [];
        foreach($timesheets as $timesheet) {
            if(!in_array($timesheet->gruppo->nome, $allGroups)){
                $allGroups[] = $timesheet->gruppo->nome;
            }
        }

        return implode(", ", $allGroups);
    }

    public function getTimesheetsUsers()
    {
        $timesheets = $this->timesheetsWithAllUsers;

        $allUsers = [];
        foreach($timesheets as $timesheet) {
            if (!in_array($timesheet->created_user->full_name, $allUsers)) {
                $allUsers[] = $timesheet->created_user->full_name;
            }
        }

        return implode(", ", $allUsers);
    }
}
