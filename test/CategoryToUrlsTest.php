<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'processform.php';
class CategoryToUrlsTest extends PHPUnit_Framework_TestCase
{
	public function testExist(){
		$this->assertTrue(function_exists('categoryToUrls'), 'function "categoryToUrls" is not found in processform.php');
	}

	public function testInput(){
		$urls = categoryToUrls('lavoro');
		$this->assertTrue(is_array($urls));
		$this->assertFalse(empty($urls));

		$urls = categoryToUrls('unknown category');
		$this->assertTrue(is_array($urls));
		$this->assertTrue(empty($urls));

	}
}