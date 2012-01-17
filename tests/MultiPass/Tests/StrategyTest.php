<?php

namespace MultiPass\Tests;

class ExampleStrategy extends \MultiPass\Strategy
{
  protected $name = 'example';

  public function __construct($opts = array())
  {
    parent::__construct($opts);

    // Default options
    $this->options = array_replace_recursive(array(
        'wakka' => 'doo'
    ), $this->options);
  }

  public function requestPhase() {}

  public function uid() {}
  
  public function info() {}
  
  public function credentials() {}
  
  public function extra() {}
}

class StrategyTest extends \MultiPass\Tests\TestCase
{
 /**
  * @var MultiPass\Strategy
  */
  protected $strategy;

 /**
  * Set up fixtures
  */
  protected function setUp()
  {
    $this->strategy = $this->getMock('MultiPass\Tests\ExampleStrategy', array('authHash'), array(array('foo' => 'bar')));

    $this->strategy->expects($this->any())
                   ->method('authHash')
                   ->will($this->returnValue('AUTH HASH'));
  }

  protected function tearDown()
  {
    unset($this->strategy);
  }

 /**
  * @cover MultiPass\Strategy::__construct()
  */
  public function testConstructorBuildsStrategy()
  {
    // should be a subclass of MultiPass\Strategy
    $this->assertTrue(is_subclass_of($this->strategy, '\MultiPass\Strategy'));

    // options should be inherited from parent
    $this->assertEquals('bar', $this->strategy->options['foo']);

    // contructor should also set default options for the object 
    $this->assertEquals('doo', $this->strategy->options['wakka']);
  }

 /**
  * @cover MultiPass\Strategy::configure()
  */
  public function testConfigure()
  {
    // should take a hash and deep merge it
    $this->strategy->configure(array('abc' => array('def' => 123)));
    $this->strategy->configure(array('abc' => array('ghi' => 456)));
    $this->assertArrayEquals(array('abc' => array('def' => 123, 'ghi' => 456)), $this->strategy->options['abc']);
  }

 /**
  * @cover MultiPass\Strategy::callbackPhase()
  */
  public function testCallbackPhase()
  {
    // should set the auth hash
    $this->assertEquals('AUTH HASH', $this->strategy->callbackPhase());
  }

 /**
  * @cover MultiPass\Strategy::getPathPrefix()
  * @cover MultiPass\Strategy::getRequestPath()
  * @cover MultiPass\Strategy::getCallbackPath()
  */
  public function testPathMethods()
  {
    // path_prefix should default to '/auth'
    $this->assertEquals('/auth', $this->strategy->getPathPrefix());

    // custom path_prefix
    $this->strategy->options['path_prefix'] = '/connect';
    $this->assertEquals('/connect', $this->strategy->getPathPrefix());

    // request_path should default to "{$path_prefix}/{$name}"
    $this->assertEquals('/connect/example', $this->strategy->getRequestPath());

    // custom request_path
    $this->strategy->options['request_path'] = "/{$this->strategy->getName()}/request";
    $this->assertEquals('/example/request', $this->strategy->getRequestPath());

    // callback_path should default to "{$path_prefix}/{$name}/callback"
    $this->assertEquals('/connect/example/callback', $this->strategy->getCallbackPath());
    
    // custom callback_path
    $this->strategy->options['request_path'] = "/{$this->strategy->getName()}/callback";
    $this->assertEquals('/example/callback', $this->strategy->getRequestPath());
  }

 /**
  * @cover MultiPass\Strategy::getName()
  */
  public function testGetName()
  {
    $this->assertEquals('example', $this->strategy->getName());
  }
}
