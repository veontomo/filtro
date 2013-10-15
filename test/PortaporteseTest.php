<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Portaportese.php';
class PortaporteseTest extends PHPUnit_Framework_TestCase
{
	private function createExternalUrl($dirName){
		$dirPath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$dirName;
		if(!is_dir($dirPath) ) {
			try{
				mkdir($dirPath);
				echo PHP_EOL.'Directory '.$dirPath. ' is created.';
			}catch(Exception $e){
				throw new Exception("Problem with creating drectory in ".__METHOD__.
					". Got message: ".$e->getMessage(), 1);
			}
		}else{
			throw new Exception("Error in: ".__METHOD__.
					". $dirPath exists!  Prefer not to touch exiting directory!", 1);
		}
		$fileName = 'test'.date("Y-d-M-H-i-s", time());
		$filePath = $dirPath.DIRECTORY_SEPARATOR.$fileName;
		$content = __METHOD__. date(" Y d M H:i:s ", time());
		if(file_put_contents($filePath, $content)){
			echo PHP_EOL.'File '.$filePath. ' is created.';
			return array('content' => $content, 'filename' => $fileName);
		};
	}

	private function removeExternalUrl($arr){
		if(!array_key_exists('dirname', $arr) || !array_key_exists('filename', $arr)){
			throw new Exception("Attention! Incorrect attempt to make use of code that deletes file. 
				Nothing is deleted.", 1);
		}
		$dirName = $arr['dirname'];
		$fileName = $arr['filename'];

		$dirPath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$dirName;
		$filePath = $dirPath.DIRECTORY_SEPARATOR.$fileName;
		if(file_exists($filePath)){
			try{
				unlink($filePath);
				echo PHP_EOL.'File '.$filePath. ' is removed.';
			}catch(Exception $e){
				throw new Exception('File '.$filePath. ' is NOT removed: '.$e->getMessage(), 1);
			}
		}else{
			echo PHP_EOL.'Attention: there is no file '.$filePath.'! Are you sure you are using it correctly?'.PHP_EOL;
		}
		if(is_dir($dirPath)){
			try{
				rmdir($dirPath);
				echo PHP_EOL.'Directory '.$dirPath. ' is removed.'.PHP_EOL;
			}catch(Exception $e){
				throw new Exception('Directory '.$dirPath. ' is NOT removed: '.$e->getMessage(), 1);
			}
		}else{
			echo PHP_EOL.'Attention: no such directory! Are you sure you are using it correctly?'.PHP_EOL;
		}
	}

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




	public function testPageContent(){
		$startInfo = $this->createExternalUrl('webImitationPortaportese');
		$fileContent = $startInfo['content'];
		$fileName = $startInfo['filename']; 
		$pp = new Portaportese;
		$pp->setUrl("http://localhost/filtro/webImitationPortaportese/");
		$page = $pp->pageContent($fileName);
		$this->assertEquals($fileContent, $page);
		$this->removeExternalUrl(array('dirname' => 'webImitationPortaportese', 'filename' => $fileName));

	}

	public function testRetriveAdsOnePage(){
		$savedAdDir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR
			.'portaportese'.DIRECTORY_SEPARATOR;
		$this->assertTrue(is_dir($savedAdDir) && 
						  file_exists($savedAdDir.'m-usC75&pag2'), 
			'In order to make the test start: '.PHP_EOL
			.'save a page with advertisements from '
			.'www.portaportese.it in the folder resources/portaportese/ with a name "m-usC75&pag2"'.PHP_EOL);
		$pp = new Portaportese;
		$pp->setUrl('http://localhost/filtro/resources/portaportese/');
		$result = $pp->retrieveAdsOnePage('m-usC75&pag2');
		$this->assertEquals('array', gettype($result));
		$this->assertFalse(empty($result));
		$this->assertEquals(11, count($result));
		foreach ($result as $ad) {
			$this->assertEquals('Ad', get_class($ad));
		}
		$this->assertFalse(empty($result[0]->content));
		$this->assertEquals('RICERCHIAMO agenti immobiliari '
			. 'ambosessi automuniti in possesso di diploma si offre '
			. 'rimborso spese + provvigioni per candidarsi inviare curriculum vitae '
			. 'a rm.cinecittaest@immobiliarewl.it', $result[0]->content);
		$this->assertContains('/rubriche/Lavoro/Lavoro_qualificato/m-ricerchiamo-agenti'
			. '-immobiliari-0ID2013063085335?tipo=offerte&numero=75&latstart=41.8966'
			. '&lngstart=12.494&zoomstart=10', $result[0]->url);
		// $this->assertContains("abcd", "abcd");

	
	}

	public function testRetrieveAds(){
		$pp = new Portaportese;
		$pp->setUrl('http://localhost/filtro/resources/portaportese/');
		$result = $pp->retrieveAds();
		$this->assertTrue(!empty($result));
	}
}
?>