<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Session;

use App\Models\Garland\Item;
use App\Models\Garland\Job;

class ListController extends Controller
{

	public function getIndex()
	{
		// Get the list
		$list = Session::get('list', []);

		foreach ($list as $k => &$l)
		{
			// $l starts as the amount integer and we're transforming it to an array
			$l = [
				'amount' => $l, 
				'item' => Item::with(['recipes' => function($query) {
					$query->limit(1);
				}])->find($k)
			];

			if (count($l['item']->recipes) == 0)
				unset($list[$k]);
		}
		unset($l);

		$saved_link = [];
		if ($list)
			foreach ($list as $id => $info)
				$saved_link[] = $id . ',' . $info['amount'];
		$saved_link = implode(':', $saved_link);

		$job_list = Job::lists('name', 'abbr')->all();
		$active = 'list';

		return view('pages.list', compact('active', 'list', 'saved_link', 'job_list'));
	}
			
	public function postAdd(Request $request)
	{
		$input = $request->all();

		// Get the list
		$list = Session::get('list', []);

		// What do we want to add?
		$item_id = $request['item-id'];
		$qty = $request['qty'] ?: 1;

		if ( ! in_array($item_id, array_keys($list)))
			$list[$item_id] = $qty;
		else
			$list[$item_id] += $qty;

		// Save the list
		Session::put('list', $list);
	}

	public function postEdit(Request $request)
	{
		$input = $request->all();

		// Get the list
		$list = Session::get('list', []);
		
		// What do we want to remove?
		$item_id = $input['item-id'];
		$amount = $input['amount'];

		if (is_numeric($amount) && $amount > 0)
			$list[$item_id] = $amount;
		else
			unset($list[$item_id]);

		// Save the list
		Session::put('list', $list);
	}

	public function postDelete(Request $request)
	{
		$input = $request->all();
		
		// Get the list
		$list = Session::get('list', []);
		
		// What do we want to remove?
		$item_id = $input['item-id'];

		unset($list[$item_id]);

		// Save the list
		Session::put('list', $list);
	}

	public function getFlush()
	{
		// Kill the list
		Session::forget('list');

		return redirect('/list');
	}

	public function getSaved($string = '')
	{
		// Reset the list
		$list = Session::get('list', []);
		if ($list)
			view()->share('flushed', true);
		$list = [];

		// String needs to contain at least digits comma digits (1234,5), but can be expaned as (1234,5:6789,10)
		if (preg_match('/\d+\,\d+/', $string))
		{
			foreach (explode(':', $string) as $set)
			{
				list($id, $amount) = explode(',', $set);
				if (is_numeric($amount) && $amount > 0)
					$list[$id] = $amount;
			}

			// Save the list
			Session::put('list', $list);
			view()->share('saved', true);
		}
		else
			view()->share('incomplete_saved', true);

		return $this->getIndex();
	}

}
