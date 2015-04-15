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
		foreach (array('items/nq', 'items/hq') as $ext)
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('resources/assets/images/' . $ext)) as $filename)
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

		$this->info('Uploading ' . count($files_to_upload) . ' files in batches of 100.');
		
		// Upload in batches
		foreach (array_chunk($files_to_upload, 100) as $files)
		{
			$this->info('Uploading ' . count($files) . ' files.');
			$container->uploadObjects($files);
		}

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
