<?php
require DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Subito.php';
class SubitoTest extends PHPUnit_Framework_TestCase
{
	public function testPresenceOfProperties(){
		$this->assertClassHasAttribute('url', 'Subito');
		$this->assertClassHasAttribute('urlPattern', 'Subito');

 		$this->assertTrue(method_exists('Subito', 'url'));
 		$this->assertTrue(method_exists('Subito', 'urlPattern'));
 		$this->assertTrue(method_exists('Subito', 'retrievePage'));

 	}

}
?>