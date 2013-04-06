<?php namespace Bwl\Auth;
use Bwl\Auth\Exceptions\InvalidToken;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Oauth2 implements EventSubscriberInterface {

	function __construct($config) {
		$this->config = $config;
	}

	public static function getSubscribedEvents() {
		return array(
			'request.before_send' => array('onRequestBeforeSend', 100),
			'request.error' => array('onRequestError', 100)
		);
	}

	function onRequestBeforeSend(Event $event) {
		$event['request']->setHeader('Authorization', $this->buildAuthorizationHeader());
	}

	private function buildAuthorizationHeader() {
		return 'Bearer ' . \Session::get('token');
	}

	function onRequestError(Event $event) {
		if ($event['response']->getStatusCode() === 401 || $event['response']->getStatusCode() === 403) {
			throw new InvalidToken;
		}
	}
}