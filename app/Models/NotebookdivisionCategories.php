<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotebookdivisionCategories extends Model
{
	public $table = 'notebookdivision_category';

    public function divisions()
    {
    	return $this->hasMany(Notebookdivision::class, 'category_id');
    }
}
