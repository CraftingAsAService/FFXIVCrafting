<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notebookdivision extends Model
{
	public $table = 'notebookdivision';

    public function categories()
    {
    	return $this->belongsTo(NotebookdivisionCategories::class, 'category_id');
    }

    public function notebooks()
    {
    	return $this->belongsToMany(Notebook::class, 'notebook_notebookdivision', 'notebook_id', 'notebookdivision_id');
    }
}
