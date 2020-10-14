<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatabaseExtract extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'osmose:db:extract';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Extracts the database files.  Requires p7zip-full.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->info('Extracting Database');

		$path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'generated-database' . DIRECTORY_SEPARATOR;
		$file_base = $path . '%s.sql.7z';

		foreach (['schema', 'data'] as $file_name)
			// 7z /path/to/filename.sql.7z -o/path/to -y
			exec('sudo 7z e ' . sprintf($file_base, $file_name) . ' -o' . $path . ' -y');

		foreach (['schema', 'data'] as $file_name)
			if (is_file(substr(sprintf($file_base, $file_name), 0, -3)))
				$this->info($file_name . '.sql was extracted.');

		$this->comment('Run `php artisan osmose:db:import` next.');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [

		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [

		];
	}

}
