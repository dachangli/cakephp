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
 * @since         CakePHP(tm) v3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\View\Input;

use Cake\TestSuite\TestCase;
use Cake\View\Input\DateTime;
use Cake\View\Input\SelectBox;
use Cake\View\StringTemplate;

/**
 * DateTime input test case
 */
class DateTimeTest extends TestCase {

/**
 * @setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$templates = [
			'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
			'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
			'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
			'dateWidget' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}'
		];
		$this->templates = new StringTemplate($templates);
		$this->selectBox = new SelectBox($this->templates);
		$this->DateTime = new DateTime($this->templates, $this->selectBox);
	}

/**
 * Data provider for testing various types of invalid selected values.
 *
 * @return array
 */
	public static function invalidSelectedValuesProvider() {
		$date = new \DateTime('2014-01-20 12:30:45');
		return [
			'string' => ['Bag of poop'],
			'int' => [-1],
			'array' => [[
				'derp' => 'hurt'
			]]
		];
	}

/**
 * test rendering selected values.
 *
 * @dataProvider selectedValuesProvider
 * @return void
 */
	public function testRenderSelectedInvalid($selected) {
		$result = $this->DateTime->render(['val' => $selected]);
		$now = new \DateTime();
		$format = '<option value="%s" selected="selected">%s</option>';
		$this->assertContains(
			sprintf($format, $now->format('Y'), $now->format('Y')), 
			$result
		);
	}

/**
 * Data provider for testing various acceptable selected values.
 *
 * @return array
 */
	public static function selectedValuesProvider() {
		$date = new \DateTime('2014-01-20 12:30:45');
		return [
			'DateTime' => [$date],
			'string' => [$date->format('Y-m-d H:i:s')],
			'int' => [$date->getTimestamp()],
			'array' => [[
				'year' => '2014', 'month' => '01', 'day' => '20',
				'hour' => '12', 'minute' => '30', 'second' => '45',
			]]
		];
	}

/**
 * test rendering selected values.
 *
 * @dataProvider selectedValuesProvider
 * @return void
 */
	public function testRenderSelected($selected) {
		$result = $this->DateTime->render(['val' => $selected]);
		$this->assertContains('<option value="2014" selected="selected">2014</option>', $result);
		$this->assertContains('<option value="01" selected="selected">1</option>', $result);
		$this->assertContains('<option value="20" selected="selected">20</option>', $result);
		$this->assertContains('<option value="12" selected="selected">12</option>', $result);
		$this->assertContains('<option value="30" selected="selected">30</option>', $result);
		$this->assertContains('<option value="45" selected="selected">45</option>', $result);
	}

/**
 * Test rendering widgets with empty values.
 *
 * @retun void
 */
	public function testRenderEmptyValues() {
		$result = $this->DateTime->render([
			'year' => ['empty' => 'YEAR'],
			'month' => ['empty' => 'MONTH'],
			'day' => ['empty' => 'DAY'],
			'hour' => ['empty' => 'HOUR'],
			'minute' => ['empty' => 'MINUTE'],
			'second' => ['empty' => 'SECOND'],
			'meridian' => ['empty' => 'MERIDIAN'],
		]);
		$this->assertContains('<option value="" selected="selected">YEAR</option>', $result);
		$this->assertContains('<option value="" selected="selected">MONTH</option>', $result);
		$this->assertContains('<option value="" selected="selected">DAY</option>', $result);
		$this->assertContains('<option value="" selected="selected">HOUR</option>', $result);
		$this->assertContains('<option value="" selected="selected">MINUTE</option>', $result);
		$this->assertContains('<option value="" selected="selected">SECOND</option>', $result);
		$this->assertContains('<option value="" selected="selected">MERIDIAN</option>', $result);
	}

/**
 * Test rendering the default year widget.
 *
 * @return void
 */
	public function testRenderYearWidgetDefaultRange() {
		$now = new \DateTime();
		$result = $this->DateTime->render([
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$year = $now->format('Y');
		$format = '<option value="%s" selected="selected">%s</option>';
		$this->assertContains(sprintf($format, $year, $year), $result);

		$format = '<option value="%s">%s</option>';
		$maxYear = $now->format('Y') + 5;
		$minYear = $now->format('Y') - 5;
		$this->assertContains(sprintf($format, $maxYear, $maxYear), $result);
		$this->assertContains(sprintf($format, $minYear, $minYear), $result);

		$nope = $now->format('Y') + 6;
		$this->assertNotContains(sprintf($format, $nope, $nope), $result);

		$nope = $now->format('Y') - 6;
		$this->assertNotContains(sprintf($format, $nope, $nope), $result);
	}

/**
 * Test ordering of year options.
 *
 * @return void
 */
	public function testRenderYearWidgetOrdering() {
		$now = new \DateTime('2014-01-01 12:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => [
				'start' => 2013,
				'end' => 2015,
				'data-foo' => 'test',
				'order' => 'desc',
			],
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
			'orderYear' => 'asc',
		]);
		$expected = [
			'select' => ['name' => 'date[year]', 'data-foo' => 'test'],
			['option' => ['value' => '2013']], '2013', '/option',
			['option' => ['value' => '2014', 'selected' => 'selected']], '2014', '/option',
			['option' => ['value' => '2015']], '2015', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);

		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => [
				'start' => 2013,
				'end' => 2015,
				'order' => 'asc'
			],
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[year]'],
			['option' => ['value' => '2015']], '2015', '/option',
			['option' => ['value' => '2014', 'selected' => 'selected']], '2014', '/option',
			['option' => ['value' => '2013']], '2013', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test that a selected value outside of the chosen
 * year boundary is also included as an option.
 *
 * @return void
 */
	public function testRenderYearWidgetValueOutOfBounds() {
		$now = new \DateTime('2010-01-01 12:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => [
				'start' => 2013,
				'end' => 2015,
			],
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[year]'],
			['option' => ['value' => '2010', 'selected' => 'selected']], '2010', '/option',
			['option' => ['value' => '2011']], '2011', '/option',
			['option' => ['value' => '2012']], '2012', '/option',
			['option' => ['value' => '2013']], '2013', '/option',
			['option' => ['value' => '2014']], '2014', '/option',
			['option' => ['value' => '2015']], '2015', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);

		$now = new \DateTime('2013-01-01 12:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => [
				'start' => 2010,
				'end' => 2011,
			],
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[year]'],
			['option' => ['value' => '2010']], '2010', '/option',
			['option' => ['value' => '2011']], '2011', '/option',
			['option' => ['value' => '2012']], '2012', '/option',
			['option' => ['value' => '2013', 'selected' => 'selected']], '2013', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test rendering the month widget
 *
 * @return void
 */
	public function testRenderMonthWidget() {
		$now = new \DateTime('2010-09-01 12:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[month]'],
			['option' => ['value' => '01']], '1', '/option',
			['option' => ['value' => '02']], '2', '/option',
			['option' => ['value' => '03']], '3', '/option',
			['option' => ['value' => '04']], '4', '/option',
			['option' => ['value' => '05']], '5', '/option',
			['option' => ['value' => '06']], '6', '/option',
			['option' => ['value' => '07']], '7', '/option',
			['option' => ['value' => '08']], '8', '/option',
			['option' => ['value' => '09', 'selected' => 'selected']], '9', '/option',
			['option' => ['value' => '10']], '10', '/option',
			['option' => ['value' => '11']], '11', '/option',
			['option' => ['value' => '12']], '12', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test rendering month widget with names.
 *
 * @return void
 */
	public function testRenderMonthWidgetWithNames() {
		$now = new \DateTime('2010-09-01 12:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'month' => ['data-foo' => 'test', 'names' => true],
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[month]', 'data-foo' => 'test'],
			['option' => ['value' => '01']], 'January', '/option',
			['option' => ['value' => '02']], 'February', '/option',
			['option' => ['value' => '03']], 'March', '/option',
			['option' => ['value' => '04']], 'April', '/option',
			['option' => ['value' => '05']], 'May', '/option',
			['option' => ['value' => '06']], 'June', '/option',
			['option' => ['value' => '07']], 'July', '/option',
			['option' => ['value' => '08']], 'August', '/option',
			['option' => ['value' => '09', 'selected' => 'selected']], 'September', '/option',
			['option' => ['value' => '10']], 'October', '/option',
			['option' => ['value' => '11']], 'November', '/option',
			['option' => ['value' => '12']], 'December', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test rendering the day widget.
 *
 * @return void
 */
	public function testRenderDayWidget() {
		$now = new \DateTime('2010-09-09 12:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => [
				'data-foo' => 'test',
			],
			'hour' => false,
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[day]', 'data-foo' => 'test'],
			['option' => ['value' => '01']], '1', '/option',
			['option' => ['value' => '02']], '2', '/option',
			['option' => ['value' => '03']], '3', '/option',
			['option' => ['value' => '04']], '4', '/option',
			['option' => ['value' => '05']], '5', '/option',
			['option' => ['value' => '06']], '6', '/option',
			['option' => ['value' => '07']], '7', '/option',
			['option' => ['value' => '08']], '8', '/option',
			['option' => ['value' => '09', 'selected' => 'selected']], '9', '/option',
			['option' => ['value' => '10']], '10', '/option',
			['option' => ['value' => '11']], '11', '/option',
			['option' => ['value' => '12']], '12', '/option',
			['option' => ['value' => '13']], '13', '/option',
			['option' => ['value' => '14']], '14', '/option',
			['option' => ['value' => '15']], '15', '/option',
			['option' => ['value' => '16']], '16', '/option',
			['option' => ['value' => '17']], '17', '/option',
			['option' => ['value' => '18']], '18', '/option',
			['option' => ['value' => '19']], '19', '/option',
			['option' => ['value' => '20']], '20', '/option',
			['option' => ['value' => '21']], '21', '/option',
			['option' => ['value' => '22']], '22', '/option',
			['option' => ['value' => '23']], '23', '/option',
			['option' => ['value' => '24']], '24', '/option',
			['option' => ['value' => '25']], '25', '/option',
			['option' => ['value' => '26']], '26', '/option',
			['option' => ['value' => '27']], '27', '/option',
			['option' => ['value' => '28']], '28', '/option',
			['option' => ['value' => '29']], '29', '/option',
			['option' => ['value' => '30']], '30', '/option',
			['option' => ['value' => '31']], '31', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test rendering the hour picker in 24 hour mode.
 *
 * @return void
 */
	public function testRenderHourWidget24StartAndEnd() {
		$now = new \DateTime('2010-09-09 13:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => [
				'start' => 8,
				'end' => 16
			],
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$this->assertContains('<select name="date[hour]">', $result);
		$this->assertNotContains(
			'<option value="01">1</option>',
			$result,
			'no 1 am'
		);
		$this->assertNotContains(
			'<option value="07">7</option>',
			$result,
			'contain 7'
		);
		$this->assertContains(
			'<option value="13" selected="selected">13</option>',
			$result,
			'selected value present'
		);
		$this->assertNotContains(
			'<option value="17">17</option>',
			$result,
			'contains 17 hours'
		);
	}

/**
 * Test rendering the hour picker in 24 hour mode.
 *
 * @return void
 */
	public function testRenderHourWidget24() {
		$now = new \DateTime('2010-09-09 13:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => [
				'data-foo' => 'test'
			],
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$this->assertContains('<select name="date[hour]" data-foo="test">', $result);
		$this->assertContains(
			'<option value="01">1</option>',
			$result,
			'contain 1 am'
		);
		$this->assertContains(
			'<option value="05">5</option>',
			$result,
			'contain 5 am'
		);
		$this->assertContains(
			'<option value="13" selected="selected">13</option>',
			$result,
			'selected value present'
		);
		$this->assertContains(
			'<option value="24">24</option>',
			$result,
			'contains 24 hours'
		);
		$this->assertNotContains('date[day]', $result, 'No day select.');
		$this->assertNotContains('value="0"', $result, 'No zero hour');
		$this->assertNotContains('value="25"', $result, 'No 25th hour');
		$this->assertNotContains('<select name="date[meridian]">', $result);
		$this->assertNotContains('<option value="pm" selected="selected">pm</option>', $result);
	}

/**
 * Test rendering the hour widget in 12 hour mode.
 *
 * @return void
 */
	public function testRenderHourWidget12() {
		$now = new \DateTime('2010-09-09 13:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => [
				'format' => 12,
				'data-foo' => 'test'
			],
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$this->assertContains('<select name="date[hour]" data-foo="test">', $result);
		$this->assertContains(
			'<option value="01" selected="selected">1</option>',
			$result,
			'contain 1pm selected'
		);
		$this->assertContains(
			'<option value="05">5</option>',
			$result,
			'contain 5'
		);
		$this->assertContains(
			'<option value="12">12</option>',
			$result,
			'contain 12'
		);
		$this->assertNotContains(
			'<option value="13">13</option>',
			$result,
			'selected value present'
		);
		$this->assertNotContains('date[day]', $result, 'No day select.');
		$this->assertNotContains('value="0"', $result, 'No zero hour');

		$this->assertContains('<select name="date[meridian]">', $result);
		$this->assertContains('<option value="pm" selected="selected">pm</option>', $result);
	}

/**
 * Test rendering the hour picker in 12 hour mode.
 *
 * @return void
 */
	public function testRenderHourWidget12StartAndEnd() {
		$now = new \DateTime('2010-09-09 13:00:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => [
				'start' => 8,
				'end' => 12
			],
			'minute' => false,
			'second' => false,
			'val' => $now,
		]);
		$this->assertContains('<select name="date[hour]">', $result);
		$this->assertContains(
			'<option value="08">8</option>',
			$result,
			'contains 8am'
		);
		$this->assertContains(
			'<option value="12">12</option>',
			$result,
			'contains 8am'
		);
		$this->assertNotContains(
			'<option value="01">1</option>',
			$result,
			'no 1 am'
		);
		$this->assertNotContains(
			'<option value="07">7</option>',
			$result,
			'contain 7'
		);
		$this->assertNotContains(
			'<option value="13" selected="selected">13</option>',
			$result,
			'selected value present'
		);
	}

/**
 * Test rendering the minute widget with no options.
 *
 * @return void
 */
	public function testRenderMinuteWidget() {
		$now = new \DateTime('2010-09-09 13:25:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => [
				'data-foo' => 'test',
			],
			'second' => false,
			'val' => $now,
		]);
		$this->assertContains('<select name="date[minute]" data-foo="test">', $result);
		$this->assertContains(
			'<option value="00">00</option>',
			$result,
			'contains 1'
		);
		$this->assertContains(
			'<option value="05">05</option>',
			$result,
			'contains 05'
		);
		$this->assertContains(
			'<option value="25" selected="selected">25</option>',
			$result,
			'selected value present'
		);
		$this->assertContains(
			'<option value="59">59</option>',
			$result,
			'contains 59'
		);
		$this->assertNotContains('value="60"', $result, 'No 60 value');
	}

/**
 * Test minutes with interval values.
 *
 * @return void
 */
	public function testRenderMinuteWidgetInterval() {
		$now = new \DateTime('2010-09-09 13:23:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => [
				'interval' => 5
			],
			'second' => false,
			'val' => $now,
		]);
		$this->assertContains('<select name="date[minute]">', $result);
		$this->assertContains(
			'<option value="00">00</option>',
			$result,
			'contains 0'
		);
		$this->assertContains(
			'<option value="05">05</option>',
			$result,
			'contains 05'
		);
		$this->assertContains(
			'<option value="25" selected="selected">25</option>',
			$result,
			'selected value present'
		);
		$this->assertContains(
			'<option value="55">55</option>',
			$result,
			'contains 59'
		);
		$this->assertNotContains('value="2"', $result, 'No 2 value');
		$this->assertNotContains('value="23"', $result, 'No 23 value');
		$this->assertNotContains('value="58"', $result, 'No 58 value');
		$this->assertNotContains('value="59"', $result, 'No 59 value');
		$this->assertNotContains('value="60"', $result, 'No 60 value');
	}

