<?php

class UserController extends Controller {
	private $auth;

	function __construct() {
		$this->auth = App::make('GHAuth');
	}

	function goGithub() {
		if (\Input::get('redirect_to')) {
			\Session::put('redirect', \Input::get('redirect_to'));
		}
		return $this->auth->redirect(array('user:email'));
	}

	function getAuthorization() {
		try {
			$userInfo = $this->auth->getToken();

			if ($user = User::find($userInfo['id'])) {
				$user->last_visit = new DateTime();
				$user->username = $userInfo['login'];
				$user->name = ($userInfo['name'] !== null) ? $userInfo['name'] : $userInfo['login'];
				$user->email = $userInfo['email'];			
			}
			else {
				$user = new User;
				$user->id = $userInfo['id'];
				$user->username = $userInfo['login'];
				$user->name = ($userInfo['name'] !== null) ? $userInfo['name'] : $userInfo['login'];
				$user->email = $userInfo['email'];
				$user->last_visit = new DateTime();
			}

			try {
				\Session::put('currentUser', $user);
				$user->save();
				if ($redirect = \Session::get('redirect_to')) {
					return Redirect::to($redirect);
				}
				else {
					return Redirect::to('/');
				}
			} catch (Exception $e) {
				throw new Exception("User logged in, but not added");
			}

		} catch (\Bwl\Auth\Exceptions\UserDeniedAccess $e) {
			// TODO: We may wish to prompt the user about this
			return Redirect::to('/');
		}
	}

	function logout() {
		$this->auth->destroySession();
		return Redirect::to('/');
	}
}