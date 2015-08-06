<?php
/*
 * This file is part of the RestServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider\Rest;

use Euskadi31\Silex\Provider\Rest\FieldsParser;

class FieldsParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserWithSimpleFields()
    {
        $parser = new FieldsParser;
        $fields = $parser->parse('name,email');

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertFalse($fields->has('phone'));
    }

    public function testParserWithComplexFields()
    {
        $parser = new FieldsParser;
        $fields = $parser->parse('name, email,user{id, name},create_at');

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertTrue($fields->has('user'));
        $this->assertInstanceOf('Euskadi31\Silex\Provider\Rest\FieldsBag', $fields['user']);
        $this->assertTrue($fields['user']->has('id'));
        $this->assertTrue($fields['user']->has('name'));
        $this->assertFalse($fields['user']->has('phone'));
        $this->assertFalse($fields->has('phone'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Syntax error for the right syntax to use near 'nt{id' at col 33
     */
    public function testParserWithBadFields()
    {
        $parser = new FieldsParser;
        $parser->parse('name,email, user{id,name,comment{id}}');
    }
}
