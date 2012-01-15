<?php

namespace MultiPass\Strategies;

class GitHub extends \MultiPass\Strategies\OAuth2
{
  public
      $name = 'GitHub'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options'       => array(
            'site'          => 'https://api.github.com'
          , 'authorize_url' => 'https://github.com/login/oauth/authorize' 
          , 'token_url'     => 'https://github.com/login/oauth/access_token'
        )
      , 'token_params'         => array(
            'parse' => 'query'
        )
      , 'access_token_params'  => array(
            'mode'       => 'query'
          , 'param_name' => 'access_token'
        )
      , 'authorize_options'    => array()
    ), $opts);

    parent::__construct($client_id, $client_secret, $this->options);
  }

  public function info($raw_info = null)
  {
    $raw_info = $raw_info ?: $this->raw_info();

    return array(
        'nickname'    => $raw_info['login']
      , 'email'       => $raw_info['email']
      , 'name'        => $raw_info['name']
      , 'image'       => $raw_info['avatar_url']
      , 'description' => $raw_info['bio']
      , 'urls'        => array(
            'GitHub' => $raw_info['html_url']
          , 'Blog'   => $raw_info['blog']
        )
    );
  }

  
  protected function raw_info()
  {
    try {
      $response = $this->token->get('/user', array('parse' => 'json'));
      return $response->parse();
    } catch (Exception $e) {
      print_r($e);
    }
  }
}
