<?php

namespace Modules\Profile\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use EloquentFilter\Filterable;

class Profile extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'profile__profiles';
    // public $translatedAttributes = [];

    protected $casts = [
        'partner' => 'string'
    ];

    protected $fillable = [
        'user_id',
        'titolo',
        'matricola',
        'badge',
        'username',
        'incarico',
        'data_assunzione',
        'fine_contratto',
        'titolo_di_studio',
        'tipologia_di_contratto',
        'codice_fiscale',
        'data_di_nascita',
        'comune_di_nascita',
        'provincia_di_nascita',
        'indennita_giornaliera',
        'cognome_cedolino',
        'interno',
        'num_telefono_aziendale',
        'num_telefono_personale',
        'tipo_collaborazione',
        'avvisi_task',
        'rendicontabile',
        'aree',
        'partner',
        'azienda',
		'approvatori_fpm',
		'approvatori_rimborsi',
		'visualizzatori',
		'sede_partenza',
		'indennita_pernottamento',
		'ral'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public static function getRules($user_id = null, $profile_id = null)
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'profile' => 'nullable',
            'email' => 'required|email|unique:users,email,'.$user_id,
            'password' => 'nullable|confirmed|min:8|regex:"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\~\!\@\#\$\%\^\&\*\(\)\+\=\[\{\]\}\\\'\<\,\.\>\?\/\\"\"\;\:\°\à\è\é\ì\ò\ù\§\ç\_\-\£])[A-Za-z\d\~\!\@\#\$\%\^\&\*\(\)\+\=\[\{\]\}\\\'\<\,\.\>\?\/\\"\"\;\:\°\à\è\é\ì\ò\ù\§\ç\_\-\£]{8,}"',
            'profile.username' => 'required|unique:profile__profiles,username,'.$profile_id
        ];
    }

    public function getDataAssunzioneAttribute($value)
    {
        return get_date_ita($value);
    }

    public function setDataAssunzioneAttribute($value)
    {
        $this->attributes['data_assunzione'] = set_date_ita($value);
    }

    public function getFineContrattoAttribute($value)
    {
        return get_date_ita($value);
    }

    public function setFineContrattoAttribute($value)
    {
        $this->attributes['fine_contratto'] = set_date_ita($value);
    }

    public function getIndennitaGiornalieraAttribute($value)
    {
        return get_currency($value);
    }

	public function getRalAttribute($value)
    {
        return get_currency($value);
    }

	public function getIndennitaPernottamentoAttribute($value)
    {
        return get_currency($value);
    }

    public function getDataDiNascitaAttribute($value)
    {
        return get_date_ita($value);
    }

    public function setDataDiNascitaAttribute($value)
    {
        $this->attributes['data_di_nascita'] = set_date_ita($value);
    }

    public function setIndennitaGiornalieraAttribute($value)
    {
        $this->attributes['indennita_giornaliera'] = clean_currency($value);
    }

	public function setIndennitaPernottamentoAttribute($value)
    {
        $this->attributes['indennita_pernottamento'] = clean_currency($value);
    }

	public function setRalAttribute($value)
    {
        $this->attributes['ral'] = clean_currency($value);
    }

    public function setAreeAttribute($values)
    {
        $this->attributes['aree'] = set_check_json($values);
    }

    public function setPartnerAttribute($values)
    {
        $this->attributes['partner'] = json_encode($values);
    }

    /**
     * Check if the current user is activated
     * @return bool
     */
    public function isActivated()
    {
        if (Activation::completed($this->user()))
        {
            return true;
        }
        return false;
    }

    public function user()
    {
        $driver = config('asgard.user.config.driver');

        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Profile\UserFilter::class);
    }

    // public function aree()
    // {
    //     return $this->belongsToMany('Modules\Profile\Entities\Profile', 'profile__area_user');
    // }
}
