<?php
/**
 * Yahoojp(YConnect) strategy for Opauth
 * based on http://developer.yahoo.co.jp/yconnect/
 * 
 * More information on Opauth: http://opauth.org
 * 
 * @copyright    Copyright © 2012 Ryo Ito (https://github.com/ritou)
 * @link         http://opauth.org
 * @package      Opauth.YahoojpStrategy
 * @license      MIT License
 */

/**
 * Yahoojp(YConnect) strategy for Opauth
 * based on http://developer.yahoo.co.jp/yconnect/
 * 
 * @package			Opauth.Yahoojp
 */
class YahoojpStrategy extends OpauthStrategy{
	
	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('client_id', 'client_secret');
	
	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array('redirect_uri', 'scope', 'state');
	
	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback',
		'scope' => 'openid profile email address'
	);
	
	/**
	 * Auth request
	 */
	public function request(){
		$url = 'https://auth.login.yahoo.co.jp/yconnect/v1/authorization';
		$params = array(
			'client_id' => $this->strategy['client_id'],
			'redirect_uri' => $this->strategy['redirect_uri'],
			'response_type' => 'code',
			'scope' => $this->strategy['scope']
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
			$url = 'https://auth.login.yahoo.co.jp/yconnect/v1/token';
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n".
                                "Authorization: Basic ".base64_encode($this->strategy['client_id'] . ':' . $this->strategy['client_secret'])
                )
            );
			$params = array(
				'code' => $code,
				'redirect_uri' => $this->strategy['redirect_uri'],
				'grant_type' => 'authorization_code'
			);
			$response = $this->serverPost($url, $params, $options, $headers);
			
			$results = json_decode($response);
			
			if (!empty($results) && !empty($results->access_token)){
				$userinfo = $this->userinfo($results->access_token);
				$this->auth = array(
					'provider' => 'Yahoojp',
					'uid' => $userinfo->user_id,
					'info' => array(
						'name' => $userinfo->name,
                        'email' => $userinfo->email,
                        'email_verified' => $userinfo->email_verified
					),
					'credentials' => array(
						'token' => $results->access_token,
						'expires' => date('c', time() + $results->expires_in)
					),
					'raw' => $userinfo
				);
				
				$this->callback();
			}
			else{
				$error = array(
					'provider' => 'Yahoojp',
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
				'provider' => 'Yahoojp',
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);
			
			$this->errorCallback($error);
		}
	}
	
	/**
	 * Queries People API for user info
	 *
	 * @param string $access_token 
	 * @return array Parsed JSON results
	 */
	private function userinfo($access_token){
		$userinfo = $this->serverGet('https://userinfo.yahooapis.jp/yconnect/v1/attribute', array('schema' => 'openid', 'access_token' => $access_token), null, $headers);
		if (!empty($userinfo)){
			return json_decode($userinfo);
		}
		else{
			$error = array(
				'provider' => 'Yahoojp',
				'code' => 'userinfo_error',
				'message' => 'Failed when attempting to query for user information',
				'raw' => array(
					'response' => $userinfo,
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	}
}
