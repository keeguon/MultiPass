<?php

namespace MultiPass\Strategy;

class LinkedIn extends \MultiPass\Strategy\OAuth
{
  public
      $name = 'LinkedIn'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'access_token_path'  => '/uas/oauth/accessToken'
          , 'authorize_url'      => 'https://www.linkedin.com/uas/oauth/authorize'
          , 'request_token_path' => '/uas/oauth/requestToken'
          , 'site'               => 'https://api.linkedin.com'
        )
      , 'fields'         => array('id', 'first-name', 'last-name', 'headline', 'industry', 'picture-url', 'public-profile-url')
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
        'first_name' => $raw_info['firstName']
      , 'last_name'  => $raw_info['lastName']
      , 'name'       => "{$raw_info['firstName']} {$raw_info['lastName']}"
      , 'headline'   => $raw_info['headline']
      , 'image'      => $raw_info['pictureUrl']
      , 'industry'   => $raw_info['industry']
      , 'urls'       => array(
            'public_profile' => $raw_info['publicProfileUrl']
        )
    );
  }


  protected function raw_info()
  {
    try {
      $this->client->fetch($this->options['client_options']['site'].'/v1/people/~:'.implode(',', $this->options['fields']).'?format=json')
      return json_decode($this->client->getLastResponse(), true);
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
