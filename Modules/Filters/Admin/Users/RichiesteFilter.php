<?php

namespace Modules\Filters\Admin\Users;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class RichiesteFilter extends ModelFilter
{
	public function tipologia($tipologia)
	{
		if($tipologia >= 0)
			return $this->where('tipologia', $tipologia);
	}

	public function stato($stato)
	{
		if($stato >= 0)
			return $this->where('stato', $stato);
	}

	public function anno($anno)
	{
		if(!empty($anno))
			return $this->whereRaw("year(created_at)=$anno");
	}

	public function userFilter($utente)
	{
		if(!empty($utente) && $utente > 0)
			return $this->whereUserId($utente);
	}
}
