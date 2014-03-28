<?php

class _LibraBasic extends Eloquent
{

	//protected $table = '';
	public $timestamps = false;

	// Doing these "backwards" because name_en is an integer, and en_name will be the object
	public function en_name() { return $this->hasOne('translations', 'id', 'name_en'); }
	public function ja_name() { return $this->hasOne('translations', 'id', 'name_ja'); }
	public function fr_name() { return $this->hasOne('translations', 'id', 'name_fr'); }
	public function de_name() { return $this->hasOne('translations', 'id', 'name_de'); }

	public function name() { return $this->{Config::get('language') . '_name'}(); }

}