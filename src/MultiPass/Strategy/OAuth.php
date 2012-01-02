<?php

namespace MultiPass\Strategy;

class OAuth
{
  protected
      $options = array()
  ;

  public
      $client = null
    , $name   = 'OAuth'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'auth_type'         => OAUTH_AUTH_TYPE_AUTHORIZATION
          , 'signature_method'  => OAUTH_SIG_METHOD_HMACSHA1
        )
    ), $opts);

    // Instantiate client
    $this->client = new \OAuth($client_id, $client_secret, $this->options['client_options']['signature_method'], $this->options['client_options']['auth_type']);
  }

  public function auth_hash()
  {
    $raw_info          = $this->raw_info();
    $hash              = new \MultiPass\AuthHash($this->name, $raw_info['id']);
    $hash->info        = $this->info($raw_info);
    $hash->credentials = $this->credentials();
    $hash->raw_info    = $raw_info;
    return $hash;
  }

  public function callback_phase()
  {
    try {
      // Fetch access token
      $this->client->setToken($_GET['oauth_token'], $_SESSION['oauth'][$this->name]['oauth_token_secret']);
      $access_token = $this->client->getAccessToken($this->options['client_options']['site'].$this->options['client_options']['access_token_url']);

      // Store access token informations
      $_SESSION['oauth'][$this->name] = array(
          'oauth_token'        => $access_token['oauth_token']
        , 'oauth_token_secret' => $access_token['oauth_token_secret']
      );

      // Set the client token w/ the last token informations
      $this->client->setToken($_SESSION['oauth'][$this->name]['oauth_token'], $_SESSION['oauth'][$this->name]['oauth_token_secret']);
    } catch (\Exception $e) {
      print_r($e);
    }
  }

  public function request_phase()
  {
    try {
      // Fetch request token
      $request_token = $this->client->getRequestToken($this->options['client_options']['site'].$this->options['client_options']['request_token_url'], isset($this->options['client_options']['oauth_callback']) ? $this->options['client_options']['oauth_callback'] : 'oob');

      // Throw exception if the callback isn't confirmed
      if (!in_array($request_token['oauth_callback_confirmed'], array(true, "true"))) {
        throw new \Exception("There was an error regarding the callback confirmation");
      }

      // Store the OAuth Token and the OAuth Token Secret in the session
      $_SESSION['oauth'][$this->name] = array(
          'oauth_token'        => $request_token['oauth_token']
        , 'oauth_token_secret' => $request_token['oauth_token_secret']
      );

      // Redirect the user to the Provider Authorize page
      http_redirect($this->options['client_options']['site'].$this->options['client_options']['authorize_url'].'?oauth_token='.$request_token['oauth_token']);
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
