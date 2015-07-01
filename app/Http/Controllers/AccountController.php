<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Cache;
use Session;

use App\Models\CAAS\ClassJob;

use Viion\Lodestone\LodestoneAPI;

class AccountController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'account');
	}

	public function getIndex()
	{
		$account = Session::get('account');
		
		if (empty($account))
			return redirect('/account/login');

		$job_ids = Config::get('site.job_ids');

		$crafting_job_list = ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['crafting'])->get();
		$gathering_job_list = ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['gathering'])->get();
		$melee_job_list = ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['basic_melee'])->get();
		$magic_job_list = ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['basic_magic'])->get();
		
		return view('account.index', compact('crafting_job_list', 'gathering_job_list', 'melee_job_list', 'magic_job_list'));
	}
			
	public function getLogin()
	{
		$character_name = session('character_name', '');
		$server = session('server', '');

		return view('account.login', compact('character_name', 'server'));
	}

	public function postLogin(Request $request)
	{
		$inputs = $request->all();

		$character = $inputs['name'];
		$server = $inputs['server'];

		if ( ! in_array($server, Config::get('site.servers')))
		{
			Session::flash('error', 'That is not a valid server.');
			return redirect()->back()->withInput();
		}

		$cache_key = $character . '|' . $server;

		if ( ! Cache::has($cache_key))
			Cache::put($cache_key, $this->api_register($character, $server), 30);
			
		$account = Cache::get($cache_key);

		if (empty($account))
		{
			Session::flash('error', 'That is not a valid character/server combination.');
			return redirect()->back()->withInput();
		}

		session(['account' => $account]);

		session(['character_name' => $character]);
		session(['server' => $server]);

		Session::flash('success', 'This character will now be used in the site formulas!');

		return redirect('/account');
	}

	private function api_register($character, $server)
	{
		require app_path() . '/Models/LodestoneAPI/api-autoloader.php';
		$api = new LodestoneAPI();

		$character = $api->Search->Character($character, $server);

		$levels = [];
		foreach ((array) $character->classjobs as $classjob)
			$levels[strtolower($classjob['name'])] = $classjob['level'];

		return [
			'avatar' => (string) $character->avatar,
			'levels' => $levels,
			'created' => time()
		];
	}

	public function getRefresh()
	{
		$character = Session::get('character_name');
		$server = Session::get('server');

		$cache_key = $character . '|' . $server;

		Cache::put($cache_key, $this->api_register($character, $server), 30);
			
		$account = Cache::get($cache_key);

		Session::put('account', $account);

		Session::flash('success', 'Character data was refreshed.');

		return redirect('/account');
	}

	public function getLogout()
	{
		Session::forget('account');
		Session::flash('success', 'You have been logged out.');
		return redirect('/account/login');
	}

}
