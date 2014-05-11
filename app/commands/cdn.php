<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class cdn extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cdn';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'CDN Commands';

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
		$action = $this->argument('action');

		$publish = $action == 'publish';
		$delete = ! $publish;

		$this->info('Starting cdn ' . $action);
		// Get all files from the CDN, store their names.  We'll be deleting those at the end.
		
		// Get our secret key
		// File kept out of repository.  Contents: <?php return 'whatever_the_key_is';
		$secret_key = include('secret_api_key.php');
		
		$client = new \OpenCloud\Rackspace('https://identity.api.rackspacecloud.com/v2.0/', array(
			'username' => 'nicholas.wright',
			'apiKey' => $secret_key
		));

		$objectStoreService = $client->objectStoreService(null, 'ORD');
		$container = $objectStoreService->getContainer('CAAS');
		$objects = $container->objectList();
		$existing_files = [];
		foreach ($objects as $object)
			$existing_files[] = $object->getName();

		// Only show this if we're publishing
		if ($publish)
			$this->info(count($existing_files) . ' files found');

		// Recursively go through JS and CSS.
		// Get the md5 of the contents.  
		// If it's already in the deleted block, remove it from that array, no action
		// Otherwise, prepare the new filename
		//   and upload that file as the new filename into the cdn

		$files_to_upload = array();

		foreach (array('css', 'js') as $ext)
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('public/' . $ext)) as $filename)
			{
				// Ignore '..' or '.' directories.
				if (in_array(substr($filename, -2), array('..', '\.')))
					continue;

				// Change \'s to /'s, save this for later too
				$original_filename = $filename = preg_replace('/\\\/', '/', $filename);
				// md5 file contents
				$md5 = md5_file($filename);
				// remove "public/"
				$filename = substr($filename, 7, strlen($filename));
				// Place md5 string inside filename
				$filename = preg_replace('/\.' . $ext . '/', '.' . $md5 . '.' . $ext, $filename);

				// Does this file already exist?
				if (in_array($filename, $existing_files))
				{
					// remove it
					$existing_files = array_diff($existing_files, array($filename));
					// don't do anything else with this file
					continue;
				}

				// Upload this file
				$files_to_upload[] = array(
					'name' => $filename,
					'path' => $original_filename
				);

				// Only show this if we're publishing
				if ($publish)
					$this->info('Uploading ' . $original_filename . ' as ' . $filename);
			}

		// Upload those files
		// Only if we're publishing
		if ($publish)
			$container->uploadObjects($files_to_upload);

		// Delete the necessary files
		// if they were safe to keep, they would have been removed from this array
		// Only delete if the command actions dictates
		if ($delete)
			foreach ($existing_files as $ef)
			{
				$this->info('Deleting ' . $ef . ' from cdn');
				$object = $container->getObject($ef);
				$object->delete();
			}

		$this->info('cdn ' . $action . ' finished');
		$this->comment('You may want to clear cache! (php artisan cache:clear)');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('action', InputArgument::REQUIRED, 'Publish or Delete?')
		);
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
