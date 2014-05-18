<?php

class ListController extends BaseController 
{

	public function getIndex()
	{
		// Get the list
		$list = Session::get('list', array());

		foreach ($list as $k => &$l)
		{
			// $l starts as the amount integer and we're transforming it to an array
			$l = array(
				'amount' => $l, 
				'item' => Item::with(array('recipe' => function($query) {
					$query->limit(1);
				}, 'name'))->find($k)
			);

			if (count($l['item']->recipe) == 0)
				unset($list[$k]);
		}
		unset($l);

		$saved = array();
		if ($list)
			foreach ($list as $id => $info)
				$saved[] = $id . ',' . $info['amount'];
		$saved = implode(':', $saved);

		return View::make('pages.list')
			->with('active', 'list')
			->with('list', $list)
			->with('saved_link', $saved)
			->with('job_list', ClassJob::get_name_abbr_list());
	}

	public function postAdd()
	{
		// Get the list
		$list = Session::get('list', array());

		// What do we want to add?
		$item_id = Input::get('item-id');
		$qty = Input::get('qty') ?: 1;

		if ( ! in_array($item_id, array_keys($list)))
			$list[$item_id] = $qty;
		else
			$list[$item_id] += $qty;

		// Save the list
		Session::put('list', $list);
	}

	public function postEdit()
	{
		// Get the list
		$list = Session::get('list', array());
		
		// What do we want to remove?
		$item_id = Input::get('item-id');
		$amount = Input::get('amount');

		if (is_numeric($amount) && $amount > 0)
			$list[$item_id] = $amount;
		else
			unset($list[$item_id]);

		// Save the list
		Session::put('list', $list);
	}

	public function postDelete()
	{
		// Get the list
		$list = Session::get('list', array());
		
		// What do we want to remove?
		$item_id = Input::get('item-id');

		unset($list[$item_id]);

		// Save the list
		Session::put('list', $list);
	}

	public function getFlush()
	{
		// Kill the list
		Session::forget('list');

		return Redirect::to('/list');
	}

	public function getSaved($string = '')
	{
		// Reset the list
		$list = Session::get('list', array());
		if ($list)
			View::share('flushed', true);
		$list = array();

		foreach (explode(':', $string) as $set)
		{
			list($id, $amount) = explode(',', $set);
			if (is_numeric($amount) && $amount > 0)
				$list[$id] = $amount;
		}

		// Save the list
		Session::put('list', $list);
		View::share('saved', true);

		return $this->getIndex();
	}

}