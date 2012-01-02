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
    return array_merge_recursive(array('provider' => $this->provider, 'uid' => $this->uid), $this->info, $this->credentials, $this->raw_info);
  }
}
