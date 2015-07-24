<?php

namespace App\Models\Osmose;

use Illuminate\Database\Eloquent\Model;

class AppData extends Model {
	
	protected
		$table = 'app_data',
		$connection = 'libra';
	public $timestamps = false;

}