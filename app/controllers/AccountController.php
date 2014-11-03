<?php

class AccountController extends BaseController 
{

	public function __construct()
	{
		parent::__construct();
		View::share('active', 'account');
	}

	public function getIndex()
	{
		$account = Session::get('account');
		
		if (empty($account))
			return Redirect::to('/account/login');

		$job_ids = Config::get('site.job_ids');
		
		return View::make('account.index')
			->with('crafting_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['crafting'])->get())
			->with('gathering_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['gathering'])->get())
			->with('melee_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['basic_melee'])->get())
			->with('magic_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['basic_magic'])->get());
	}

	public function getLogin()
	{
		return View::make('account.login')
			->with('character_name', Input::old('name') ?: Session::get('character_name', ''))
			->with('server', Input::old('server') ?: Session::get('server', ''));
	}

	public function postLogin()
	{
		$character = Input::get('name');
		$server = Input::get('server');

		if ( ! in_array($server, Config::get('site.servers')))
		{
			Session::flash('error', 'That is not a valid server.');
			return Redirect::back()
				->withInput();
		}

		$cache_key = $character . '|' . $server;

		if ( ! Cache::has($cache_key))
			Cache::put($cache_key, $this->api_register($character, $server), 30);
			
		$account = Cache::get($cache_key);

		if (empty($account))
		{
			Session::flash('error', 'That is not a valid character/server combination.');
			return Redirect::back()
				->withInput();
		}

		Session::put('account', $account);

		Session::put('character_name', $character);
		Session::put('server', $server);

		Session::flash('success', 'This character will now be used in the site formulas!');

		return Redirect::to('/account');
	}

	private function api_register($character, $server)
	{
		include app_path() . '/models/LodestoneAPI/api.php';
		$API = new LodestoneAPI();

		$API->searchCharacter($character, $server, true);
		
		$search = $API->getSearch();

		if ($search['total'] == 0)
			return FALSE;

		$Character = $API->get(array(
			"name" => $character,
			"server" => $server
		));

		$levels = array();
		foreach ((array) $Character->getClassJobs('named') as $key => $values)
			$levels[$key] = $values['level'];

		return array(
			'avatar' => (string) $Character->getAvatar(64),
			'levels' => $levels,
			'created' => time()
		);
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

		return Redirect::to('/account');
	}

	public function getLogout()
	{
		Session::forget('account');
		Session::flash('success', 'You have been logged out.');
		return Redirect::to('/account/login');
	}

}