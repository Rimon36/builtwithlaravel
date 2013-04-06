<?php namespace Bwl\Auth;
use \Input;
use \Session;
use Guzzle\Http\Client;
use Bwl\Auth\Oauth2;

class Authorize {

	/**
	 * Hold the configuration
	 */
	private $config;

	/**
	 * Initialize the configuration, then fire up
	 * the Guzzle client, and attach the Oauth
	 * subscriber to sign requests and sniff for errors.
	 * @param array $config
	 */
	function __construct($config=array()) {
		$this->config = $config;
		$this->client = new Client('https://github.com');
		$this->client->addSubscriber(new Oauth2($this->config));
	}

	/**
	 * Takes optional array of additional scopes (user included by default)
	 * ex. $this->redirect(array('user:email'))
	 * returns Redirect
	 * @param  array  $scopes
	 * @return Redirect
	 */
	function redirect($scopes=array()) {
		$scope = array_merge(array('user'), $scopes);
		return \Redirect::to("https://github.com/login/oauth/authorize?client_id=" . $this->config['client_id'] . "&scope=" . implode(',', $scope) . "&redirect_uri=" . url('/authorize'));
	}

	/**
	 * Should be used for obtaining tokens after using $this->redirect()
	 * Exceptions thrown for user denying access
	 * (Being redirected back with ?error=access_denied)
	 * or for issues with the authorization code
	 * (like it being used already etc.)
	 * @return User
	 */
	function getToken() {
		$code = Input::get('code');

		if ($code === NULL && Input::get('code') == 'access_denied') {
			throw new \Bwl\Auth\Exceptions\UserDeniedAccess;
		}
		$req = $this->client->post('login/oauth/access_token', array('Accept' => 'application/json'), array(
				'client_id' => $this->config['client_id'],
				'client_secret' => $this->config['client_secret'],
				'code' => $code
			)
		)->send();

		$response = $req->json();
		if (isset($response['access_token'])) {
			Session::put('token', $response['access_token']);
		}
		elseif (isset($response['error']) && $response['error'] === 'bad_verification_code') {
			throw new \Bwl\Auth\Exceptions\InvalidToken;
		}
		return $this->getUser();
	}

	/**
	 * Gets the Github profile for the current user,
	 * and also gets their primary email.
	 * Exceptions thrown for not having a token (user isn't authenticated)
	 * or if the token has expired (prompt user to re-authenticate with GH)
	 * @return array
	 */
	function getUser() {
		if (!Session::get('token')) {
			throw new \Bwl\Auth\Exceptions\NoToken;
		}
		try {
			$user = $this->client->get('https://api.github.com/user')->send()->json();
			$user['email'] = $this->getPrimaryEmail();
			return $user;
		} catch (\Bwl\Auth\InvalidToken $e) {
			return false;
		}
	}

	/**
	 * Obtains the primary && verified email on the current account
	 * (Requires the 'user:email' scope on initial auth request)
	 * @return string
	 */
	function getPrimaryEmail() {
		if (!Session::get('token')) {
			throw new \Bwl\Auth\Exceptions\NoToken;
		}
		try {
			$response = $this->client->get('https://api.github.com/user/emails', array('Accept' => 'application/vnd.github.v3'))->send()->json();
			foreach ($response as $email) {
				if ($email['primary'] === true && $email['verified'] === true) {
					return $email['email'];
				}
			}
		} catch (\Bwl\Auth\InvalidToken $e) {
			return false;
		}
	}

	/**
	 * Destroys any sessions we should have open
	 * for the current user
	 * @return void
	 */
	function destroySession() {
		Session::forget('token');
		Session::forget('currentUser');
	}
}