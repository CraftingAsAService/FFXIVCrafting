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

		$this->info('Prepare for Release, clear caches');

		$this->error($php . ' artisan cache:clear');
		$this->error($php . ' artisan route:clear');
		$this->error($php . ' artisan config:clear');
		$this->error($php . ' artisan view:clear');
		$this->error($php . ' artisan inspire');
		$this->error('cp .env.' . $env . ' .env');
		$this->error('composer update --no-dev -o');
		$this->error($php . ' artisan route:cache');
		$this->error($php . ' artisan config:cache');
		$this->error($php . ' artisan migrate');

		$this->error('mysqldump -u homestead -psecret ffxivcrafting > docker/caas.sql');
		$this->error('tar -zhcvf docker/caas-db.tar.gz docker/caas.sql');
		$this->error('rm docker/caas.sql');

		$exclude_from_tar = [
			'caas/.env.*',
			'caas/.git/*',
			'caas/node_modules/*',
			'caas/caas/*',
			'caas/docker/*',
			'caas/resources/assets/images/*',
			'caas/resources/assets/maps/*',
		];

		$this->error('tar --exclude="' . implode('" --exclude="', $exclude_from_tar) . '" -zhcvf docker/caas-web.tar.gz caas/');

		$this->error($php . ' artisan route:clear');
		$this->error($php . ' artisan config:clear');
		$this->error($php . ' artisan cache:clear');
		$this->error($php . ' artisan view:clear');
		$this->error('cp .env.local  .env');
		$this->error('composer update');

		if ($this->confirm('Ready to Tag and Push? [yes|no]'))
		{
			$this->error('git commit -a -m "' . $tag . ' Release"');
			$this->error('git tag ' . $tag . '');
			$this->info('RUN THIS: git push --tags origin master');
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
