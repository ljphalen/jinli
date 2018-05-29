<?php
class A {
	public static function who() {
		echo __CLASS__;
	}
	public static function test() {
		self::who();
	}
	public static function test2() {
		static::who();
	}
}
class B extends A {
	public static function who() {
		echo __CLASS__;
	}
}
B::test();
B::test2();
