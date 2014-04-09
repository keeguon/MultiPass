<?php

namespace MultiPass\Strategies;

class GitHub extends \MultiPass\Strategies\OAuth2
{
  protected $name = 'github';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'          => 'https://api.github.com'
          , 'authorize_url' => 'https://github.com/login/oauth/authorize'
          , 'token_url'     => 'https://github.com/login/oauth/access_token'
          , 'client_auth'   => 'query'
        )
      , 'token_params' => array(
            'parse' => 'query'
        )
      , 'token_options' => array(
            'mode'       => 'query'
          , 'param_name' => 'access_token'
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
        'nickname'    => $rawInfo['login']
      , 'email'       => $rawInfo['email']
      , 'name'        => $rawInfo['name']
      , 'image'       => $rawInfo['avatar_url']
      , 'description' => $rawInfo['bio']
      , 'urls'        => array(
            'GitHub' => $rawInfo['html_url']
          , 'Blog'   => $rawInfo['blog']
        )
    );
  }

  protected function rawInfo()
  {
    try {
      $response = $this->accessToken->get('/user', array('parse' => 'json'));
      return $response->parse();
    } catch (Exception $e) {
      print_r($e);
    }
  }
}
