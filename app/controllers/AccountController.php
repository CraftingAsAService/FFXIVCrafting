<?php

class AccountController extends BaseController 
{

	public function __construct()
	{
		View::share('active', 'account');
	}

	public function getIndex()
	{
		include app_path() . '/models/LodestoneAPI/api.php';
		$API = new LodestoneAPI();

		$account = Cache::get('sakhr', function() use ($API) {
			$Character = $API->get(array(
				"name" => "Sakhr Ruh'wah", 
				"server" => "Ultros"
			));

			$account = array(
				'avatar' => (string) $Character->getAvatar(64),
				'classes' => (array) $Character->getClassJobsOrdered('desc', 'level', 'named'),
				'created' => time()
			);

			Cache::put('sakhr', $account, Config::get('site.cache_length'));

			return $account;
		});

		return View::make('account.index')
			->with('account', $account);
	}

}