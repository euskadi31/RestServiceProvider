<?php
/*
 * This file is part of the RestServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider\Rest\Exception;

use Euskadi31\Silex\Provider\Rest\Exception\ErrorCollectionException;

class ErrorCollectionExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testErrorCollectionException()
    {
        $errors = [];
        $errors[] = $ex1 = $this->getMock('\Exception');
        $errors[] = $ex2 = $this->getMock('\Exception');

        $collection = new ErrorCollectionException($errors);

        $this->assertEquals(400, $collection->getStatusCode());

        $this->assertEquals(2, count($collection));

        $collection[] = $ex3 = $this->getMock('\Exception');

        $this->assertEquals(3, count($collection));

        $this->assertEquals($ex3, $collection[2]);

        $this->assertTrue(isset($collection[2]));

        $this->assertEquals($ex1, $collection->current());
        $this->assertEquals(0, $collection->key());
        $this->assertTrue($collection->valid());

        $collection->next();

        $this->assertEquals($ex2, $collection->current());
        $this->assertEquals(1, $collection->key());
        $this->assertTrue($collection->valid());

        $collection->rewind();

        $this->assertEquals($ex1, $collection->current());
        $this->assertEquals(0, $collection->key());
        $this->assertTrue($collection->valid());

        unset($collection[0]);

        $this->assertFalse(isset($collection[0]));
    }
}
