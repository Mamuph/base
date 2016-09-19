<?php


class TestSystemArr extends PHPUnit_Framework_TestCase
{

    /**
     * Test if array is associative
     *
     * @group mamuph.system.arr
     */
    public function test_is_associative()
    {
        $this->assertTrue(Arr::is_assoc(array('foo' => 'bar')));
        $this->assertFalse(Arr::is_assoc(array('foo', 'bar')));
    }


    /**
     * Test if element is an array
     *
     * @group mamuph.system.arr
     */
    public function test_is_array()
    {
        $this->assertTrue(Arr::is_array(array('foo')));
        $this->assertFalse(Arr::is_array(new stdClass()));
    }


}