<?php

namespace MultiPass;

class AuthHash
{
  protected
      $provider = ''
    , $uid      = ''
  ;

  public
      $credentials = array()
    , $info        = array()
    , $raw_info    = array()
  ;

  public function __construct($provider, $uid)
  {
    $this->provider = strtolower($provider);
    $this->uid      = $uid;
  }

  public function toArray()
  {
    return array(
        'provider'    => $this->provider
      , 'uid'         => $this->uid
      , 'info'        => $this->info
      , 'credentials' => $this->credentials
      , 'extra'       => array(
            'raw_info' => $this->raw_info
        )
    );
  }
}
