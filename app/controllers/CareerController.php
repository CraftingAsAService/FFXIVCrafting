<?php

class CareerController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = Job::whereIn('disciple', array('DOL','DOH'))->get()->lists('name', 'abbreviation');

		return View::make('career')
			->with('active', 'career')
			->with('job_list', $job_list);
	}

	public function postProducer()
	{
		$my_class = Input::get('supporter-producer-class');
		$supported_classes = implode(',', Input::get('supporter-supported-classes'));
		$min_level = (int) Input::get('supporter-min-level') ?: 1;
		$max_level = (int) Input::get('supporter-max-level') ?: 70;

		return Redirect::to('/career/producer/' . implode('/', get_defined_vars()));
	}

	public function getProducer($my_class = '', $supported_classes = '', $min_level = 0, $max_level = 0)
	{
		# I am a  Carpenter  , what can I make to support  these 8 Classes  between levels  x and  y ?
		$supported_classes = explode(',', $supported_classes);

		$show_quests = false;//in_array($my_class, $supported_classes);

		if (empty($supported_classes))
			exit('No supported class selected... Todo: real error'); // TODO

		$supported_classes = Job::whereIn('abbreviation', $supported_classes)->get()->lists('id');

		if (empty($supported_classes))
			exit('No supported class recognized...'); // TODO

		$jobs = Job::whereIn('id', $supported_classes)->lists('name');
	
		$job = Job::where('abbreviation', $my_class)->first();

		if (empty($job))
			exit('No primary class recognized...'); // TODO

		DB::statement('SET SESSION group_concat_max_len=16384');

		$results = DB::table('recipes AS r')
			->select('*', DB::raw('SUM(cj.amount) AS amount'), DB::raw("(
					SELECT
						GROUP_CONCAT(DISTINCT CONCAT(v.name,'|',v.title,'|',IFNULL(vl.name, ''),'|',v.x,'|',v.y) ORDER BY vl.name SEPARATOR '***') AS vendors
					FROM `item_vendor` AS `iv` 
					JOIN `vendors` AS `v` ON `v`.`id` = `iv`.`vendor_id`
					LEFT JOIN `locations` AS `vl` ON `vl`.`id` = `v`.`location_id`
					WHERE `iv`.`item_id` = `I`.`id`
				) AS vendors"))
			->join('items AS i', 'i.id', '=', 'r.item_id')
			->join('careers AS c', 'c.identifier', '=', 'r.id')
			->join('career_job as cj', 'cj.career_id', '=', 'c.id')
			->whereBetween('c.level', array($min_level, $max_level))
			->where('r.job_id', $job->id)
			->where('c.type', 'recipe')
			->whereIn('cj.job_id', $supported_classes)
			->groupBy('r.id')
			->orderBy('c.level')
			->orderBy('i.ilvl')
			->having('amount', '>', '1')
			->get();

		// Cleanup result nodes & vendors
		foreach ($results as &$result)
		{
			// Vendors
			$result->vendor_count = 0;

			if ($result->vendors)
			{
				$new_vendors = array();
				
				foreach(explode('***', $result->vendors) as $vendor)
				{
					list($name, $title, $location, $x, $y) = explode('|', $vendor);

					$new_vendors[$location ?: 'Unknown'][] = (object) array(
						'name' => $name,
						'title' => $title,
						'x' => $x,
						'y' => $y
					);

					$result->vendor_count++;
				}

				ksort($new_vendors);

				$result->vendors = $new_vendors;
			}
		}
		unset($result);

		return View::make('career.production')
			->with(array(
				'recipies' => $results,
				'show_quests' => $show_quests,
				'jobs' => $jobs,
				'job' => $job,
				'min_level' => $min_level,
				'max_level' => $max_level
			));
	}

	public function postReceiver()
	{
		$my_class = Input::get('receiver-recipient-class');
		$supported_classes = implode(',', Input::get('receiver-producer-classes'));
		$min_level = (int) Input::get('receiver-min-level') ?: 1;
		$max_level = (int) Input::get('receiver-max-level') ?: 70;

		return Redirect::to('/career/receiver/' . implode('/', get_defined_vars()));
	}

	public function getReceiver($my_class = '', $supported_classes = '', $min_level = 0, $max_level = 0)
	{
		# I am a  Carpenter  , what should  these 8 Classes  make for me between levels  x and  y ?
		$supported_classes = explode(',', $supported_classes);

		$show_quests = false;//in_array($my_class, $supported_classes);

		if (empty($supported_classes))
			exit('No supported class selected... Todo: real error'); // TODO

		$supported_classes = Job::whereIn('abbreviation', $supported_classes)->get()->lists('id');

		if (empty($supported_classes))
			exit('No supported class recognized...'); // TODO

		$jobs = Job::whereIn('id', $supported_classes)->lists('name');
	
		$job = Job::where('abbreviation', $my_class)->first();

		if (empty($job))
			exit('No primary class recognized...'); // TODO

		DB::statement('SET SESSION group_concat_max_len=16384');

		$results = DB::table('career_job as cj')
			->select('*', DB::raw('SUM(cj.amount) AS amount'), DB::raw("(
					SELECT
						GROUP_CONCAT(DISTINCT CONCAT(v.name,'|',v.title,'|',IFNULL(vl.name, ''),'|',v.x,'|',v.y) ORDER BY vl.name SEPARATOR '***') AS vendors
					FROM `item_vendor` AS `iv` 
					JOIN `vendors` AS `v` ON `v`.`id` = `iv`.`vendor_id`
					LEFT JOIN `locations` AS `vl` ON `vl`.`id` = `v`.`location_id`
					WHERE `iv`.`item_id` = `I`.`id`
				) AS vendors"))
			->join('careers AS c', 'cj.career_id', '=', 'c.id')
			->join('recipes AS r', 'r.id', '=', 'c.identifier')
			->join('jobs AS j', 'j.id', '=', 'r.job_id')
			->join('items AS i', 'i.id', '=', 'r.item_id')
			->whereBetween('c.level', array($min_level, $max_level))
			->where('cj.job_id', $job->id)
			->where('c.type', 'recipe')
			->whereIn('r.job_id', $supported_classes)
			->groupBy('r.id')
			->orderBy('r.job_level')
			->having('amount', '>', '1')
			->get();

		// Cleanup result nodes & vendors
		foreach ($results as &$result)
		{
			// Vendors
			$result->vendor_count = 0;

			if ($result->vendors)
			{
				$new_vendors = array();
				
				foreach(explode('***', $result->vendors) as $vendor)
				{
					list($name, $title, $location, $x, $y) = explode('|', $vendor);

					$new_vendors[$location ?: 'Unknown'][] = (object) array(
						'name' => $name,
						'title' => $title,
						'x' => $x,
						'y' => $y
					);

					$result->vendor_count++;
				}

				ksort($new_vendors);

				$result->vendors = $new_vendors;
			}
		}
		unset($result);

		return View::make('career.receiver')
			->with(array(
				'recipies' => $results,
				'show_quests' => $show_quests,
				'jobs' => $jobs,
				'job' => $job,
				'min_level' => $min_level,
				'max_level' => $max_level
			));
	}

	public function postGathering()
	{
		$my_class = Input::get('gatherer-class');
		$supported_classes = implode(',', Input::get('gathering-supported-classes'));
		$min_level = (int) Input::get('gathering-min-level') ?: 1;
		$max_level = (int) Input::get('gathering-max-level') ?: 70;

		return Redirect::to('/career/gathering/' . implode('/', get_defined_vars()));
	}

	public function getGathering($my_class = '', $supported_classes = '', $min_level = 0, $max_level = 0)
	{
		$supported_classes = explode(',', $supported_classes);

		$show_quests = in_array($my_class, $supported_classes);

		if (empty($supported_classes))
			exit('No supported class selected... Todo: real error'); // TODO

		$supported_classes = Job::whereIn('abbreviation', $supported_classes)->get()->lists('id');

		if (empty($supported_classes))
			exit('No supported class recognized...'); // TODO

		$jobs = Job::whereIn('id', $supported_classes)->lists('name');
		
		if ($my_class != 'BTL')
			$job = Job::where('abbreviation', $my_class)->first();
		else
			$job = $my_class;

		if (empty($job))
			exit('No primary class recognized...'); // TODO

		$top_query = $inner_query = $join = $where = $union = $having = '';
		$parameters = array();

		DB::statement('SET SESSION group_concat_max_len=16384');

		if (in_array($my_class, array('MIN', 'BTN')))
		{
			$actions = $my_class == 'MIN' 
				? array('Mining','Quarrying') 
				: array('Harvesting','Logging');

			// Add Nodes
			$top_query .= "
					(
						SELECT 
							GROUP_CONCAT(DISTINCT CONCAT(gn.action,'|',gn.level,'|',gn.location_level,'|',gnl.name) ORDER BY gn.level , gn.location_level SEPARATOR '***') AS nodes
						FROM `gathering_node_item` AS `gni`
						JOIN `gathering_nodes` AS `gn` ON `gn`.`id` = `gni`.`gathering_node_id`
						JOIN `locations` AS `gnl` ON `gnl`.`id` = `gn`.`location_id`
						WHERE `gn`.`action` in (" . str_pad('', count($actions) * 2 - 1, '?,') . ")
							AND `gni`.`item_id` = `x`.`id`
					) AS nodes,
			";

			$parameters = array_merge($parameters, $actions);

			$having = "HAVING nodes != ''";
		} else {
			// Battling or Fishing
			$join = "LEFT JOIN `gathering_node_item` AS `gni` ON `gni`.`item_id` = `i`.`id`";

			// FSH where the item is "seafood"
			// BTL where the item is not "seafood"
			$where = "AND `i`.`role` " . ($my_class == 'BTL' ? '!' : '') . "= 'Seafood'";
			$where .= " AND `gni`.`id` IS NULL";
		}

		$parameters[] = $min_level;
		$parameters[] = $max_level;
		$parameters = array_merge($parameters, $supported_classes);

		if ($my_class != 'BTL')
		{
			$union = "
					UNION

					SELECT
						`i`.`id`, `i`.`name`, `i`.`icon`, `i`.`role`, `i`.`stack`, `i`.`buy`, qi.amount AS amount, 
						qi.level AS quest_level, qi.quality AS quest_quality
					FROM quest_items AS qi
					JOIN items AS i ON i.id = qi.item_id
					JOIN jobs AS j ON j.id = qi.job_id
					WHERE j.id = ?
						AND qi.level BETWEEN ? AND ?
			";
			
			$parameters[] = $job->id;
			$parameters[] = $min_level;
			$parameters[] = $max_level;
		}

		// TODO Caching

		$results = DB::select("
			SELECT x.*,
				" . $top_query . "
				(
					SELECT
						GROUP_CONCAT(DISTINCT CONCAT(v.name,'|',v.title,'|',IFNULL(vl.name, ''),'|',v.x,'|',v.y) ORDER BY vl.name SEPARATOR '***') AS vendors
					FROM `item_vendor` AS `iv` 
					JOIN `vendors` AS `v` ON `v`.`id` = `iv`.`vendor_id`
					LEFT JOIN `locations` AS `vl` ON `vl`.`id` = `v`.`location_id`
					WHERE `iv`.`item_id` = `x`.`id`
				) AS vendors
			FROM (
				SELECT 
					`i`.`id`, `i`.`name`, `i`.`icon`, `i`.`role`, `i`.`stack`, `i`.`buy`, SUM(cj.amount) AS amount,
					NULL AS quest_level, NULL AS quest_quality
				FROM `careers` AS `c`
				JOIN `items` AS `i` ON `i`.`id` = `c`.`identifier`
				JOIN `career_job` AS `cj` ON `cj`.`career_id` = `c`.`id`
				" . $join . "
				WHERE
					`c`.`type` = 'item'
					AND `c`.`level` BETWEEN ? AND ?
					AND `cj`.`job_id` in (" . str_pad('', count($supported_classes) * 2 - 1, '?,') . ")
					" . $where . "
				GROUP BY `c`.`identifier`

				" . $union . "
				
				ORDER BY `id` ASC
			) AS x
			" . $having, 
			$parameters
		);
		
		// Cleanup result nodes & vendors
		foreach ($results as &$result)
		{
			// Nodes
			if (isset($result->nodes) && $result->nodes)
			{
				$new_nodes = array();

				foreach (explode('***', $result->nodes) as $node)
				{
					list($action, $ilvl, $location_level, $location_name) = explode('|', $node);
					
					$result->ilvl = $ilvl;

					$new_nodes[$location_name][$action][] = $location_level;
				}

				foreach (array_keys($new_nodes) as $key)
					ksort($new_nodes[$key]);

				$result->nodes = $new_nodes;
			}

			// Vendors
			$result->vendor_count = 0;

			if ($result->vendors)
			{
				$new_vendors = array();
				
				foreach(explode('***', $result->vendors) as $vendor)
				{
					list($name, $title, $location, $x, $y) = explode('|', $vendor);

					$new_vendors[$location ?: 'Unknown'][] = (object) array(
						'name' => $name,
						'title' => $title,
						'x' => $x,
						'y' => $y
					);

					$result->vendor_count++;
				}

				ksort($new_vendors);

				$result->vendors = $new_vendors;
			}
		}
		unset($result);
		
		if ($my_class != 'BTL')
		{
			// Rip out Quest Entries
			foreach ($results as $k => $result)
				if ($result->quest_level != NULL)
				{
					$quest_results[] = $result;
					unset($results[$k]);
				}

			// Put them back in, either merge or insert
			if ($show_quests)
				foreach ($quest_results as $quest_item)
				{
					foreach($results as $k => $result)
					{
						if ($quest_item->id == $result->id)
						{
							// Merge
							$original_amount = $result->amount;
							$quest_amount = $quest_item->amount;
							$results[$k] = $quest_item;
							$results[$k]->amount = $original_amount;
							$results[$k]->quest_amount = $quest_amount;

							continue 2;
						}
					}

					// If a match was found it would have continued
					// This means at this point we add it in straight up
					$quest_item->quest_amount = $quest_item->amount;
					$results[] = $quest_item;
				}

			// Fishing doesn't have an ilvl...
			if ($my_class != 'FSH')
			{
				$sortable_results = array();
				foreach ($results as $row)
					$sortable_results[$row->ilvl][] = $row;
				ksort($sortable_results);

				$results = array();
				foreach($sortable_results as $ilvl => $rows)
					foreach ($rows as $row)
						$results[] = $row;
				unset($sortable_results);
			}
		}

		return View::make('career.items')
			->with(array(
				'items' => $results,
				'show_quests' => $show_quests,
				'jobs' => $jobs,
				'job' => $job,
				'min_level' => $min_level,
				'max_level' => $max_level
			));
	}

}