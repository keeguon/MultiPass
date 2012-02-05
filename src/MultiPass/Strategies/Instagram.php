<?php

namespace MultiPass\Strategies;

class Instagram extends \MultiPass\Strategies\OAuth2
{
  const DEFAULT_SCOPE = 'basic';

  protected $name = 'instagram';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'      => 'https://api.instagram.com'
          , 'token_url' => '/oauth/access_token'
        )
      , 'token_params' => array(
            'parse' => 'json'
        )
      , 'token_options' => array(
            'mode'       => 'query'
          , 'param_name' => 'access_token'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return array(
        'nickname'    => $rawInfo['username']
      , 'name'        => $rawInfo['full_name']
      , 'image'       => $rawInfo['profile_picture']
      , 'description' => $rawInfo['bio']
      , 'urls'        => array(
            'Website' => $rawInfo['website']
        )
    );
  }

  public function authorizeParams()
  {
    $params = parent::authorizeParams();
    $params['scope'] = isset($params['scope']) ? $params['scope'] : self::DEFAULT_SCOPE;

    return $params;
  }

  protected function rawInfo()
  {
    try {
      $response       = $this->accessToken->get('/v1/users/self', array('parse' => 'json'));
      $parsedResponse = $response->parse();
      return $parsedResponse['data'];
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
