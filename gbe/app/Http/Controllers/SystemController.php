<?php namespace DemocracyApps\GB\Http\Controllers;

use Illuminate\Http\Request;
use DemocracyApps\GB\User;

class SystemController extends Controller
{

    public function settings(Request $request)
    {
        return view('system.settings', array());
    }

    public function users(Request $request)
    {
        $users = User::orderBy('id')->get();
        return view('system.users', array('users' => $users));
    }

}
