<?php

namespace MultiPass\Strategies;

/**
 *
 * (c) Alberto Aldegheri <albyrock87+dev[at]gmail.com>
 *
 * Configure with:
 * google:
 *    client_id: "MYID.apps.googleusercontent.com"
 *    client_secret: "mysecret"
 */

class Google extends \MultiPass\Strategies\OAuth2
{
  protected $name = 'google';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'          => 'https://'
          , 'token_url'     => 'accounts.google.com/o/oauth2/token'
          , 'authorize_url' => 'accounts.google.com/o/oauth2/auth'
          , 'token_method'  => 'POST'
        )
      , 'authorize_params' => array(
          'response_type' => 'code',
          'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
        )
      , 'token_params' => array(
            'parse' => 'json'
        )
      , 'token_options' => array(
            'mode'       => 'query'
          , 'param_name' => 'code'
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
        'email'       => $rawInfo['email']
      , 'name'        => empty($rawInfo['name']) ?
                            $rawInfo['given_name'].' '.$rawInfo['family_name'] :
                            $rawInfo['name']
      , 'first_name'  => $rawInfo['given_name']
      , 'last_name'   => $rawInfo['family_name']
      , 'image'       => $rawInfo['picture']
      , 'gender'      => $rawInfo['gender']
    );
  }

  protected function rawInfo()
  {
    try {
      $response = $this->accessToken->get('https://www.googleapis.com/oauth2/v1/userinfo', array('parse' => 'json', 'params' => array('access_token'=>$this->accessToken->getToken())));
      $parsedResponse = $response->parse();
      return $parsedResponse;
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}
