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

use Euskadi31\Silex\Provider\Rest\FieldsBag;

class FieldsBagTest extends \PHPUnit_Framework_TestCase
{
    public function testFields()
    {
        $fields = new FieldsBag(['name', 'email']);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertFalse($fields->has('phone'));
    }

    public function testDefaults()
    {
        $fields = new FieldsBag(['name', 'email']);

        $fields->setDefaults(['name', 'email', 'phone']);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertFalse($fields->has('phone'));

        $fields = new FieldsBag([]);

        $fields->setDefaults(['name', 'email', 'phone']);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertTrue($fields->has('phone'));

        $fields = new FieldsBag(['summary']);

        $fields->setDefaults(['name', 'email', 'phone']);

        $this->assertTrue($fields->has('summary'));
        $this->assertFalse($fields->has('name'));
        $this->assertFalse($fields->has('email'));
        $this->assertFalse($fields->has('phone'));
    }

}
