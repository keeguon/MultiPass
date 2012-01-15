<?php

namespace MultiPass\Strategies;

class Dropbox extends \MultiPass\Strategies\OAuth
{
  public
      $name = 'Dropbox'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'               => 'https://api.dropbox.com'
          , 'access_token_path'  => '/1/oauth/access_token'
          , 'authorize_url'      => 'https://www.dropbox.com/1/oauth/authorize' 
          , 'request_token_path' => '/1/oauth/request_token'
        )
    ), $opts);

    parent::__construct($client_id, $client_secret, $this->options);
  }

  public function credentials()
  {
    return array(
        'token'  => $_SESSION['oauth'][$this->name]['oauth_token']
      , 'secret' => $_SESSION['oauth'][$this->name]['oauth_token_secret']
    );
  }

  public function info($raw_info = null)
  {
    $raw_info = $raw_info ?: $this->raw_info();

    return array(
        'name'  => $raw_info['display_name']
      , 'email' => $raw_info['email']
    );
  }


  protected function raw_info()
  {
    try {
      $this->client->fetch($this->options['client_options']['site'].'/1/account/info');
      $response = json_decode($this->client->getLastResponse());

      // Setting the UID right
      $response['id'] = $response['uid'];
      unset($response['uid']);

      // return hash
      return $response;
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
