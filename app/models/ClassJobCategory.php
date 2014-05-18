<?php

class ClassJobCategory extends _LibraBasic
{

	protected $table = 'classjob_category';

	public function classjob()
	{
		return $this->belongsToMany('ClassJob', 'classjob_classjob_category', 'classjob_category_id', 'classjob_id');
	}

}