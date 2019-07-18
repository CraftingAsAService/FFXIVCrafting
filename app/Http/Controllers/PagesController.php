<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{

    public function stats()
    {
    	$active = 'stats';
		return view('pages.stats', compact('active'));
    }

    public function report()
    {
    	$active = 'report';
		return view('pages.report', compact('active'));
    }

    public function thanks()
    {
    	$active = 'thanks';
		return view('pages.thanks', compact('active'));
    }

    public function credits()
    {
    	$active = 'credits';
		return view('pages.credits', compact('active'));
    }


}
