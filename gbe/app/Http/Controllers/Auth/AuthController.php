<?php namespace DemocracyApps\GB\Http\Controllers\Auth;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */
use DemocracyApps\GB\Http\Controllers\Controller;
use DemocracyApps\GB\Users\Social;
use DemocracyApps\GB\Users\User;
use DemocracyApps\GB\Users\UserConfirmation;
use DemocracyApps\GB\Utility\AppState;
use DemocracyApps\GB\Utility\Mailers\UserMailer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use PhpSpec\Exception\Exception;

class AuthController extends Controller
{

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	//use AuthenticatesAndRegistersUsers;
	private $userCreated = false;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar $registrar
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		//$this->middleware('guest', ['except' => 'getLogout']);
	}

	/**
	 * Show the application login form.
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 * @throws Exception
	 */
	public function login(Request $request)
	{
		if (\Request::method() == 'GET') {
			return view('auth.login');
		} else { // Login submission
			if (\Input::get('PW')) { // We can just do it.
				$rules = ['email' => 'required|email|exists:users,email', 'password' => 'required'];
				$this->validate($request, $rules);
				$user = $this->loadOrCreateUser(\Input::get('email'), \Input::get('password'), null, null, null,
					null, null);
				if ($user != null) {
					\Auth::login($user);
					return redirect()->intended('/');
				}
			} else if (\Input::get('FB')) {
				return redirect('auth/loginfb');
			} else if (\Input::get('TW')) {
				throw new Exception("Twitter Login Not Available");
				return redirect('auth/logintw');
			} else {
				return redirect('/');
			}
		}
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 * @throws Exception
	 */
	public function register(Request $request)
	{
		if ($request->method() == 'GET') {
			return view('auth.register');
		} else {
			if (\Input::get('PW')) {
				$rules = ['name' => 'required', 'email' => 'required|email|unique:users,email', 'password' => 'required'];
				$this->validate($request, $rules);
				$user = $this->loadOrCreateUser(\Input::get('email'), \Input::get('password'), null, \Input::get('name'), null,
					null, null);
				if ($user != null) {
					\Auth::login($user);
					return redirect('auth/thanks');
				} else {
					throw new Exception("Unknown problem registering as a new user");
				}
			} else if (\Input::get('FB')) {
				return redirect('auth/loginfb');
			}
			redirect('/');
		}
	}

	public function loginfb(Request $request)
	{
		if ($request->has('code')) {
			$fbUser = \Socialize::with('facebook')->user();
			$user = $this->loadOrCreateUser($fbUser->email, null, $fbUser->id, $fbUser->name,
				$fbUser->name, "facebook", $fbUser->token);
			if ($user != null) {
				\Auth::login($user);
			}
			return redirect()->intended('/');
		} else {
			return \Socialize::with('facebook')->scopes(['email'])->redirect();
		}
	}

	public function logout()
	{
		\Auth::logout();
		return redirect('/');
	}

	public function thanks(Request $request)
	{
		if ($request->method() == 'GET') {
			return view('auth.signup_thanks');
		} else if ($request->method() == 'POST') {
			return redirect()->intended('/');
		}
		return redirect('/');
	}

	public function confirm(Request $request)
	{
		$failed = true;
		if ($request->has('code')) {
			$code = $request->get('code');
			$uconfirm = UserConfirmation::where('code', '=', $code)->first();
			if ($uconfirm != null) {
				if ($uconfirm->checkCode($code)) {
					if ($uconfirm->type == 'em') {
						$user = User::find($uconfirm->user);
						$user->verified = true;
						$user->save();
						$uconfirm->done = true;
						$uconfirm->save();
						$failed = false;
					}
				}
			}
		}
		if ($failed) {
			return redirect('auth/confirm/failed');
		} else {
			return redirect('auth/confirm/ok');
		}
	}

	public function confirmResponse($result)
	{
		if ($result == 'ok') {
			return view('auth.confirm_ok');
		}
		else {
			return view('auth.confirm_failed');
		}
	}

	/*
	 * Utility functions
	 */
	private function loadOrCreateUser ($email, $password, $socialId, $userName, $socialName, $socialNetwork, $accessToken)
	{
		$this->userCreated = false;

		if ($socialId != null) {
			$socialProfile = Social::where('socialid', '=', $socialId)->first();
			if (empty($socialProfile)) { // We must create a new user
				$user = $this->createUser($userName, $email, $password);
				$socialProfile = new Social();
				$socialProfile->socialid = $socialId;
				$socialProfile->type = $socialNetwork;
				$socialProfile->username = $socialName;
				$socialProfile->userid = $user->id;
			} else {
				$user = User::findOrFail($socialProfile->userid);
			}
			$socialProfile->access_token = $accessToken;
			$socialProfile->save();
		}
		else if ($email != null && $password != null) {
			$user = User::where('email', $email)->first();
			if ($user == null) { // new user
				$user = $this->createUser($userName, $email, $password);
			}
			else { // Existing user - check the password
				if (! \Hash::check($password, $user->password)) {
					$user = null;
				}
			}

		}
		else throw new Exception("Unknown request to log in or create user");

		return $user;
	}

	private function createUser ($userName, $email, $password) {

		$superuserInitialized = false;

        $supers = User::where('superuser', '=', true)->get();
        if ($supers != null && sizeof($supers) > 0) {
            $superuserInitialized = true;
        }

		$this->userCreated = true;

		$user = new User;
		$user->name = $userName;
		$user->email = $email;
		if ($password != null) $user->password = \Hash::make($password);
		$user->superuser = false;
		$user->projectcreator = false;
		if (!$superuserInitialized) {
			$user->superuser = true;
			$user->projectcreator = true;
		}
		$user->save();

		$mailer = new UserMailer();
		$mailer->confirmEmail($user);
		return $user;
	}

	public function updateUserAccount($userId)
	{
		$rules = ['name' => 'required', 'email'=>'required|email'];
		$validator = \Validator::make(\Input::all(), $rules);
		if ($validator->fails()) {
			return \Redirect::back()->withInput()->withErrors($validator->messages());
		}

		$data = \Input::all();
		$user = User::find($userId);
		$user->name = $data['name'];

		$email = $data['email'];
		if ($email != $user->email) {
			//Check that nobody else has the email.
			$others = User::where('email', '=', $email)->first();
			if ($others != null) {
				return \Redirect::back()->withInput()->withErrors(array('email' => 'Another account with this email already exists'));
			}
			$user->email = $email;
			$user->verified = false;
			$user->save();
			$mailer = new UserMailer();
			$mailer->confirmEmail($user);

			\Session::put('url.intended', '/user/profile');
			return \Redirect::to('email_changed');
		}
		else {
			$user->save();
		}

		return \Redirect::to('/user/profile');
	}

}
