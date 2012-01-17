<?php

namespace MultiPass;

class AuthHash
{
  public $credentials = array();
  public $extra       = array();
  public $info        = array();
  public $provider    = '';
  public $uid         = '';

  public function __construct($provider, $uid, $info = array())
  {
    $this->provider = strtolower($provider);
    $this->uid      = $uid;
    if ($info) {
      $this->info = $info;
    }
  }

  public function getName()
  {
    // return name if available
    if (!empty($this->info['name'])) {
      return $this->info['name'];
    }

    // concatenate first_name and last_name as a fallback
    if (!empty($this->info['first_name']) && !empty($this->info['last_name'])) {
      return "{$this->info['first_name']} {$this->info['last_name']}";
    }

    // only display first_name or last_name if only that is available
    if (!empty($this->info['first_name']) && empty($this->info['last_name'])) {
      return $this->info['first_name'];
    }
    if (empty($this->info['first_name']) && !empty($this->info['last_name'])) {
      return $this->info['last_name'];
    }

    // return nickname if no name, first or last is available
    if (!empty($this->info['nickname'])) {
      return $this->info['nickname'];
    }

    // return the email if no name, first, last, or nick is available
    if (!empty($this->info['email'])) {
      return $this->info['email'];
    }

    return null;
  }

  public function isValid()
  {
    return ($this->uid && $this->provider && $this->info && $this->getName());
  }

  public function toArray()
  {
    // put all properties in an array
    $array = array(
        'provider'    => $this->provider
      , 'uid'         => $this->uid
      , 'info'        => $this->info
      , 'credentials' => $this->credentials
      , 'extra'       => $this->extra
    );

    // making sure that the name attribute is set
    $array['info']['name'] = !empty($array['info']['name']) ? $array['info']['name'] : $this->getName();

    return $array;
  }
}
