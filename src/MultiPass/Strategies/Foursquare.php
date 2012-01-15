<?php

namespace MultiPass\Strategies;

class Foursquare extends \MultiPass\Strategies\OAuth2
{
  public
      $name = 'Foursquare'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options'       => array(
            'site'          => 'https://foursquare.com'
          , 'token_url'     => '/oauth2/access_token'
          , 'authorize_url' => '/oauth2/authenticate'
          , 'token_method'  => 'GET'
        )
      , 'token_params'         => array(
            'parse' => 'json'
        )
      , 'access_token_options' => array(
            'mode'       => 'query'
          , 'param_name' => 'oauth_token'
        )
      , 'authorize_options'    => array()
    ), $opts);

    parent::__construct($client_id, $client_secret, $this->options);
  }

  public function info($raw_info = null)
  {
    $raw_info = $raw_info ?: $this->raw_info();

    return array(
        'first_name' => $raw_info['firstName']
      , 'last_name'  => $raw_info['lastName']
      , 'image'      => $raw_info['photo']
    );
  }


  protected function raw_info()
  {
    try {
      $response       = $this->token->get('https://api.foursquare.com/v2/users/self', array('parse' => 'json'));
      $parsedResponse = $response->parse();
      return $parsedResponse['response']['user'];
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
