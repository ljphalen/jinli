<?php
(!class_exists('PHPUnit_Framework_TestCase')) && include 'PHPUnit/Framework/TestCase.php';

error_reporting(E_ALL && ~E_NOTICE);
define("BASE_PATH", dirname(__FILE__) . "/../../");
define ("APP_PATH", BASE_PATH . "application/");
define ('ENV', 'develop');
define("DEFAULT_MODULE", 'Api');
$app = new Yaf_Application(BASE_PATH. "configs/application.ini", ENV);


abstract class Base_TestCase extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		parent::setUp();
	}
	
	protected function tearDown() {
		parent::tearDown();
	}
	
	protected function assertArrayEquals($array1, $array2) {
		(!is_array($array1)) && $this->fail("Error type for arg1");
		$this->assertTrue(is_array($array2) && (count($array1) == count($array2)));
		foreach ($array1 as $key => $value) {
			$this->assertTrue(isset($array2[$key]));
			if (is_array($value)) {
				$this->assertArrayEquals($value, $array2[$key]);
			} elseif (is_object($value)) {
				$this->assertEquals(get_class($value), get_class($array2[$key]));
			} else {
				$this->assertEquals($value, $array2[$key]);
			}
		}
	}
	
	/**
	 * 断言  获取列表、获取单条数据 不是空数组或false
	 * @param array $function
	 * @param array $param
	 * @return
	 */
	protected function _getArrayTest($function, $param) {
		$row = call_user_func($function,$param);
		return $this->assertGreaterThan(0, count($row));
	}
	
	/**
	 * 断言 获取的数值，大于等于0
	 * @param array $function
	 * @param array $param
	 * @return
	 */
	protected function _getValueTest($function, $param) {
		$value = call_user_func($function,$param);
		return $this->assertGreaterThanOrEqual(0, $value);
	}
	
	/**
	 * 断言 添加数据 返回的是true
	 * @param array $function
	 * @param array $param
	 * @return
	 */
	protected function _addTest($function, $param) {
		return $this->_trueTest($function, $param);
	}
	
	/**
	 * 断言修改数据，返回的是影响的条数 或 true
	 * @param array $function
	 * @param array $param
	 * @param bool $rowCount
	 * @return
	 */
	protected function _updateTest($function, $param, $rowCount = false) {
		if (!$rowCount) return $this->_trueTest($function, $param);
		return $this->_getValueTest($function, $param);
	}
	
	/**
	 * 断言返回的是true
	 * @param array $function
	 * @param array $param
	 * @return
	 */
	protected function _trueTest($function, $param) {
		$bool = call_user_func($function,$param);
		return $this->assertTrue($bool);
	}
	
	/**
	 * 断言返回的是false
	 * @param array $function
	 * @param array $param
	 * @return
	 */
	protected function _falseTest($function, $param) {
		$bool = call_user_func($function,$param);
		return $this->assertFalse($bool);
	}
}
