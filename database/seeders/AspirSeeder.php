<?php

namespace Database\Seeders;

// php artisan aspir:migrate
//  OR
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

		// Run List is the same as what exists in the Aspir model's $data variable as keys
		$aspir = new \App\Models\Aspir\Aspir($this);
		$runList = array_keys($aspir->data);

		foreach ($runList as $table)
		{
			$data = json_decode(file_get_contents(storage_path('app/aspir/' . $table . '.json')), true);
			if ( ! empty($data))
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
		$ignore = $updateKeys ? '' : 'IGNORE';

		foreach (array_chunk($rows, 500) as $batchID => $data)
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
				'INSERT ' . $ignore . ' INTO ' . $table . ' ' . $keys .
				' VALUES ' . implode(',', $values) .
				($ignore ? '' : ' ON DUPLICATE KEY UPDATE ' . $updateKeys)
			, $pdo);
		}
	}

}