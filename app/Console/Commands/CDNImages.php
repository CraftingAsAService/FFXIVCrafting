<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CDNImages extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'osmose:cdn:images';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'CDN Image Publish Tool';

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
		$this->info('Starting Image CDN Publish');
		
		$client = new \OpenCloud\Rackspace('https://identity.api.rackspacecloud.com/v2.0/', [
			'username' => env('RACKSPACE_USERNAME'),
			'apiKey' => env('RACKSPACE_API_KEY')
		]);

		$objectStoreService = $client->objectStoreService(null, 'ORD');
		$container = $objectStoreService->getContainer('CAAS_Assets');
		// There's more than 10,000 items
		// http://docs.php-opencloud.com/en/latest/services/object-store/objects.html?highlight=objectlist#list-over-10-000-objects
		
		$existing_files = [];
		$marker = '';

		$this->info('Pulling existing images. Each dot represents 100 objects.');

		while ($marker !== null)
		{
			echo '.';
			
			$objects = $container->objectList(['marker' => $marker]);
			$total = $objects->count();
			$count = 0;

			if ($total == 0)
				break;

			foreach ($objects as $object)
			{
				$existing_files[] = $object->getName();
				if (++$count == $total)
				{
					$marker = $object->getName();
					break;
				}
				else
					$marker = null;
			}
		}

		echo "\n";

		// Only show this if we're publishing
		$this->info(count($existing_files) . ' files found');

		// Recursively go through Images.
		// Get the md5 of the contents.  
		// If it's already in the deleted block, remove it from that array, no action
		// Otherwise, prepare the new filename
		//   and upload that file as the new filename into the cdn

		$files_to_upload = array();

		$tally = 0;
		chdir('../garlanddeploy/db/icons');
		// This only gets two levels deep, but is good enough.
		foreach (array_merge(glob('*', GLOB_ONLYDIR),glob('*/*', GLOB_ONLYDIR)) as $ext)
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($ext)) as $filename)
			{
				// Ignore '..' or '.' directories.
				if (in_array(substr($filename, -2), array('..', '\.', '/.'))) // \. for windows, /. for linux
					continue;

				$original_file = $filename->getPathname();
				$save_as = $ext . '/' . $filename->getFilename();

				// Upload this file
				if ( ! in_array($save_as, $existing_files))
					$files_to_upload[] = array(
						'name' => $save_as,
						'path' => $original_file
					);
			}

		$this->info('Uploading ' . count($files_to_upload) . ' files.');

		// Upload files individually.  Multiple upload fails too much.
		foreach ($files_to_upload as $file)
		{
			// Protect against japanese filenames
			$is_japanese = preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $file['name']);
			if ($is_japanese)
			{
				$this->comment('Skipping ' . $file['name'] . '; Japanese characters.');
				continue;
			}
			
			$this->info('Uploading ' . $file['name']);

			try {
				$container->uploadObject($file['name'], fopen($file['path'], 'r+'));
			} catch (\Guzzle\Http\Exception\CurlException $e) {
				exec('echo -ne \'\007\'');
				$this->error('CURL Failure');
				exit;
			}
		}
		
		// $batch_amount = 50;

		// $this->info('Uploading ' . count($files_to_upload) . ' files in batches of ' . $batch_amount . '.');
		
		// Upload in batches
		// foreach (array_chunk($files_to_upload, $batch_amount) as $files)
		// {
		// 	$this->info('Uploading ' . count($files) . ' files.');
		// 	$container->uploadObjects($files);
		// }

		$this->info('cdn images publish finished');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}
