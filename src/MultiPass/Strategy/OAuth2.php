<?php

namespace MultiPass\Strategy;

class OAuth2
{
  protected
      $options = array()
  ;

  public
      $client = null
    , $name   = 'OAuth2'
    , $token  = null
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_merge_recursive(array(
        'client_options'       => array()
      , 'token_params'         => array()
      , 'access_token_options' => array()
      , 'authorize_options'    => array()
    ), $opts);

    // Instanciate client
    $this->client = new \OAuth2\Client($client_id, $client_secret, $this->options['client_options']);
  }

  public function auth_hash()
  {
    $raw_info = $this->raw_info();
    $hash = new \MultiPass\AuthHash($this->name, $raw_info['id']);
    $hash->info        = $this->info($raw_info);
    $hash->credentials = $this->credentials();
    $hash->raw_info    = $raw_info;
    return $hash;
  }

  public function callback_phase()
  {
    if (isset($_GET['error']) || isset($_GET['error_reason'])) {
      $error_reason = (!empty($_GET['error_reason']))                                           ? $_GET['error_reason'] :
                     ((isset($_GET['error_description']) && !empty($_GET['error_description'])) ? $_GET['error_description'] :
                                                                                                  '');
      throw new \MultiPass\CallbackError($_GET['error'], $error_reason, $_GET['error_uri']);
    }

    $this->token = $this->client->auth_code()->get_token($_GET['code'], $this->options['token_params'], $this->options['access_token_options']);
    if ($this->token->is_expired()) {
      $this->token->refresh();
    }
  }
  
  public function request_phase()
  {
    http_redirect($this->client->auth_code()->authorize_url($this->options['authorize_options']));
  }
}
