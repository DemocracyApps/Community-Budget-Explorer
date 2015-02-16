<?php namespace DemocracyApps\GB\Http\Controllers;

use DemocracyApps\GB\Users\User;
use Illuminate\Http\Request;

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
