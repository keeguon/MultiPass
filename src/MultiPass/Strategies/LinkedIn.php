<?php

namespace MultiPass\Strategies;

class LinkedIn extends \MultiPass\Strategies\OAuth
{
  protected $name = 'linkedin';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'access_token_path'  => '/uas/oauth/accessToken'
          , 'authorize_url'      => 'https://www.linkedin.com/uas/oauth/authorize'
          , 'request_token_path' => '/uas/oauth/requestToken'
          , 'site'               => 'https://api.linkedin.com'
        )
      , 'fields' => array('id', 'first-name', 'last-name', 'headline', 'industry', 'picture-url', 'public-profile-url')
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
        'first_name' => $rawInfo['firstName']
      , 'last_name'  => $rawInfo['lastName']
      , 'name'       => "{$rawInfo['firstName']} {$rawInfo['lastName']}"
      , 'headline'   => $rawInfo['headline']
      , 'image'      => $rawInfo['pictureUrl']
      , 'industry'   => $rawInfo['industry']
      , 'urls'       => array(
            'public_profile' => $rawInfo['publicProfileUrl']
        )
    );
  }

  protected function rawInfo()
  {
    try {
      $this->client->fetch($this->options['client_options']['site'].'/v1/people/~:'.implode(',', $this->options['fields']).'?format=json');
      return json_decode($this->client->getLastResponse(), true);
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
