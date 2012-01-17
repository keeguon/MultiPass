<?php

namespace MultiPass\Tests;

class AuthHashTest extends \MultiPass\Tests\TestCase
{
 /**
  * @var MultiPass\AuthHash
  */
  protected $authHash;

 /**
  * Setup fixtures
  */
  protected function setUp()
  {
    $this->authHash = new \MultiPass\AuthHash('example', '123', array('name' => 'Steven'));
  }

  protected function tearDown()
  {
    unset($this->authHash);
  }

 /**
  * @covers MultiPass\AuthHash::isValid()
  */
  public function testIsValid()
  {
    // should be valid with the right parameters
    $this->assertTrue($this->authHash->isValid());

    // should require a uid
    $this->authHash->uid = null;
    $this->assertFalse($this->authHash->isValid());
    $this->authHash->uid = '123';

    // should require a provider
    $this->authHash->provider = null;
    $this->assertFalse($this->authHash->isValid());
    $this->authHash->provider = 'example';

    // should require a name in the user info hash
    $this->authHash->info['name'] = null;
    $this->assertFalse($this->authHash->isValid());
    $this->authHash->info['name'] = 'Steve';
  }

 /**
  * @covers MultiPass\AuthHash::getName()
  */
  public function testName()
  {
    // redefine info hash
    $this->authHash->info = array(
        'name'       => 'Phillip J. Fry'
      , 'first_name' => 'Phillip'
      , 'last_name'  => 'Fry'
      , 'nickname'   => 'meatbag'
      , 'email'      => 'fry@planetexpress.com'
    );

    // should default to the name key
    $this->assertEquals('Phillip J. Fry', $this->authHash->getName());

    // should fall back to go to first_name last_name concatenation
    $this->authHash->info['name'] = null;
    $this->assertEquals('Phillip Fry', $this->authHash->getName());

    // should display only a first or last name if only that is available
    $this->authHash->info['first_name'] = null;
    $this->assertEquals('Fry', $this->authHash->getName());

    // should display the nickname if no name, first, or last is available
    $this->authHash->info['last_name'] = null;
    $this->assertEquals('meatbag', $this->authHash->getName());

    // should display the email if no name, first, last, or nick is available
    $this->authHash->info['nickname'] = null;
    $this->assertEquals('fry@planetexpress.com', $this->authHash->getName());
  }

 /**
  * @covers MultiPass\AuthHash::toArray()
  */
  public function testToArray()
  {
    $this->authHash = new \MultiPass\AuthHash('test', '123', array('name' => 'Bob Example'));

    // should be a plain old array
    $this->assertInternalType('array', $this->authHash->toArray());

    // should have string keys
    $this->assertArrayHasKey('uid', $this->authHash->toArray());

    // info hash should also be a plain old array
    $this->authHash->info = array('first_name' => 'Bob', 'last_name' => 'Example');
    $authHashArray = $this->authHash->toArray();
    $this->assertInternalType('array', $authHashArray['info']);

    // should supply the calculated name in the converted hash
    $this->authHash->info = array('first_name' => 'Bob', 'last_name' => 'Examplar');
    $authHashArray = $this->authHash->toArray();
    $this->assertEquals('Bob Examplar', $authHashArray['info']['name']);
  }
}
