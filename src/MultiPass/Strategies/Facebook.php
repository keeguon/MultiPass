<?php

namespace MultiPass\Strategies;

class Facebook extends \MultiPass\Strategies\OAuth2
{
  const DEFAULT_SCOPE = 'email,offline_access';

  public $name = 'facebook';

  public function __construct($opts)
  {
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'      => 'https://graph.facebook.com'
          , 'token_url' => '/oauth/access_token'
        )
      , 'token_params' => array(
            'parse' => 'query'
        )
      , 'token_options' => array(
            'header_format' => 'OAuth %s'
          , 'param_name'    => 'access_token'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function uid($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();
 
    return $rawInfo['id'];
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return array(
        'nickname'    => $rawInfo['username']
      , 'email'       => $rawInfo['email']
      , 'name'        => $rawInfo['name']
      , 'first_name'  => $rawInfo['first_name']
      , 'last_name'   => $rawInfo['last_name']
      , 'image'       => "http://graph.facebook.com/{$rawInfo['id']}/picture?type=square"
      , 'description' => $rawInfo['bio']
      , 'urls'        => array(
            'Facebook' => $rawInfo['link']
          , 'Website'  => isset($rawInfo['website']) ? $rawInfo['website'] : null
        )
      , 'location'    => isset($rawInfo['location']) ? $rawInfo['location']['name'] : null
    );
  }
  
  public function authorizeParams()
  {
    $params = parent::authorizeParams();
    $params['scope'] = array_key_exists('scope', $params) ? $params['scope'] : self::DEFAULT_SCOPE;

    return $params;
  }


  protected function rawInfo()
  {
    try {
      $response = $this->accessToken->get('/me', array('parse' => 'json'));
      return $response->parse();
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
