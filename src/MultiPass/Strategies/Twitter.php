<?php

namespace MultiPass\Strategies;

class Twitter extends \MultiPass\Strategies\OAuth
{
  protected $name = 'twitter';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'access_token_path'  => '/oauth/access_token'
          , 'authorize_path'     => '/oauth/authorize'
          , 'request_token_path' => '/oauth/request_token'
          , 'site'               => 'https://api.twitter.com'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function uid($rawInfo = null) {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return $rawInfo['id'];
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return array(
        'nickname'    => $rawInfo['screen_name']
      , 'name'        => $rawInfo['name']
      , 'location'    => $rawInfo['location']
      , 'image'       => $rawInfo['profile_image_url']
      , 'description' => $rawInfo['description']
      , 'urls'        => array(
            'Twitter' => 'http://twitter.com/'.$rawInfo['screen_name']
          , 'Website' => isset($rawInfo['url']) ? $rawInfo['url'] : null
        )
    );
  }

  protected function rawInfo()
  {
    try {
      $this->client->fetch($this->options['client_options']['site'].'/1.1/account/verify_credentials.json');
      return json_decode($this->client->getLastResponse(), true);
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
