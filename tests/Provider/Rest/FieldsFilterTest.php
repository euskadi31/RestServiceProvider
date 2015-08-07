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

        $this->assertEquals([
            [
                'id' => 1,
                'user' => [
                    'id' => 12,
                    'firstname' => 'Axel',
                    'email' => 'axel@domain.com'
                ],
                'translates' => [
                    [
                        'id'            => 1234,
                        'locale'        => 'fr',
                        'title'         => 'Test title',
                        'program'       => 'Bla Bla',
                        'material'      => 'Bla'
                    ]
                ],
                'create_at' => $data[0]['create_at']
            ],
            [
                'id' => 2,
                'user' => [
                    'id' => 122,
                    'firstname' => 'Rui',
                    'email' => 'rui@domain.com',
                ],
                'translates' => [
                    [
                        'id'            => 12342,
                        'locale'        => 'fr',
                        'title'         => 'Test title 2',
                        'program'       => 'Bla Bla',
                        'material'      => 'Bla'
                    ]
                ],
                'create_at' => $data[1]['create_at']
            ]
        ], $dataFiltered);
    }
}
