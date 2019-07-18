<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatabaseExport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'osmose:db:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export and zip the database into 7z files.';

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
		$this->info('Exporting Database into 7zip Files');

		$file_path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'generated-database' . DIRECTORY_SEPARATOR;
		$file_extension = '.sql.7z';

		$rm_command =
			'sudo rm ' . $file_path . '%s' . $file_extension . ' 2>/dev/null';

		$export_command =
			'sudo mysqldump -u ' . getenv('DB_USERNAME') . ' -p' . getenv('DB_PASSWORD') . ' %s ' . getenv('DB_DATABASE') .
			// We receive a warning that passwords in the command line are insecure.  Ignore it.
			' 2>/dev/null' .
			// Pipe the dump straight into 7zip
			' | 7z a -si -t7z ' . $file_path . '%s' . $file_extension . ' -mx9';

		foreach (['schema' => '--no-data', 'data' => '--no-create-info'] as $file => $option)
		{
			$this->comment('Compiling ' . $file . '.sql.7z');
			exec(sprintf($rm_command, $file));
			exec(sprintf($export_command, $option, $file));
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
