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
        $fields = new FieldsBag([
            'name' => true,
            'email' => true
        ]);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertFalse($fields->has('phone'));
    }

    public function testHasField()
    {
        $fields = new FieldsBag([
            'name' => true,
            'email' => true
        ]);

        $fields['translates'] = new FieldsBag([
            'id' => true,
            'title' => true
        ]);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertTrue($fields->has('translates'));
        $this->assertTrue($fields->has('translates.id'));
        $this->assertTrue($fields->has('translates.title'));
        $this->assertFalse($fields->has('translates.program'));
        $this->assertFalse($fields->has('phone'));
    }

    public function testGenerateKeys()
    {
        $fields = new FieldsBag([
            'name' => true,
            'email' => true
        ]);

        $fields['translates'] = new FieldsBag([
            'id' => true,
            'title' => true
        ]);

        $this->assertFalse($fields->has('id'));

        $fields->addParameter('id');

        $this->assertTrue($fields->has('id'));
    }

    public function testDefaults()
    {
        $fields = new FieldsBag([
            'name' => true,
            'email' => true
        ]);

        $fields->setDefaults(['name', 'email', 'phone']);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertFalse($fields->has('phone'));

        $fields = new FieldsBag([]);

        $fields->setDefaults(['name', 'email', 'phone']);

        $this->assertTrue($fields->has('name'));
        $this->assertTrue($fields->has('email'));
        $this->assertTrue($fields->has('phone'));

        $fields = new FieldsBag([
            'summary' => true
        ]);

        $fields->setDefaults(['name', 'email', 'phone']);

        $this->assertTrue($fields->has('summary'));
        $this->assertFalse($fields->has('name'));
        $this->assertFalse($fields->has('email'));
        $this->assertFalse($fields->has('phone'));
    }
}
