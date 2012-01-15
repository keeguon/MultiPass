<?php

namespace MultiPass;

class MultiPass
{
  public $provider = null;
  public $options  = array();

  public function __construct($provider, $opts)
  {
    // Default options

    // Handle
    $provider     = ucfirst(strtolower($provider));
    $clientId     = $opts['client_id'];
    $clientSecret = $opts['client_secret'];
    unset($opts['client_id'], $opts['client_secret']);

    // Instanciate provider
    $strategy       = "\MultiPass\Strategies\\$provider";
    $this->provider = new $strategy($clientId, $clientSecret, $opts);
  }

  public function callbackPhase()
  {
    $this->provider->callbackPhase();
    return $this->provider->authHash();
  }

  public function requestPhase()
  {
    $this->provider->requestPhase();
  }
}
