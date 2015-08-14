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
use Euskadi31\Silex\Provider\Rest\FieldsFilter;
use DateTime;
use ArrayObject;
use JsonSerializable;

class FieldsFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $data = [];

        $data[] = [
            'id' => 1,
            'user' => [
                'id' => 12,
                'firstname' => 'Axel',
                'lastname'  => 'Etcheverry',
                'email'     => 'axel@domain.com',
                'phone'     => '+33000000000'
            ],
            'translates' => [
                new ArrayObject([
                    'id'            => 1234,
                    'locale'        => 'fr',
                    'title'         => 'Test title',
                    'experience'    => 'Bla bla bla',
                    'program'       => 'Bla Bla',
                    'material'      => 'Bla'
                ])
            ],
            'create_at' => (new DateTime('now'))->format(DateTime::ISO8601),
            'update_at' => (new DateTime('now'))->format(DateTime::ISO8601),
        ];

        $data[] = [
            'id' => 2,
            'user' => [
                'id' => 122,
                'firstname' => 'Rui',
                'lastname'  => 'Avelino',
                'email'     => 'rui@domain.com',
                'phone'     => '+33000000000'
            ],
            'translates' => [
                [
                    'id'            => 12342,
                    'locale'        => 'fr',
                    'title'         => 'Test title 2',
                    'experience'    => 'Bla bla bla',
                    'program'       => 'Bla Bla',
                    'material'      => 'Bla'
                ]
            ],
            'create_at' => (new DateTime('now'))->format(DateTime::ISO8601),
            'update_at' => (new DateTime('now'))->format(DateTime::ISO8601),
        ];

        $parser = new FieldsParser;
        $fields = $parser->parse('translates{locale,title,program,material},user{id,firstname,email},create_at');

        $filter = new FieldsFilter($fields);
        $dataFiltered = $filter->filter($data);

        $this->assertCount(2, $dataFiltered);

        $this->assertArrayHasKey('id', $dataFiltered[0]);
        $this->assertArrayHasKey('user', $dataFiltered[0]);
        $this->assertArrayHasKey('translates', $dataFiltered[0]);
        $this->assertArrayHasKey('create_at', $dataFiltered[0]);
        $this->assertArrayNotHasKey('update_at', $dataFiltered[0]);

        $this->assertArrayHasKey('id', $dataFiltered[0]['user']);
        $this->assertArrayHasKey('firstname', $dataFiltered[0]['user']);
        $this->assertArrayHasKey('email', $dataFiltered[0]['user']);
        $this->assertArrayNotHasKey('lastname', $dataFiltered[0]['user']);
        $this->assertArrayNotHasKey('phone', $dataFiltered[0]['user']);


        $this->assertArrayHasKey('id', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('locale', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('title', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('program', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('material', $dataFiltered[0]['translates'][0]);
        $this->assertArrayNotHasKey('experience', $dataFiltered[0]['translates'][0]);


        $this->assertArrayHasKey('id', $dataFiltered[1]);
        $this->assertArrayHasKey('user', $dataFiltered[1]);
        $this->assertArrayHasKey('translates', $dataFiltered[1]);
        $this->assertArrayHasKey('create_at', $dataFiltered[1]);
        $this->assertArrayNotHasKey('update_at', $dataFiltered[1]);

        $this->assertArrayHasKey('id', $dataFiltered[1]['user']);
        $this->assertArrayHasKey('firstname', $dataFiltered[1]['user']);
        $this->assertArrayHasKey('email', $dataFiltered[1]['user']);
        $this->assertArrayNotHasKey('lastname', $dataFiltered[1]['user']);
        $this->assertArrayNotHasKey('phone', $dataFiltered[1]['user']);

        $this->assertArrayHasKey('id', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('locale', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('title', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('program', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('material', $dataFiltered[1]['translates'][0]);
        $this->assertArrayNotHasKey('experience', $dataFiltered[1]['translates'][0]);
    }

    public function testFilterObject()
    {
        $data = [];

        $data[] = [
            'id' => 1,
            'user' => [
                'id' => 12,
                'firstname' => 'Axel',
                'lastname'  => 'Etcheverry',
                'email'     => 'axel@domain.com',
                'phone'     => '+33000000000'
            ],
            'translates' => [
                new ArrayObject([
                    'id'            => 1234,
                    'locale'        => 'fr',
                    'title'         => 'Test title',
                    'experience'    => 'Bla bla bla',
                    'program'       => 'Bla Bla',
                    'material'      => 'Bla'
                ])
            ],
            'create_at' => (new DateTime('now'))->format(DateTime::ISO8601),
            'update_at' => (new DateTime('now'))->format(DateTime::ISO8601),
        ];

        $data[] = [
            'id' => 2,
            'user' => [
                'id' => 122,
                'firstname' => 'Rui',
                'lastname'  => 'Avelino',
                'email'     => 'rui@domain.com',
                'phone'     => '+33000000000'
            ],
            'translates' => [
                [
                    'id'            => 12342,
                    'locale'        => 'fr',
                    'title'         => 'Test title 2',
                    'experience'    => 'Bla bla bla',
                    'program'       => 'Bla Bla',
                    'material'      => 'Bla'
                ]
            ],
            'create_at' => (new DateTime('now'))->format(DateTime::ISO8601),
            'update_at' => (new DateTime('now'))->format(DateTime::ISO8601),
        ];

        $parser = new FieldsParser;
        $fields = $parser->parse('translates,user{id,firstname,email},create_at');

        $filter = new FieldsFilter($fields);
        $dataFiltered = $filter->filter($data);

        $this->assertCount(2, $dataFiltered);

        $this->assertArrayHasKey('id', $dataFiltered[0]);
        $this->assertArrayHasKey('user', $dataFiltered[0]);
        $this->assertArrayHasKey('translates', $dataFiltered[0]);
        $this->assertArrayHasKey('create_at', $dataFiltered[0]);
        $this->assertArrayNotHasKey('update_at', $dataFiltered[0]);

        $this->assertArrayHasKey('id', $dataFiltered[0]['user']);
        $this->assertArrayHasKey('firstname', $dataFiltered[0]['user']);
        $this->assertArrayHasKey('email', $dataFiltered[0]['user']);
        $this->assertArrayNotHasKey('lastname', $dataFiltered[0]['user']);
        $this->assertArrayNotHasKey('phone', $dataFiltered[0]['user']);


        $this->assertArrayHasKey('id', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('locale', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('title', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('program', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('material', $dataFiltered[0]['translates'][0]);
        $this->assertArrayHasKey('experience', $dataFiltered[0]['translates'][0]);


        $this->assertArrayHasKey('id', $dataFiltered[1]);
        $this->assertArrayHasKey('user', $dataFiltered[1]);
        $this->assertArrayHasKey('translates', $dataFiltered[1]);
        $this->assertArrayHasKey('create_at', $dataFiltered[1]);
        $this->assertArrayNotHasKey('update_at', $dataFiltered[1]);

        $this->assertArrayHasKey('id', $dataFiltered[1]['user']);
        $this->assertArrayHasKey('firstname', $dataFiltered[1]['user']);
        $this->assertArrayHasKey('email', $dataFiltered[1]['user']);
        $this->assertArrayNotHasKey('lastname', $dataFiltered[1]['user']);
        $this->assertArrayNotHasKey('phone', $dataFiltered[1]['user']);

        $this->assertArrayHasKey('id', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('locale', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('title', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('program', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('material', $dataFiltered[1]['translates'][0]);
        $this->assertArrayHasKey('experience', $dataFiltered[1]['translates'][0]);
    }

    public function testFilterObjectList()
    {
        $data = new ArrayObject();

        $data[] = new ArrayObject([
            'id'        => 1234,
            'foo'       => 'bar',
            'create_at' => (new DateTime('now'))->format(DateTime::ISO8601),
            'roles'     => [
                'foo' => 'bar'
            ],
            'tags'      => ['foo']
        ]);

        $parser = new FieldsParser;
        $fields = $parser->parse('foo,roles{foo}');

        $filter = new FieldsFilter($fields);
        $dataFiltered = $filter->filter($data);

        $this->assertCount(1, $dataFiltered);

        $this->assertArrayHasKey('id', $dataFiltered[0]);
        $this->assertArrayHasKey('foo', $dataFiltered[0]);
        $this->assertArrayNotHasKey('create_at', $dataFiltered[0]);
        $this->assertArrayHasKey('roles', $dataFiltered[0]);
    }

    public function testFilterString()
    {
        $parser = new FieldsParser;
        $fields = $parser->parse('foo');

        $filter = new FieldsFilter($fields);

        $this->assertEquals('foo', $filter->filter('foo'));
    }

    public function testJson()
    {
        $date = new DateTime('now');
        $data = [
            new TestObject([
                'id'        => 123,
                'create_at' => $date
            ])
        ];

        $parser = new FieldsParser;
        $fields = $parser->parse('id,create_at');

        $filter = new FieldsFilter($fields);
        $dataFiltered = $filter->filter($data);

        $this->assertArrayHasKey('id', $dataFiltered[0]);
        $this->assertArrayHasKey('create_at', $dataFiltered[0]);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'id'        => 123,
                'create_at' => $date->format(DateTime::ISO8601)
            ]),
            json_encode($dataFiltered[0])
        );
    }
}

class TestObject extends ArrayObject implements JsonSerializable
{
    public function jsonSerialize()
    {
        $item = $this->getArrayCopy();

        if (isset($item['create_at'])) {
            $item['create_at'] = $item['create_at']->format(DateTime::ISO8601);
        }

        return $item;
    }
}
