<?php namespace App\Models\Osmose;

/**
 * Manage JSON files
 */
class FileHandler
{
	
	public static function path()
	{
		return storage_path() . '/app/libra/';
	}

	public static function get_file($filename)
	{
		if ( ! is_file(FileHandler::path() . $filename . '.json'))
			return (object) array('version' => null);
		$data = json_decode(file_get_contents(FileHandler::path() . $filename . '.json'));
		$data->version->filesize = FileHandler::format_bytes(filesize(FileHandler::path() . $filename . '.json'));
		return $data;
	}

	public static function get_version($filename)
	{
		return FileHandler::get_file($filename)->version;
	}

	public static function get_data($filename)
	{
		return FileHandler::get_file($filename)->data;
	}

	public static function save($filename, $data = array())
	{
		$base = array(
			'version' => null,
			'data' => null
		);

		$version = AppData::first();

		$base['version'] = array(
			'schema' => $version->schema_version,
			'data' => $version->data_version,
			'seq' => $version->data_sequence_no,
			'built' => date('Y-m-d H:i:s')
		);

		$base['data'] = $data;

		file_put_contents(FileHandler::path() . $filename . '.json', json_encode($base));
	}

	public static function format_bytes($bytes, $precision = 2)
	{ 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 

		// Uncomment one of the following alternatives
		// $bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow)); 

		return number_format(round($bytes, $precision)) . ' ' . $units[$pow]; 
	} 

}