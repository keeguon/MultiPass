<?php

namespace MultiPass;

class MultiPass
{
  protected
      $provider = null
  ;

  public function __construct($provider, $opts)
  {
    // Handle
    $provider      = ucfirst(strtolower($provider));
    $client_id     = $opts['client_id'];
    $client_secret = $opts['client_secret'];
    unset($opts['client_id'], $opts['client_secret']);

    // Instanciate provider
    $this->provider = new \MultiPass\Strategy\\$provider($client_id, $client_secret, $opts);
  }

  public function callback_phase()
  {
    $this->provider->callback_phase();
  }

  public function request_phase()
  {
    $this->provider->request_phase();
  }
}
