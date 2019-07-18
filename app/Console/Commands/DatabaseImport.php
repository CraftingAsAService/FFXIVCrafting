<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatabaseImport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'osmose:db:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import the database from the extracted sql files.';

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
		$this->info('Importing Database from SQL Files');

		$base_command =
			'sudo mysql -u ' . getenv('DB_USERNAME') . ' -p' . getenv('DB_PASSWORD') . ' ' . getenv('DB_DATABASE') . ' < ' .
			storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'generated-database' . DIRECTORY_SEPARATOR . '%s.sql'
			. ' 2>/dev/null'; // We receive a warning that passwords in the command line are insecure.  Suppress it.

		foreach (['schema', 'data'] as $file)
		{
			$this->comment('Executing ' . $file . '.sql');
			exec(sprintf($base_command, $file));
		}
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
