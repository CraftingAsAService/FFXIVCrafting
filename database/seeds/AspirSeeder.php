<?php

// php artisan migrate:refresh
// php artisan db:seed --class=AspirSeeder

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AspirSeeder extends Seeder
{

	public function run()
	{
		// Setup, open the floodgates
		set_time_limit(0);
		Model::unguard();
		\DB::connection()->disableQueryLog();
		\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		$runList = [
			'achievement',
			'location',
			'node',
			'item_node',
			'fishing',
			'fishing_item',
		];

		foreach ($runList as $table)
		{
			// dd(file_get_contents(storage_path('app/aspir/' . $table . '.json')));
			$data = json_decode(file_get_contents(storage_path('app/aspir/' . $table . '.json')), true);
			$this->batchInsert($table, $data);
		}

		// Cleanup
		\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

	private function batchInsert($table, $rows)
	{
		$keys = array_keys(reset($rows));

		$updateKeys = [];
		foreach (array_slice($keys, 1) as $field)
			$updateKeys[] = '`' . $field . '`=VALUES(`' . $field . '`)';

		$keys = '(`' . implode('`,`', $keys) . '`)';
		$updateKeys = implode(', ', $updateKeys);

		foreach (array_chunk($rows, 300) as $batchID => $data)
		{
			$this->command->comment('Inserting ' . count($data) . ' rows for ' . $table . ' (' . ($batchID + 1) . ')');

			$values = $pdo = [];
			foreach ($data as $row)
			{
				$values[] = '(' . str_pad('', count($row) * 2 - 1, '?,') . ')';

				// Cleanup value, if FALSE set to NULL
				foreach ($row as $value)
					$pdo[] = $value === FALSE ? NULL : $value;
			}

			\DB::insert(
				'INSERT INTO ' . $table . ' ' . $keys .
				' VALUES ' . implode(',', $values) .
				' ON DUPLICATE KEY UPDATE ' . $updateKeys
			, $pdo);
		}
	}

}