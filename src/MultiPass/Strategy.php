<?php

namespace MultiPass;

abstract class Strategy
{
  public $options = array();

  protected $name = '';

  public function __construct($opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'path_prefix' => '/auth'
      , 'skip_info'   => false
    ), $opts);
  }

  abstract public function requestPhase();

  abstract public function uid();

  abstract public function info();

  abstract public function credentials();

  abstract public function extra();

  public function authHash() {
    $hash              = new \MultiPass\AuthHash(array('provider' => $this->name, 'uid' => $this->uid()));
    if (false === $this->options['skip_info']) $hash->info = $this->info();
    $hash->credentials = $this->credentials() || null;
    $hash->extra       = $this->extra() || null;
    
    return $hash;
  }

  public function callbackPhase() {
    return $this->authHash();
  }

  public function getPathPrefix()
  {
    return $this->options['path_prefix'] ?: '/auth';
  }

  public function getRequestPath()
  {
    return $this->options['request_path'] ?: $this->getPathPrefix().'/'.$this->name;
  }

  public function getCallbackPath()
  {
    return $this->options['callback_path'] ?: $this->getPathPrefix().'/'.$this->name.'/callback';
  }

  public function getCurrentPath()
  {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
    
    return $parsedUrl['path'];
  }

  public function getQueryString()
  {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
    
    return $parsedUrl['query'];
  }

  public function getFullHost()
  {
    return $_SERVER['HTTPS'] === 'off' ? 'http://'.$_SERVER['HTTP_HOST'] : 'https://'.$_SERVER['HTTP_HOST'];
  }

  public function getCallbackUrl()
  {
    return $this->getFullHost().$this->getCallbackPath().'?'.$this->getQueryString();
  }

  public function getName()
  {
    return $this->name;
  }
}
