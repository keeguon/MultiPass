<?php

namespace MultiPass\Strategies;

class Dropbox extends \MultiPass\Strategies\OAuth2
{
  protected $name = 'dropbox';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'          => 'https://api.dropbox.com'
          , 'token_url'     => '/1/oauth2/token'
          , 'authorize_url' => 'https://www.dropbox.com/1/oauth2/authorize'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function authorizeParams()
  {
    $params = parent::authorizeParams();
    if (isset($_REQUEST['state']) && $_REQUEST['state'] !== '') {
      $params['state'] = $_REQUEST['state'];
    }
    return $params;
  }

  public function uid($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();
    return $rawInfo['uid'];
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();

    return array(
      'referral_link' => $rawInfo['referral_link'],
      'display_name'=> $rawInfo['display_name'],
      'country'=> $rawInfo['country'],
      'quota_info' => $rawInfo['quota_info'],
    );
  }

  protected function rawInfo()
  {
    $response = $this->accessToken->get('/1/account/info', array('parse' => 'json'));
    $parsedResponse = $response->parse();
    return $parsedResponse;
  }
}
