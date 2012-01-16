<?php

namespace MultiPass\Strategies;

class OAuth extends \MultiPass\Strategy
{
  public $options = array();
  
  protected $client  = null;
  protected $name    = 'oauth';

  public function __construct($opts)
  {
    parent::__construct($opts);
    
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'auth_type'         => OAUTH_AUTH_TYPE_AUTHORIZATION
          , 'signature_method'  => OAUTH_SIG_METHOD_HMACSHA1
        )
    ), $this->options);

    // Instanciate client
    $this->client = new \OAuth($this->options['client_id'], $this->options['client_secret'], $this->options['client_options']['signature_method'], $this->options['client_options']['auth_type']);
  }

  public function getClient()
  {
    return $this->client;
  }

  public function uid() {}

  public function info() {}

  public function credentials()
  {
    return array(
        'token'  => $_SESSION['oauth'][$this->name]['oauth_token']
      , 'secret' => $_SESSION['oauth'][$this->name]['oauth_token_secret']
    );
  }
  
  public function extra($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();
    
    return array('raw_info' => $rawInfo);
  }
  
  public function requestPhase()
  {
    try {
      // Fetch request token
      $requestToken = $this->client->getRequestToken($this->requestTokenUrl(), $this->getCallbackUrl() ?: 'oob');

      // Throw exception if the callback isn't confirmed
      if (!in_array($requestToken['oauth_callback_confirmed'], array(true, "true"))) {
        throw new \Exception("There was an error regarding the callback confirmation");
      }

      // Store the OAuth Token and the OAuth Token Secret in the session
      $_SESSION['oauth'][$this->name] = array(
          'oauth_token'        => $requestToken['oauth_token']
        , 'oauth_token_secret' => $requestToken['oauth_token_secret']
      );

      // Redirect the user to the Provider Authorize page
      header('Location: '.$this->authorizeUrl(array('oauth_token' => $requestToken['oauth_token'])));
      exit;
    } catch (\Exception $e) {
      print_r($e);
    }
  }

  public function callbackPhase()
  {
    try {
      // Fetch access token
      $this->client->setToken($_GET['oauth_token'], $_SESSION['oauth'][$this->name]['oauth_token_secret']);
      $accessToken = $this->client->getAccessToken($this->accessTokenUrl(array('oauth_token' => $_GET['oauth_token'])));

      // Store access token informations
      $_SESSION['oauth'][$this->name] = array(
          'oauth_token'        => $accessToken['oauth_token']
        , 'oauth_token_secret' => $accessToken['oauth_token_secret']
      );

      // Set the client token w/ the last token informations
      $this->client->setToken($_SESSION['oauth'][$this->name]['oauth_token'], $_SESSION['oauth'][$this->name]['oauth_token_secret']);

      return parent::callbackPhase();
    } catch (\Exception $e) {
      print_r($e);
    }
  }

  protected function accessTokenUrl($params = array())
  {
    $accessTokenUrl = isset($this->options['client_options']['access_token_url']) ? $this->options['client_options']['access_token_url'] : $this->options['client_options']['site'].$this->options['client_options']['access_token_path'];
  
    return $params ? $accessTokenUrl.'?'.http_build_query($params) : $accessTokenUrl;
  }

  protected function authorizeUrl($params = array())
  {
    $authorizeUrl = isset($this->options['client_options']['authorize_url']) ? $this->options['client_options']['authorize_url'] : $this->options['client_options']['site'].$this->options['client_options']['authorize_path'];
    
    return $params ? $authorizeUrl.'?'.http_build_query($params) : $authorizeUrl;
  }

  protected function requestTokenUrl($params = array())
  {
    $requestTokenUrl = isset($this->options['client_options']['request_token_url']) ? $this->options['client_options']['request_token_url'] : $this->options['client_options']['site'].$this->options['client_options']['request_token_path'];
    
    return $params ? $requestTokenUrl.'?'.http_build_query($params) : $requestTokenUrl;
  }
}
