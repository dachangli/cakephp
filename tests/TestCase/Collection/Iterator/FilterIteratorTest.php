<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Cake\Test\TestCase\Collection\Iterator;

use Cake\Collection\Iterator\FilterIterator;
use Cake\TestSuite\TestCase;

/**
 * FilterIterator test
 *
 */
class FilterIteratorTest extends TestCase {

/**
 * Tests that the iterator works correctly
 *
 * @return void
 */
	public function testFilter() {
		$this->assertFalse(defined('HHVM_VERSION'), 'Broken on HHVM');
		$items = new \ArrayIterator([1, 2, 3]);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 0, $items)
			->will($this->returnValue(false));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 1, $items)
			->will($this->returnValue(true));
		$callable->expects($this->at(2))
			->method('__invoke')
			->with(3, 2, $items)
			->will($this->returnValue(false));

		$filter = new FilterIterator($items, $callable);
		$this->assertEquals([1 => 2], iterator_to_array($filter));
	}

}