<?php

namespace App\Models;

use App\Models\Garland\Recipe;
use App\Models\Notebookdivision;
use Illuminate\Database\Eloquent\Model;

class Notebook extends Model
{
	public $table = 'notebook';

    public function notebookdivisions()
    {
    	return $this->belongsToMany(Notebookdivision::class, 'notebook_notebookdivision');
    }

    public function recipes()
    {
    	return $this->belongsToMany(Recipe::class, 'notebook_recipe', 'notebook_id', 'recipe_id');
    }
}
