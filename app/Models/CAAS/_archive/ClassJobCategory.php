<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class ClassJobCategory extends _LibraBasic
{

	protected $table = 'classjob_category';

	public function classjob()
	{
		return $this->belongsToMany('App\Models\CAAS\ClassJob', 'classjob_classjob_category', 'classjob_category_id', 'classjob_id');
	}

}