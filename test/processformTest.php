<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'processform.php';
class ProcessFormTest extends PHPUnit_Framework_TestCase
{
	public function testExists(){
		$this->assertTrue(function_exists('retrieveAds'));
	}

	
}