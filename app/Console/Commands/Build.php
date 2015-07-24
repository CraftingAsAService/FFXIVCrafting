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
	protected $description = 'Build the site for Docker usage';

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
	public function fire()
	{
		$tag = $this->argument('tag');
		$env = $this->option('qa') ? 'qa' : 'production';
		$php = $this->option('php') ? 'php' : 'hhvm';

// 		$this->info('QA? ' . ($env ? 'Yes' : 'No'));

// 		$this->info('PHP? ' . $use);

// 		$this->info('bye ' . $tag);

// 		return;

// 		$this->info('Display this on the screen');

// $this->error('Something went wrong!');

// $name = $this->ask('What is your name?');

// $password = $this->secret('What is the password?');


// $this->info('bye ' . $name . ' ' . $password . ' ' . $tag);


		$this->info('Prepare for Release, clear caches');

		exec($php . ' artisan cache:clear');
		exec($php . ' artisan route:clear');
		exec($php . ' artisan config:clear');
		exec($php . ' artisan view:clear');
		exec($php . ' artisan inspire');
		exec('cp .env.' . $env . ' .env');
		exec('composer update --no-dev -o');
		exec($php . ' artisan route:cache');
		exec($php . ' artisan config:cache');
		exec($php . ' artisan migrate');

		exec('mysqldump -u homestead -psecret ffxivcrafting > docker/caas.sql');
		exec('tar -zhcvf docker/caas-db.tar.gz docker/caas.sql');
		exec('rm docker/caas.sql');

		$exclude_from_tar = [
			'caas/.env.*',
			'caas/.git/*',
			'caas/node_modules/*',
			'caas/caas/*',
			'caas/docker/*',
			'caas/resources/assets/images/*',
			'caas/resources/assets/maps/*',
		];

		exec('tar --exclude="' . implode('" --exclude="', $exclude_from_tar) . '" -zhcvf docker/caas-web.tar.gz caas/');

		exec($php . ' artisan route:clear');
		exec($php . ' artisan config:clear');
		exec($php . ' artisan cache:clear');
		exec($php . ' artisan view:clear');
		exec('cp .env.local  .env');
		exec('composer update');

		if ($this->confirm('Ready to Tag and Push? [yes|no]'))
		{
			exec('git commit -a -m "' . $tag . ' Release"');
			exec('git tag ' . $tag . '');
			exec('git push --tags origin master');
		}

		$this->info('Done!');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['tag', InputArgument::REQUIRED, 'What to tag, like 3.0.1'],
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
		];
	}

}
