<?php

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		Eloquent::unguard();

		$this->call('StatTableSeeder');

		$this->call('JobTableSeeder');

		$this->call('EquipementTypeTableSeeder');

		$this->call('DataTablesSeeder');
	}

}

class StatTableSeeder extends Seeder
{

	public function run()
	{
		$stats = array(
			'Gathering',
			'Perception',
			'GP',
			'Craftsmanship',
			'Control',
			'CP',
		);

		foreach ($stats as $stat)
			Stat::create(array(
				'name' => $stat
			));
	}
	
}

class JobTableSeeder extends Seeder
{

	public function run()
	{
		$jobs = array(
			'DOH' => 'Disciple of the Hand',

			'BSM' => 'Blacksmith',
			'GSM' => 'Goldsmith',
			'ARM' => 'Armourer',
			'CRP' => 'Carpenter',
			'LTW' => 'Leatherworker',
			'WVR' => 'Weaver',
			'ALC' => 'Alchemist',
			'CUL' => 'Culinarian',

			'DOL' => 'Disciple of the Land',

			'FSH' => 'Fisher',
			'BTN' => 'Botanist',
			'MIN' => 'Miner',

			'DOW' => 'Disciple of War',

			

			'DOM' => 'Disciple of Magic',
		);

		foreach ($jobs as $abbr => $job)
			Job::create(array(
				'abbreviation' => $abbr,
				'name' => $job
			));
	}
	
}

class EquipementTypeTableSeeder extends Seeder
{

	public function run()
	{
		$types = array(
			'Primary',
			'Secondary',
			'Head',
			'Body',
			'Hands',
			'Waist',
			'Legs',
			'Feet',
			'Neck',
			'Ear',
			'Wrist',
			'Ring',
			'Ring'
		);

		foreach ($types as $key => $type)
			EquipmentType::create(array(
				'name' => $type,
				'rank' => $key
			));
	}
	
}

class DataTablesSeeder extends Seeder
{

	public
		$stats = array(),
		$jobs = array(),
		$types = array();


	public function run()
	{
		// Get stats, jobs and equipment types
		foreach (Stat::all() as $stat)
			$this->stats[$stat->name] = $stat->id;

		foreach (Job::all() as $job)
			$this->jobs[$job->abbreviation] = $job->id;

		foreach (EquipmentType::all() as $type)
			$this->types[$type->name] = $type->id;

		// Import Materia file
		$this->materia($this->tsv(storage_path() . '/raw-data/materia.txt'));

		// Import Food file
		$this->food($this->tsv(storage_path() . '/raw-data/food.txt'));

		// Import Crafting Equipment file
		$this->crafting($this->tsv(storage_path() . '/raw-data/crafting-equipment.txt'));

		// Import Gathering Equipment file
		$this->gathering($this->tsv(storage_path() . '/raw-data/gathering-equipment.txt'));
	}

	/**
	 * TSV - Tab Seperated Values
	 *  Open a file and prep it into an array
	 */
	private function tsv($filepath = '')
	{
		// Get the file contents
		$contents = file_get_contents($filepath);

		// Remove any \r's.  Explode on \n.
		$contents = explode("\n", preg_replace("/\r/", "", $contents));
		
		// Remove the header information
		// Storing the header just incase we need the info after we parse
		$this->header = array_shift($contents);

		// Footer might be blank too
		if (end($contents) == "")
			array_pop($contents);

		// Break out each row
		$return = array();
		foreach ($contents as $row)
			$return[] = explode("\t", $row);

		return $return;
	}

	private function materia($data = array())
	{
		foreach ($data as $row)
		{
			list($job, $name, $stat, $amount) = $row;

			Materia::create(array(
				'job_id' => $this->jobs[$job],
				'name' => $name,
				'stat_id' => $this->stats[$stat],
				'amount' => $amount
			));
		}
	}

	private function food($data = array())
	{
		foreach ($data as $row)
		{
			list($job, $a_stat, $b_stat, $name, $a_percent, $a_maximum, $b_percent, $b_maximum) = $row;
			
			// Base Food Item
			$food = Food::create(array(
				'job_id' => $this->jobs[$job],
				'name' => $name
			));

			$stats = array();

			// Food Stats
			foreach (array('a', 'b') as $letter)
				if (${$letter . '_stat'})
				{
					$stats[$this->stats[${$letter . '_stat'}]] = array(
						'percent' => ${$letter . '_percent'},
						'maximum' => ${$letter . '_maximum'}
					);
				}

			if ( ! empty($stats))
				$food->stats()->sync($stats);
		}
	}

	private function crafting($data = array())
	{
		$this->_equipment($data, array('Craftsmanship', 'Control', 'CP'));
	}

	private function gathering($data = array())
	{
		$this->_equipment($data, array('Gathering', 'Perception', 'GP'));
	}

	private function _equipment($data = array(), $stat_order = array())
	{
		foreach ($data as $row)
		{
			list($job, $type, $level, $name, $origin, $stat_0, $stat_1, $stat_2, $materia, $comments) = $row;

			// Base Item
			$equipment = Equipment::create(array(
				'job_id' => $this->jobs[$job],
				'type_id' => $this->types[$type],
				'name' => $name,
				'level' => $level,
				'origin' => $origin,
				'materia' => $materia,
				'comments' => $comments
			));

			$stats = array();

			// Equipment Stats
			foreach ($stat_order as $key => $stat)
				if (${'stat_' . $key})
				{
					$stats[$this->stats[$stat]] = array(
						'amount' => ${'stat_' . $key}
					);
				}

			if ( ! empty($stats))
				$equipment->stats()->sync($stats);

		}
	}

}