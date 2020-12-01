<?php

namespace App\Models\Garland;

use App\Models\Notebook;
use App\Models\Notebookdivision;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model {

	protected $table = 'recipe';

	public function reagents()
	{
		return $this->belongsToMany('App\Models\Garland\Item', 'recipe_reagents')->withPivot('amount');
	}

	public function item()
	{
		return $this->belongsTo('App\Models\Garland\Item');
	}

	public function job()
	{
		return $this->belongsTo('App\Models\Garland\Job');
	}

	public function career()
	{
		return $this->hasMany('App\Models\Garland\Career', 'identifier');
	}

	public function notebooks()
	{
		return $this->belongsToMany(Notebook::class, 'notebook_recipe', 'recipe_id', 'notebook_id')->withPivot('slot');
	}

}
