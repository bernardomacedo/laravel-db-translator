<?php

namespace bernardomacedo\DBTranslator\Test;

use bernardomacedo\DBTranslator\Test\TestCase;

class DBTranslatorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_single_translation()
    {
        // test simple translation
        $this->assertEquals('db translator is good', lang('db translator is good'));
    }

    public function test_translation_with_variable()
    {
        $this->assertEquals('My name is Bernardo Macedo', lang('My name is :name', ['name' => 'Bernardo Macedo']));
    }

    public function test_translation_with_multiple_variables()
    {
        $this->assertEquals('My name is Bernardo Macedo', lang('My name is :first :last', ['first' => 'Bernardo', 'last' => 'Macedo']));
    }

    public function test_one_apple()
    {
        $this->assertEquals('One apple', lang('One apple|Many apples', [], 1));
        $this->assertEquals('One apple named Bernardo', lang('One apple named :name|Many apples named :name', ['name' => 'Bernardo'], 1));
    }

    public function test_many_apples()
    {
        $this->assertEquals('Many apples', lang('One apple|Many apples', [], 2));
        $this->assertEquals('Many apples', lang('One apple|Many apples', null, 2));
    }

    public function test_many_apples_with_name()
    {
        $this->assertEquals('Many apples named Bernardo', lang('One apple named :name|Many apples named :name', ['name' => 'Bernardo'], 2));
    }
}