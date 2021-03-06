<?php

namespace MultiPass;

abstract class Strategy
{
  public $options = array();

  protected $name = '';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'path_prefix' => '/auth'
      , 'skip_info'   => false
    ), $opts);
  }

  public function configure($opts = array())
  {
    $this->options = array_replace_recursive($opts, $this->options);
  }

  abstract public function getAuthType();

  abstract public function requestPhase();

  abstract public function uid();

  abstract public function info();

  abstract public function credentials();

  abstract public function extra();

  public function authHash() {
    $hash              = new \MultiPass\AuthHash($this->name, $this->uid());
    if (false === $this->options['skip_info']) {
      $hash->info = $this->info();
    }
    $hash->credentials = $this->credentials() ?: null;
    $hash->extra       = $this->extra() ?: null;

    return $hash;
  }

  public function callbackPhase() {
    return $this->authHash();
  }

  public function getPathPrefix()
  {
    return array_key_exists('path_prefix', $this->options) ? $this->options['path_prefix'] : '/auth';
  }

  public function getRequestPath()
  {
    return array_key_exists('request_path', $this->options) ? $this->options['request_path'] : $this->getPathPrefix().'/'.$this->name;
  }

  public function getCallbackPath()
  {
    return array_key_exists('callback_path', $this->options) ? $this->options['callback_path'] : $this->getPathPrefix().'/'.$this->name.'/callback';
  }

  public function getCurrentPath()
  {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

    return $parsedUrl['path'];
  }

  public function getQueryString()
  {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

    return isset($parsedUrl['query']) ? $parsedUrl['query'] : null;
  }

  public function getFullHost()
  {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'];
  }

  public function getCallbackUrl()
  {
    return $this->getQueryString() ? $this->getFullHost().$this->getCallbackPath().'?'.$this->getQueryString() : $this->getFullHost().$this->getCallbackPath();
  }

  public function getName()
  {
    return $this->name;
  }
}
