<?php

class ClassJobCategory extends _LibraBasic
{

	protected $table = 'classjob_category';

	public function classjob()
	{
		return $this->hasMany('ClassJob');
	}

}