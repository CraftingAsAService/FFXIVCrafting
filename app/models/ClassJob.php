<?php

class ClassJob extends _LibraBasic
{

	protected $table = 'classjob';

	public function en_abbr() { return $this->hasOne('Translations', 'id', 'abbr_en'); }
	public function ja_abbr() { return $this->hasOne('Translations', 'id', 'abbr_ja'); }
	public function fr_abbr() { return $this->hasOne('Translations', 'id', 'abbr_fr'); }
	public function de_abbr() { return $this->hasOne('Translations', 'id', 'abbr_de'); }

	public function abbr() { return $this->{Config::get('language') . '_abbr'}(); }

	public static function get_name_abbr_list()
	{
		$list = array();
		$results = ClassJob::with('name', 'abbr')->get();

		foreach ($results as $row)
			$list[$row->abbr->term] = $row->name->term;

		return $list;
	}

	public static function get_id_abbr_list()
	{
		$list = array();
		$results = ClassJob::with('abbr')->get();

		foreach ($results as $row)
			$list[$row->abbr->term] = $row->id;

		return $list;
	}

	public static function get_by_abbr($abbr = '')
	{
		$all_jobs = ClassJob::with('en_abbr')->get();

		foreach ($all_jobs as $job)
			if ($job->en_abbr->term == $abbr)
				return $job;
		
		return false;
	}

	public static function get_abbr_list($ids = array())
	{
		$all_jobs = ClassJob::with('abbr')->whereIn('id', $ids)->get();

		$return = array();
		foreach ($all_jobs as $job)
			$return[] = $job->abbr->term;
		
		return $return;
	}

}