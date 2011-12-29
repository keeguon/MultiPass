<?php

namespace MultiPass\Strategy;

class Facebook extends \MultiPass\Strategy\OAuth2
{
  const DEFAULT_SCOPE = 'email,offline_access';

  public function __construct($client_id, $client_secret, $opts)
  {
    $this->options = array_merge_recursive(array(
        'client_options'       => array(
            'site'      => 'https://graph.facebook.com'
          , 'token_url' => '/oauth/access_token'
        )
      , 'token_params'         => array(
            'parse' => 'query'
        )
      , 'access_token_options' => array(
            'header_format' => 'OAuth %s'
          , 'param_name'    => 'access_token'
        )
      , 'authorize_options'    => array(
            'scope' => self::DEFAULT_SCOPE
        )
    ), $opts);

    parent::__construct($client_id, $client_secret, $this->options);
  }
}
