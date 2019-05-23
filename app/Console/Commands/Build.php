<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Build extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'build';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export DB to Ultros Folder';

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
		\DB::table('sessions')->truncate();
		\DB::table('cache')->truncate();

		$this->info('Exporting MySQL Database');
		echo exec('mysqldump -u homestead -psecret ffxivcrafting > ../ultros/caas.sql');
	}

	/**
	 * Run commands to reset the instance back to normal
	 */
	public function reset($php)
	{
		$this->info('Resetting back to normal, clearing caches again');
		echo exec($php . ' artisan route:clear') . PHP_EOL;
		echo exec($php . ' artisan config:clear') . PHP_EOL;
		echo exec($php . ' artisan cache:clear') . PHP_EOL;

		$this->info('Switching Environment to Local');
		echo exec('cp .env.local  .env') . PHP_EOL;

		$this->info('Updating Composer for Development');
		echo exec('composer update') . PHP_EOL;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			// ['tag', InputArgument::REQUIRED, 'What to tag, like 3.0.1'],
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
			['php', null, InputOption::VALUE_NONE, 'Use PHP instead of HHVM', null],
			['qa', null, InputOption::VALUE_NONE, 'Run for a QA build over Production', null],
			['reset', null, InputOption::VALUE_NONE, 'Jump to the reset and exit', null],
		];
	}

}
