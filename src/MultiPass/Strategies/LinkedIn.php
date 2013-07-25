<?php

namespace MultiPass\Strategies;

class LinkedIn extends \MultiPass\Strategies\OAuth2
{
  public $name = 'linkedin';

  public function __construct($opts = array())
  {
    // Default options
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'          => 'https://www.linkedin.com/uas/oauth2'
          , 'token_url'     => '/accessToken'
          , 'authorize_url' => '/authorization'
          , 'fields'        => 'id,first-name,last-name,picture-url,site-standard-profile-request'
        ),
        'token_params' => array(
          'parse' => 'json',
        ),
        'token_options' => array(
            'mode' => 'query'
          , 'param_name' => 'oauth2_access_token'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function authorizeParams()
  {
    $params = parent::authorizeParams();
    if (isset($_REQUEST['state']) && $_REQUEST['state'] !== '') {
      $params['state'] = $_REQUEST['state'];
    } else {
      // State is required in LinkedIn
      $params['state'] =
        $_SESSION['oauth'][$this->name]['state'] =
          uniqid($this->name);
    }
    return $params;
  }

  public function uid($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();
    //$matches = NULL;
    // Get number ID from siteStandardProfile
    //if (isset($rawInfo['siteStandardProfileRequest']['url'])) {
    //  preg_match('/id=(\d+)/', $rawInfo['siteStandardProfileRequest']['url'], $matches);
    //}
    //
    //if ($matches) {
    //  return $matches[1];
    //} else
    //  // This ID is changing, if you change client_id
      return $rawInfo['id'];
  }

  public function info($rawInfo = null)
  {
    $rawInfo = $rawInfo ?: $this->rawInfo();
    $firstName = isset($rawInfo['firstName']) ? $rawInfo['firstName'] : '';
    $lastName = isset($rawInfo['lastName']) ? $rawInfo['lastName'] : '';
    return array(
      'id' => isset($rawInfo['id']) ? $rawInfo['id'] : NULL,
      'first_name' => $firstName,
      'last_name' => $lastName,
      'name' => empty($rawInfo['name'])
        ? $firstName . ' ' . $lastName
        : $rawInfo['name'],
      'email' => isset($rawInfo['emailAddress']) ? $rawInfo['emailAddress'] : NULL,
      'image' => isset($rawInfo['pictureUrl']) ? $rawInfo['pictureUrl'] : NULL,
    );
  }

  public function callbackPhase() {
    if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth'][$this->name]['state']) {
      throw new \MultiPass\Error\CSRFError('CSRF protection', 'Returned "state" value is not correct. Possible CSRF');
    }
    return parent::callbackPhase();
  }

  protected function rawInfo()
  {
    $response = $this->accessToken->get('https://api.linkedin.com/v1/people/~:(' . $this->options['client_options']['fields'] . ')', array(
      'parse' => 'json',
      'params' => array( 'format' => 'json' )
    ));
    return $response->parse();
  }
}
