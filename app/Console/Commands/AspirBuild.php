<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AspirBuild extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'aspir:build-db';

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
		echo exec('mysqldump -u root -ppassword ffxivcrafting > ../cactuar/caas.sql');
	}

}
