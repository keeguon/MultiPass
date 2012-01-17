<?php

namespace MultiPass\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
 /**
  * Assert that two arrays are equals even if not ordered
  *
  * @param  array   $expected The reference array to test against
  * @param  array   $actual   The array we're testing
  * @return boolean
  */
  protected function assertArrayEquals($expected, $actual)
  {
    $this->deepKsort($expected);
    $this->deepKsort($actual);

    return $expected === $actual;
  }

 /**
  * Deeply sort array by keys
  *
  * @param $array The array we're going to ksort
  */
  private function deepKsort(&$array)
  {
    if (!is_array($array)) {
      return false;
    }

    ksort($array);
    foreach ($array as $k => $v) {
      $this->deepKsort($array[$k]);
    }

    return true;
  }
}