/**
 * Test rounding up and down.
 *
 * @return void
 */
	public function testRenderMinuteWidgetIntervalRounding() {
		$now = new \DateTime('2010-09-09 13:22:00');
		$data = [
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => [
				'interval' => 5,
				'round' => 'up',
			],
			'second' => false,
			'val' => $now,
		];
		$result = $this->DateTime->render($data);
		$this->assertContains(
			'<option value="25" selected="selected">25</option>',
			$result,
			'selected value present'
		);
		$this->assertNotContains('value="23"', $result, 'No 23 value');

		$data['minute']['round'] = 'down';
		$result = $this->DateTime->render($data);
		$this->assertContains(
			'<option value="20" selected="selected">20</option>',
			$result,
			'selected value present'
		);
		$this->assertNotContains('value="23"', $result, 'No 23 value');
	}

/**
 * Test that minute interval rounding can effect hours and days.
 *
 * @return void
 */
	public function testMinuteIntervalHourRollover() {
		$now = new \DateTime('2010-09-09 23:58:00');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'minute' => [
				'interval' => 5,
				'round' => 'up',
			],
			'second' => false,
			'val' => $now,
		]);

		$this->assertContains(
			'<option value="00" selected="selected">00</option>',
			$result,
			'selected minute present'
		);
		$this->assertContains(
			'<option value="10" selected="selected">10</option>',
			$result,
			'selected day present'
		);
	}

