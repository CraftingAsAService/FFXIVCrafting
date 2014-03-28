<?php

class ClassJob extends _LibraBasic
{

	protected $table = 'classjob';

	public function abbr_en() { return $this->hasOne('translations', 'id', 'abbr_en'); }
	public function abbr_ja() { return $this->hasOne('translations', 'id', 'abbr_ja'); }
	public function abbr_fr() { return $this->hasOne('translations', 'id', 'abbr_fr'); }
	public function abbr_de() { return $this->hasOne('translations', 'id', 'abbr_de'); }

	public function abbr() { return $this->{'abbr_' . Config::get('language')}(); }

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
		$all_jobs = ClassJob::with('abbr')->get();

		foreach ($all_jobs as $job)
			if ($job->abbr->term == $abbr)
				return $job;
		
		return false;
	}

}