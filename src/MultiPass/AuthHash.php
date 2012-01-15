<?php

namespace MultiPass;

class AuthHash
{
  public $credentials = array();
  public $info        = array();
  public $provider    = '';
  public $rawInfo     = array();
  public $uid         = '';

  public function __construct($provider, $uid)
  {
    $this->provider = strtolower($provider);
    $this->uid      = $uid;
  }

  public function isValid()
  {
    return ($this->uid && $this->provider && $this->info && array_key_exists('name', $this->info));
  }

  public function toArray()
  {
    return array(
        'provider'    => $this->provider
      , 'uid'         => $this->uid
      , 'info'        => $this->info
      , 'credentials' => $this->credentials
      , 'extra'       => $this->rawInfo
    );
  }
}
