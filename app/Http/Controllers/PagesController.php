<?php

namespace App\Http\Controllers;

class PagesController extends Controller
{

    public function recipes()
    {
        view()->share('active', 'recipes');
        return view('recipes');
    }

    public function stats()
    {
        view()->share('active', 'stats');
		return view('pages.stats');
    }

    public function report()
    {
        view()->share('active', 'report');
		return view('pages.report');
    }

    public function thanks()
    {
        view()->share('active', 'thanks');
		return view('pages.thanks');
    }

    public function credits()
    {
        view()->share('active', 'credits');
		return view('pages.credits');
    }

}
