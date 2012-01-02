<?php

namespace MultiPass\Strategy;

class Instagram extends \MultiPass\Strategy\OAuth2
{
  const DEFAULT_SCOPE = 'basic';

  public
      $name = 'Instagram'
  ;

  public function __construct($client_id, $client_secret, $opts)
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options'       => array(
            'site'      => 'https://api.instagram.com'
          , 'token_url' => '/oauth/access_token'
        )
      , 'token_params'         => array(
            'parse' => 'json'
        )
      , 'access_token_options' => array()
      , 'authorize_options'    => array(
            'scope' => self::DEFAULT_SCOPE
        )
    ), $opts);

    parent::__construct($client_id, $client_secret, $this->options);
  }

  public function info($raw_info = null)
  {
    $raw_info = $raw_info ?: $this->raw_info();

    return array(
        'nickname'    => $raw_info['username']
      , 'name'        => $raw_info['full_name']
      , 'image'       => $raw_info['profile_picture']
      , 'description' => $raw_info['bio']
      , 'urls'        => array(
            'Website' => $raw_info['website']
        )
    );
  }


  protected function raw_info()
  {
    try {
      $response       = $this->token->get($this->client->site.'/v1/users/self', array('parse' => 'json'));
      $parsedResponse = $response->parse();
      return $parsedResponse['data'];
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