/**
 * Test render seconds basic.
 *
 * @return void
 */
	public function testRenderSecondsWidget() {
		$now = new \DateTime('2010-09-09 13:00:25');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => [
				'data-foo' => 'test',
			],
			'val' => $now,
		]);
		$this->assertContains('<select name="date[second]" data-foo="test">', $result);
		$this->assertContains(
			'<option value="01">01</option>',
			$result,
			'contains 1'
		);
		$this->assertContains(
			'<option value="05">05</option>',
			$result,
			'contains 05'
		);
		$this->assertContains(
			'<option value="25" selected="selected">25</option>',
			$result,
			'selected value present'
		);
		$this->assertContains(
			'<option value="60">60</option>',
			$result,
			'contains 60'
		);
		$this->assertNotContains('value="0"', $result, 'No zero value');
		$this->assertNotContains('value="61"', $result, 'No 61 value');
	}

/**
 * Test the merdian select.
 *
 * @return void
 */
	public function testRenderMeridianWidget() {
		$now = new \DateTime('2010-09-09 13:00:25');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'meridian' => [],
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[meridian]'],
			['option' => ['value' => 'am']], 'am', '/option',
			['option' => ['value' => 'pm', 'selected' => 'selected']], 'pm', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);

		$now = new \DateTime('2010-09-09 09:00:25');
		$result = $this->DateTime->render([
			'name' => 'date',
			'year' => false,
			'month' => false,
			'day' => false,
			'hour' => false,
			'minute' => false,
			'second' => false,
			'meridian' => [],
			'val' => $now,
		]);
		$expected = [
			'select' => ['name' => 'date[meridian]'],
			['option' => ['value' => 'am', 'selected' => 'selected']], 'am', '/option',
			['option' => ['value' => 'pm']], 'pm', '/option',
			'/select',
		];
		$this->assertTags($result, $expected);
	}

}