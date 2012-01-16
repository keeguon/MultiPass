<?php

namespace MultiPass\Strategies;

class Dropbox extends \MultiPass\Strategies\OAuth
{
  public $name = 'dropbox';

  public function __construct($opts)
  {
    parent::__construct($opts);
    
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'               => 'https://api.dropbox.com'
          , 'access_token_path'  => '/1/oauth/access_token'
          , 'authorize_url'      => 'https://www.dropbox.com/1/oauth/authorize' 
          , 'request_token_path' => '/1/oauth/request_token'
        )
    ), $this->options);
  }

  public function uid($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return $rawInfo['uid'];
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return array(
        'name'  => $rawInfo['display_name']
      , 'email' => $rawInfo['email']
    );
  }

  protected function rawInfo()
  {
    try {
      $this->client->fetch($this->options['client_options']['site'].'/1/account/info');
      return json_decode($this->client->getLastResponse());
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
