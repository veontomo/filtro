<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'processform.php';
class ProcessFormTest extends PHPUnit_Framework_TestCase
{
	public function testExists(){
		$this->assertTrue(function_exists('retrieveAds'));
	}

	/**
	* @group current
	*/
	public function testIncompleteInput(){
		$input = array('keywords' => ''); // key 'category' is not present
		$output = retrieveAds($input);
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));


		$input = array('keywords' => '', 'category' => ''); // key 'category' is present, but its value is empty
		$output = retrieveAds($input);
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));

		$input = array('keywords' => '', 'category' => NULL); // key 'category' is present, but its value is NULL
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));


	}

	/**
	* @group current
	*/
	public function testWrongDates(){
		$input = array('category' => 'lavoro', 'keywords' => 'Domestico', 'timeMin' => 1223, 'timeMax' => 21);
		$output = retrieveAds($input);
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));
	}



	// public function testFirstTry(){
	// 	$input = array('category' => 'lavoro', 'keywords' => 'Domestico');
	// 	$output = retrieveAds($input);
	// 	$this->assertTrue(is_array($output));
	// 	$this->assertTrue(array_key_exists('success', $output));
	// 	$this->assertTrue($output['success']);
	// 	$this->assertTrue(array_key_exists('result', $output));
	// 	$this->assertTrue(is_array($output['result']));
	// 	$this->assertFalse(empty($output['result']));
	// }

	// public function testFunctionCalled(){
	// 	$input = array('category' => 'lavoro', 'keywords' => 'Domestico');
	// 	retrieveAds($input);
		

	// }



}