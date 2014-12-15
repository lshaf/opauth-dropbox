<?php
/**
 * Dropbox strategy for Opauth
 * based on https://www.dropbox.com/developers/core/doc
 * 
 * More information on Opauth: http://opauth.org
 * 
 * @copyright    Copyright © 2014 L Shaf (http://pictalogi.com)
 * @link         http://opauth.org
 * @package      Opauth.DropboxStrategy
 * @license      MIT License
 */
class DropboxStrategy extends OpauthStrategy{
  /**
   * Compulsory config keys, listed as unassociative arrays
   * eg. array('app_id', 'app_secret');
   */
  public $expects = array('key', 'secret');
  
  /**
   * Optional config keys with respective default values, listed as associative arrays
   * eg. array('scope' => 'email');
   */
  public $defaults = array(
    'redirect_uri' => '{complete_url_to_strategy}int_callback',
    'response_type' => 'code'
  );

  /**
   * Auth request
   */
  public function request(){
    $url = 'https://www.dropbox.com/1/oauth2/authorize';
    $params = array(
      'locale' => 'en',
      'client_id' => $this->strategy['key'],
      'redirect_uri' => $this->strategy['redirect_uri']
    );

    if (!empty($this->strategy['state'])) $params['state'] = $this->strategy['state'];
    if (!empty($this->strategy['response_type'])) $params['response_type'] = $this->strategy['response_type'];
    
    $this->clientGet($url, $params);
  }
  
  /**
   * Internal callback, after Dropbox's OAuth
   */
  public function int_callback(){
    if (array_key_exists('code', $_GET) && !empty($_GET['code'])){
      $url = 'https://api.dropbox.com/1/oauth2/token';
      $params = array(
        "grant_type" => "authorization_code",
        'client_id' => $this->strategy['key'],
        'client_secret' => $this->strategy['secret'],
        'redirect_uri'=> $this->strategy['redirect_uri'],
        'code' => trim($_GET['code'])
      );
      $response = $this->serverPost($url, $params, null, $headers);

      $results = json_decode($response);

      if (!empty($results) && !empty($results->access_token)){
        $me = $this->me($results->access_token);

        $this->auth = array(
          'provider' => 'Dropbox',
          'uid' => $me->uid,
          'info' => array(
            'name' => $me->display_name,
            'image' => ''
          ),
          'credentials' => array(
            'token' => $results->access_token
          ),
          'raw' => $me
        );

        $this->callback();
      }
      else{
        $error = array(
          'provider' => 'Dropbox',
          'code' => 'access_token_error',
          'message' => 'Failed when attempting to obtain access token',
          'raw' => $headers
        );

        $this->errorCallback($error);
      }
    }
    else{
      $error = array(
        'provider' => 'Dropbox',
        'code' => $_GET['error'],
        'message' => $_GET['error_description'],
        'raw' => $_GET
      );
      
      $this->errorCallback($error);
    }
  }
  
  /**
   * Queries Dropbox API for user info
   *
   * @param string $access_token 
   * @return array Parsed JSON results
   */
  private function me($access_token){
    $option['http']['header'] = "Authorization: Bearer $access_token";
    $me = $this->serverGet('https://api.dropbox.com/1/account/info', array(), $option, $headers);
    if (!empty($me)){
      return json_decode($me);
    }
    else{
      $error = array(
        'provider' => 'Dropbox',
        'code' => 'me_error',
        'message' => 'Failed when attempting to query for user information',
        'raw' => array(
          'response' => $me,
          'headers' => $headers
        )
      );

      $this->errorCallback($error);
    }
  }
}
