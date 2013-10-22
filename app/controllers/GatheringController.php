<?php

class GatheringController extends BaseController 
{

	public function getIndex()
	{
		return Redirect::to('/career');
	}

	public function getList($master_class = 'MIN')
	{
		return Redirect::to('/career');
	}

}