<?php

namespace App\Models\Osmose;

// How often will you be crafting each recipe, if you make one of everything?
//   Future note, be sure to mark leves and quests
// By extension, how many end-materials will need to be gathered?
class Career 
{

	public $recipes = array(),
			$item_to_recipe = array();

	public $career_recipes = array(),
			$career_reagents = array();

	public function run()
	{
		foreach(FileHandler::get_data('Recipe') as $r)
		{
			$r->reagents = array();
			$this->recipes[$r->id] = $r;
			
			$this->item_to_recipe[$r->item_id][] = $r->id;

			foreach ($r->extra->reagents as $item_id => $amount)
				$this->recipes[$r->id]->reagents[] = (object) array('item_id' => $item_id, 'amount' => $amount);
		}

		foreach ($this->recipes as $r)
			$this->recursive($r, $r->level, $r->classjob, $r->yields);

		list($careers, $career_job) = $this->compile();

		FileHandler::save('careers', $careers);
		FileHandler::save('career_classjob', $career_job);
	}

	public function recursive($recipe = array(), $parent_level = 0, $parent_class = '', $make_this_many = 0, $level = 0)
	{
		##echo str_pad('', $level * 2, "\t") . 'MAKING ' . ($make_this_many / $recipe->yields) . ' (' . $make_this_many . '/' . $recipe->yields . ') ' . $recipe->name . "\n";
		// For recipe W, At level X, to fulfil a Y class objective, make this many.
		// If I only need one item, but the recipe makes more than that, do the division.
		@$this->career_recipes[$recipe->id][$parent_level][$parent_class] += $make_this_many / $recipe->yields;

		// Loop through the reagents
		// Either add treat it as a recipe or add them to career reagents
		foreach ($recipe->reagents as $reagent)
			// Recipe
			if (isset($this->item_to_recipe[$reagent->item_id]))
			{
				// Loop through the item's recipe. If the recipe is made in multiple (like bronze ingot for BSM/ARM), divide by two, because it will be reported on both. (or three, four, etc);
				foreach($this->item_to_recipe[$reagent->item_id] as $reagent_recipe_id)
				{
					##echo str_pad('', ($level + 1) * 2, "\t") . 'LOOKING ' . ($reagent->amount * $make_this_many / $recipe->yields / count($this->item_to_recipe[$reagent->item_id])) . ' (' . $reagent->amount . '*' . $make_this_many . '/' . $recipe->yields . '/' . count($this->item_to_recipe[$reagent->item_id]) . ') ' . $this->recipes[$reagent_recipe_id]->name . "\n";
					$this->recursive($this->recipes[$reagent_recipe_id], $parent_level, $parent_class, $reagent->amount * $make_this_many / $recipe->yields / count($this->item_to_recipe[$reagent->item_id]), $level + 1);
				}
			}
			// Reagent
			else
			{
				##echo str_pad('', ($level + 1) * 2, "\t") . 'ADDING ' . ($reagent->amount * $make_this_many / $recipe->yields) . ' (' . $reagent->amount . '*' . $make_this_many . '/' . $recipe->yields . ') ' . $reagent->item_id . "\n";
				// For item W, at level X, to fulfil a Y class objective, gather this many
				@$this->career_reagents[$reagent->item_id][$parent_level][$parent_class] += $reagent->amount * $make_this_many / $recipe->yields;
			}
	}

	public function compile()
	{
		$career_id = 0;
		$careers = $career_job = array();
		foreach (array('recipe' => 'career_recipes', 'item' => 'career_reagents') as $type => $key)
		{
			$data =& $this->$key;

			foreach ($data as $identifier => $i)
				foreach ($i as $level => $j)
				{
					$careers[] = array(
						'id' => ++$career_id,
						'type' => $type,
						'identifier' => $identifier,
						'level' => $level
					);

					foreach ($j as $class => $amount)
					{
						$career_job[] = array(
							'career_id' => $career_id,
							'classjob_id' => $class,
							'amount' => $amount
						);
					}
				}
		}

		return array($careers, $career_job);
	}

}