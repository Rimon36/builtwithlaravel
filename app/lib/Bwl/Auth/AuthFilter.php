<?php namespace Bwl\Auth;

class AuthFilter {

	var $user;

	function __construct() {
		$this->user = (\Session::get('currentUser')) ? (object) \Session::get('currentUser') : false;
	}

	public function filter() {
		if ($this->user === false) {
			$type = \Request::getAcceptableContentTypes()[0];

			if ($type == 'application/json') {
				return \Response::make('', 401);
			}
			else {
				return \Redirect::to('/');
			}
		}
	}
}