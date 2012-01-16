<?php

namespace MultiPass\Strategies;

class Foursquare extends \MultiPass\Strategies\OAuth2
{
  protected $name = 'foursquare';

  public function __construct($opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'          => 'https://foursquare.com'
          , 'token_url'     => '/oauth2/access_token'
          , 'authorize_url' => '/oauth2/authenticate'
          , 'token_method'  => 'GET'
        )
      , 'token_params' => array(
            'parse' => 'json'
        )
      , 'token_options' => array(
            'mode'       => 'query'
          , 'param_name' => 'oauth_token'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function uid($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();
 
    return $rawInfo['id'];
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return array(
        'first_name' => $rawInfo['firstName']
      , 'last_name'  => $rawInfo['lastName']
      , 'image'      => $rawInfo['photo']
    );
  }

  protected function rawInfo()
  {
    try {
      $response       = $this->accessToken->get('https://api.foursquare.com/v2/users/self', array('parse' => 'json'));
      $parsedResponse = $response->parse();
      return $parsedResponse['response']['user'];
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
