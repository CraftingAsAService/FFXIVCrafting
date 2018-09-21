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
	protected $description = 'Export DB to K8S-Cluster repository';

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
		// $tag = $this->argument('tag');
		// $env = $this->option('qa') ? 'qa' : 'production';
		// $php = $this->option('php') ? 'php' : 'hhvm';

		// if ($this->option('reset'))
		// 	return $this->reset($php);

		// $latest_tag = exec('git tag');

		// $tmp = explode('.', $latest_tag);
		// $tmp[count($tmp) - 1] = end($tmp) + 1;

		// $new_tag = implode('.', $tmp);

		// $this->info('Latest tag is ' . $latest_tag);
		// $this->info('Suggested tag is ' . $new_tag);
		// $tag = $this->ask('Name this branch: (`enter` to use default)', $new_tag);

		\DB::table('sessions')->truncate();
		\DB::table('cache')->truncate();

		$this->info('Exporting MySQL Database');
		echo exec('mysqldump -u homestead -psecret ffxivcrafting > ../ffxiv-config/caas-db/caas.sql');

		// $this->info('Clearing caches');
		// echo exec($php . ' artisan cache:clear') . PHP_EOL;
		// echo exec($php . ' artisan route:clear') . PHP_EOL;
		// echo exec($php . ' artisan config:clear') . PHP_EOL;
		// echo exec($php . ' artisan view:clear') . PHP_EOL;
		// echo exec($php . ' artisan inspire') . PHP_EOL;

		// $this->info('Switching Environment to ' . strtoupper($env));
		// echo exec('cp .env.' . $env . ' .env');

		// $this->info('Updating an Optimized/NoDev Composer');
		// echo exec('composer update --no-dev -o') . PHP_EOL;

		// $this->info('Caching!');
		// echo exec($php . ' artisan route:cache') . PHP_EOL;
		// echo exec($php . ' artisan config:cache') . PHP_EOL;

		// $this->info('Creating Tarball');

		// $exclude_from_tar = [
		// 	'caas/.env.*',
		// 	'caas/.git/*',
		// 	'caas/node_modules/*',
		// 	'caas/caas/*',
		// 	'caas/docker/*',
		// 	'caas/resources/assets/*',
		// 	'caas/storage/app/osmose/*',
		// ];

		// exec('tar --exclude="' . implode('" --exclude="', $exclude_from_tar) . '" -zhcvf docker/caas-web.tar.gz caas/') . PHP_EOL;

		// $this->reset($php);

		// if ($this->confirm('Ready to Tag and Push? [yes|no]'))
		// {
		// 	echo exec('git commit -a -m "' . $tag . ' Release"') . PHP_EOL;
		// 	echo exec('git tag ' . $tag . '') . PHP_EOL;
		// 	$this->info('RUN THIS: git push --tags origin master');
		// }

		// // TODO, push the DB repo??
		// // if ($this->confirm('Ready to Tag and Push? [yes|no]'))
		// // {
		// // 	echo exec('git commit -a -m "' . $tag . ' Release"') . PHP_EOL;
		// // 	echo exec('git tag ' . $tag . '') . PHP_EOL;
		// // 	$this->info('RUN THIS: git push --tags origin master');
		// // }

		// $this->info('Done!');
		$this->info('Consider running osmose:cdn:assets!');
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
