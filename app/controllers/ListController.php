<?php

class ListController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		// Get the list
		$list = Session::get('list', array());

		foreach ($list as $k => &$l)
			// $l starts as the amount integer and we're transforming it to an array
			$l = array(
				'amount' => $l, 
				'item' => Item::with(array('recipes' => function($query) {
					$query->limit(1);
				}))->find($k)
			);

		return View::make('list')
			->with('active', 'list')
			->with('list', $list)
			->with('job_list', $job_list);
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

}