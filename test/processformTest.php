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
		$output = retrieveAds(json_encode($input));
		$outputDecoded = json_decode($output, true);
		$this->assertTrue(is_array($outputDecoded));
		$this->assertTrue(array_key_exists('success', $outputDecoded));
		$this->assertFalse($outputDecoded['success']);
		$this->assertTrue(array_key_exists('message', $outputDecoded));


		$input = array('keywords' => '', 'category' => ''); // key 'category' is present, but its value is empty
		$output = retrieveAds(json_encode($input));
		$outputDecoded = json_decode($output, true);
		$this->assertTrue(is_array($outputDecoded));
		$this->assertTrue(array_key_exists('success', $outputDecoded));
		$this->assertFalse($outputDecoded['success']);
		$this->assertTrue(array_key_exists('message', $outputDecoded));

		$input = array('keywords' => '', 'category' => NULL); // key 'category' is present, but its value is NULL
		$output = retrieveAds(json_encode($input));
		$outputDecoded = json_decode($output, true);
		$this->assertTrue(is_array($outputDecoded));
		$this->assertTrue(array_key_exists('success', $outputDecoded));
		$this->assertFalse($outputDecoded['success']);
		$this->assertTrue(array_key_exists('message', $outputDecoded));


	}

	public function testFirstTry(){
		$input = array('category' => 'lavoro', 'keywords' => 'Domestico');
		$output = retrieveAds(json_encode($input));
		$outputDecoded = json_decode($output, true);
		$this->assertTrue(is_array($outputDecoded));
		$this->assertTrue(array_key_exists('success', $outputDecoded));
		$this->assertTrue($outputDecoded['success']);
		$this->assertTrue(array_key_exists('result', $outputDecoded));
		$this->assertTrue(is_array($outputDecoded['result']));
		$this->assertFalse(empty($outputDecoded['result']));
	}

	public function testFunctionCalled(){
		$input = array('category' => 'lavoro', 'keywords' => 'Domestico');
		retrieveAds(json_encode($input));
		

	}



}