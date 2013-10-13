<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Portaportese.php';
class PortaporteseTest extends PHPUnit_Framework_TestCase
{

	public function testPresenceOfProperties(){
		$this->assertClassHasAttribute('url', 'Portaportese');
		$this->assertClassHasAttribute('urlPattern', 'Portaportese');

 		$this->assertTrue(method_exists('Portaportese', 'url'));
 		$this->assertTrue(method_exists('Portaportese', 'urlPattern'));
 		$this->assertTrue(method_exists('Portaportese', 'retrieveAds'));

 	}

 	public function testUrlGetter(){
 		$pp = new Portaportese;
 		$pp->setUrl('http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/');
 		$this->assertEquals('http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/', $pp->url());
 	}


 	public function testUrlPatternGetter(){
 		$pp = new Portaportese;
 		$this->assertEquals('m-usCPLACEHOLDER1&pagPLACEHOLDER2', $pp->urlPattern());
 	}

 	public function testRepoDirSetterGetter(){
 		$pp = new Portaportese;
 		$this->assertTrue(method_exists('Portaportese', 'setRepoDir'));
 		$pp->setRepoDir('f:/a/b/c/d/');
 		$this->assertEquals('f:/a/b/c/d/', $pp->RepoDir());
 	}


 	public function testTimeMax(){
 		$pp = new Portaportese;
  		$time = time();
 		$this->assertEquals($time, $pp->timeMax());

 		$date1 = 1223;
 		$this->assertTrue($pp->setTimeMax($date1));
 		$this->assertEquals($date1, $pp->timeMax());

 		$date2 = "abcd";
 		$this->assertFalse($pp->setTimeMax($date2));
 		$this->assertEquals($date1, $pp->timeMax(), "since \"$date2\" can not be transformed into time, the previously imposed value should remain.");

 		$date3 = "12 Oct 2013 10:17";
 		$this->assertTrue($pp->setTimeMax($date3));
 		$this->assertEquals(strtotime($date3), $pp->timeMax());

 		$date4 = "-1 day";
 		$this->assertTrue($pp->setTimeMax($date4));
 		$this->assertEquals(strtotime($date4), $pp->timeMax());
 	}

	public function testTimeMin(){
		$pp = new Portaportese;
 		$time = strtotime('-7 day');
		$this->assertEquals($time, $pp->timeMin());

		$date1 = 1223;
		$this->assertTrue($pp->setTimeMin($date1));
		$this->assertEquals($date1, $pp->timeMin());

		$date2 = "abcd";
		$this->assertFalse($pp->setTimeMin($date2));
		$this->assertEquals($date1, $pp->timeMin(), "since \"$date2\" can not be transformed into time, the previously imposed value should remain.");

		$date3 = "12 Oct 2013 10:17";
		$this->assertTrue($pp->setTimeMin($date3));
		$this->assertEquals(strtotime($date3), $pp->timeMin());

		$date4 = "-1 day";
		$this->assertTrue($pp->setTimeMin($date4));
		$this->assertEquals(strtotime($date4), $pp->timeMin());
	}  


	public function testLocalPath(){
		$pp = new Portaportese;
		$pp->setUrl("http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/");
		$this->assertEquals('portaportese/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag4', $pp->localPath('m-usC72&pag4'));
		$this->assertEquals('portaportese/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag1', $pp->localPath('m-usC72'));

		$pp = new Portaportese;
		$pp->setUrl("http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/");
		$this->assertEquals('portaportese/rubriche/Lavoro/Lavoro_qualificato/m-usC71&pag1', $pp->localPath('m-usC71'));
		$this->assertEquals('portaportese/rubriche/Lavoro/Lavoro_qualificato/m-usC71&pag1', $pp->localPath('m-usC71&pag1'));


	}

}
?>