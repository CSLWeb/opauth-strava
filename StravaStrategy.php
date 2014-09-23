<?php
/**
 * Strava strategy for Opauth
 *
 * More information on Opauth: http://opauth.org
 *
 * @copyright    Copyright © 2014 CSL Web (http://csl-web.com)
 * @link         http://www.csl-web.com
 * @package      cslweb.StravaStrategy
 * @license      MIT License
 */

/**
 * Strava strategy for Opauth
 *
 * @package			Opauth.Strava
 */
class StravaStrategy extends OpauthStrategy{

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('client_id', 'client_secret');

	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array( 'scope', 'state', 'response_type','approval_prompt','redirect_uri');

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback',
		'scope' => 'public',
		'state' => 'silverstripe',
		'approval_prompt' => 'auto'
	);

	/**
	 * Auth request
	 */
	public function request(){
		$url = 'https://www.strava.com/oauth/authorize';
		$params = array(
			'client_id' => $this->strategy['client_id'],
			'redirect_uri' => $this->strategy['redirect_uri'],
			'response_type' => 'code',
			'approval_prompt' => $this->strategy['approval_prompt'],
			'scope' => $this->strategy['scope'],
			'state' => $this->strategy['state']
		);

		foreach ($this->optionals as $key){
			if (!empty($this->strategy[$key])) $params[$key] = $this->strategy[$key];
		}

		$this->clientGet($url, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback(){
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])){
			$code = $_GET['code'];
			$url = 'https://www.strava.com/oauth/token';
			$params = array(
				'code' => $code,
				'client_id' => $this->strategy['client_id'],
				'client_secret' => $this->strategy['client_secret']
			);
			$response = $this->serverPost($url, $params, null, $headers);

			$results = json_decode($response);
			if (!empty($results) && !empty($results->access_token)){
				$userinfoobject = $results->athlete;
				$userinfo = array();
				foreach($userinfoobject as $k=>$v){
					$userinfo[$k] = $v;
				}

				$this->auth = array(
					'uid' => $userinfo['id'],
					'info' => $userinfo,
					'credentials' => array(
						'token' => $results->access_token
					),
					'raw' => $userinfo
				);

				if (!empty($results->refresh_token))
				{
					$this->auth['credentials']['refresh_token'] = $results->refresh_token;
				}

		    $this->mapProfile($userinfo, 'firstname','info.firstname');
		    $this->mapProfile($userinfo, 'lastname','info.lastname');
		    $this->mapProfile($userinfo, 'email','info.email');

				$this->callback();
			}
			else{
				$error = array(
					'code' => 'access_token_error',
					'message' => 'Failed when attempting to obtain access token',
					'raw' => array(
						'response' => $response,
						'headers' => $headers
					)
				);

				$this->errorCallback($error);
			}
		}
		else{
			$error = array(
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}
}