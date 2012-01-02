<?php

namespace MultiPass\Strategy;

class Twitter extends \MultiPass\Strategy\OAuth
{
  public
      $name = 'Twitter'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'access_token_url'  => '/oauth/access_token'
          , 'authorize_url'     => '/oauth/authorize'
          , 'request_token_url' => '/oauth/request_token'
          , 'site'              => 'https://api.twitter.com'
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
        'nickname'    => $raw_info['screen_name']
      , 'name'        => $raw_info['name']
      , 'location'    => $raw_info['location']
      , 'image'       => $raw_info['profile_image_url']
      , 'description' => $raw_info['description']
      , 'urls'        => array(
            'Twitter' => 'http://twitter.com/'.$raw_info['screen_name']
          , 'Website' => isset($raw_info['url']) ? $raw_info['url'] : null
        )
    );
  }

  
  protected function raw_info()
  {
    try {
      $this->client->fetch($this->options['client_options']['site'].'/1/account/verify_credentials.json');
      return json_decode($this->client->getLastResponse(), true);
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
