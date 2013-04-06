<?php

class APIController extends Controller {

	var $user;

	function __construct() {
		$this->user = (Session::get('currentUser')) ? (object) Session::get('currentUser') : false;
	}

	/**
	 * Returns a 410 Gone (Deleted)
	 * @return Response
	 */
	function deleted() {
		return Response::json([], 410);
	}

	/**
	 * Returns a 302 with the location of the existing item and
	 * its data
	 * @param  array  $contents
	 * @param  string $path
	 * @return Response
	 */
	function alreadyExists($contents, $path) {
		$response = Response::json($contents, 302);
		$response->headers->set('Location', url($path));
		return $response;
	}

	/**
	 * Returns HTTP 201 created with the appropriate Location header
	 * @param  mixed $contents
	 * @param  string $path
	 * @return Response
	 */
	function created($contents, $path) {
		$response = Response::json($contents, 201);
		$response->headers->set('Location', url($path));
		return $response;
	}

	/**
	 * Return the current user if we have one,
	 * false otherwise
	 * @return boolean
	 */
	function hasUser() {
		return $this->user;
	}

	/**
	 * Returns the object if there is an authenticated user
	 * otherwise throws a 401
	 * @param  mixed  $contents
	 * @param  integer $status  HTTP status to use for a user
	 * @return Response
	 */
	function requireUser($object, $status=200) {
		if ($this->user === false) {
			return Response::json(['error' => true, 'message' => 'Unauthorized'], 401);
		}
		return Response::json($object, $status);
	}

	/**
	 * Returns whether or not the current user
	 * is the user specified (via id)
	 * @param  integer  $id
	 * @return boolean
	 */
	function hasOwner($id) {
		return ($this->user->id !== $id);
	}

	/**
	 * Firstly, requires an authenticated user,
	 * then requires that the current user be $ownerID,
	 * and if so, returns the contents with 200 
	 * (unless overridden via $status)
	 * @param  integer  $ownerID
	 * @param  mixed    $contents
	 * @param  integer  $status
	 * @return Response
	 */
	function requireOwner($ownerID, $contents, $status=200) {
		if ($this->hasUser()) {
			if ($ownerID !== $this->user->id) {
				return Response::make('', 403);
			}
			return Response::json($contents, $status);
		}
		else {
			return Response::json(['error' => true, 'message' => 'Unauthorized'], 401);
		}

		return true;
	}

	function hasAdmin() {
		if ($this->hasUser()) {
			return (bool) $this->user->is_admin;
		}
		return false;
	}

	function requireAdmin() {
		if ($this->hasUser()) {
			if (!$this->hasAdmin()) {
				return Response::json(['error' => true, 'message' => 'Forbidden'], 403);
			}
		}
		else {
			return Response::json(['error' => true, 'message' => 'Unauthorized'], 401);
		}
	}

	/**
	 * If you just wanna die with an error, you can do that here
	 * @param  integer $status   HTTP Status Code
	 * @return Response
	 */
	function error($messages = [], $status=400) {
		return Response::json(['error' => true, 'message' => $messages], $status);
	}
}