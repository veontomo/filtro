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

		$this->assertContains('ETICA SI specialist ricerca agenti immobiliari con esperienza '
			.'da inserire in organico il nostro obiettivo &egrave; la tua crescita professionale ', $result[4]->content);
		$this->assertContains('operativa aggiornamento professionale continuo '
			.'e piani provvigionali dal 50 % all\' 80 %. contattaci!', $result[4]->content);

	
	}

	public function testRetrieveAds(){
		$pp = new Portaportese;
		$pp->setUrl('http://localhost/filtro/resources/portaportese/');
		$pp->setTimeMin('10 Oct 2013 00:00');
		$pp->setTimeMax('12 Oct 2013 00:00');
		$result = $pp->retrieveAds();
		$this->assertTrue(!empty($result));
		// six proper pages and one empty page with ads are present in the web imitation folder
		// each of them contains 11 ads
		$this->assertEquals(count($result), 66); 
		// check that all elements of the array are of Ad class
		foreach ($result as $ad) {
			$this->assertEquals('Ad', get_class($ad));
		}

		// selective check of some ads
		$ad1 = $result[5];
		$this->assertEquals($ad1->content, 'AGENTE IMMOBILIARE Sei sicuro che le tue qualita\' '
			. 'siano davvero riconosciute utilizzate e apprezzate Non perdere l occasione di '
			. 'conoscere come farlo al meglio. Per info e colloquio ozanam@ferrariemeriziola.it');

		$ad2 = $result[12];
		$this->assertEquals($ad2->content, 'AZIENDA ATTIVA AREA ROMA RICERCA 5 IMPIEGATI/E WEB '
			.'ANCHE INESPERTI PER AMPLIAMENTO ORGANICO PROPRIA SEDE SI RICHIEDE DIPLOMA '
			.'SUPERIORE MAX 30 ANNI DISPONIBILITA\' IMMEDIATA PER APPUNTAMENTO DALLE ORE 9 ALLE 14');
		}

		public function testRetrieveDates(){
			$savedAdDir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR
				.'portaportese'.DIRECTORY_SEPARATOR;
			$this->assertTrue(is_dir($savedAdDir) && 
							  file_exists($savedAdDir.'index.html'), 
				'In order to make the test '.__METHOD__.'start, '
				.'save an "entry page" (i.e. http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/) '
				.'in the folder "resources/portaportese/" under the name "index.html".'.PHP_EOL);
			
			$pp = new Portaportese;
			$pp->setUrl('http://localhost/filtro/resources/portaportese/');
			$dates = $pp->retrieveDates();
			$this->assertTrue(is_array($dates));
			$this->assertTrue(array_key_exists('m-usC78', $dates));
			$this->assertEquals($dates['m-usC78'], '22 Oct 2013 00:00');
			$this->assertEquals($dates['m-usC68'], '17 Sep 2013 00:00');
			$this->assertEquals($dates['m-usC72'], '01 Oct 2013 00:00');
		}

		public function testSlice(){
			$stub = array(	'm-usC78' => '22 Oct 2013 00:00', 'm-usC77' => '18 Oct 2013 00:00',
					    	'm-usC76' => '15 Oct 2013 00:00', 'm-usC75' => '11 Oct 2013 00:00',
						    'm-usC74' => '08 Oct 2013 00:00', 'm-usC73' => '04 Oct 2013 00:00',
						    'm-usC72' => '01 Oct 2013 00:00', 'm-usC71' => '27 Sep 2013 00:00',
						    'm-usC70' => '24 Sep 2013 00:00', 'm-usC69' => '20 Sep 2013 00:00',
						    'm-usC68' => '17 Sep 2013 00:00');

			$pp = $this->getMock('Portaportese', array('retrieveDates'));
			$pp->expects($this->any())
			    ->method('retrieveDates')
			    ->will($this->returnValue($stub));

			$expected1 = array('m-usC77' => '18 Oct 2013 00:00',
					    	'm-usC76' => '15 Oct 2013 00:00', 'm-usC75' => '11 Oct 2013 00:00',
						    'm-usC74' => '08 Oct 2013 00:00');
			$actual1 = $pp->linksInDateRange(strtotime('07 Oct 2013 13:43'), strtotime('20 Oct 2013 14:45'));
			$this->assertEquals($expected1, $actual1);

			$expected2 = array(	'm-usC74' => '08 Oct 2013 00:00', 'm-usC73' => '04 Oct 2013 00:00',
						    'm-usC72' => '01 Oct 2013 00:00', 'm-usC71' => '27 Sep 2013 00:00',
						    'm-usC70' => '24 Sep 2013 00:00', 'm-usC69' => '20 Sep 2013 00:00');
			$actual2 = $pp->linksInDateRange(strtotime('20 Sep 2013 00:00'), strtotime('08 Oct 2013 00:00'));
			$this->assertEquals($expected2, $actual2);

			$pp->setTimeMin('20 Sep 2013 00:00');
			$pp->setTimeMax('8 Oct 2013 00:00');
			$actual3 = $pp->linksInDateRange();
			$this->assertEquals($expected2, $actual3);
		}


		public function testRetrieveAdsFixedDate(){
			$pp = $this->getMock('Portaportese', array('retrieveAdsOnePage'));
			$this->assertClassHasAttribute('keywords', 'Portaportese');
			$pp->keywords = "word1, word2";
			$pp->expects($this->any())
			    ->method('retrieveAdsOnePage')
			    ->will($this->returnCallback('justAMock'));
			$result = $pp->retrieveAdsFixedDate(1);
			$this->assertEquals(3, count($result));
		}



		public function testFilterOut(){
			$pp = new Portaportese;
			$pp->keywords = "word1, word2";

			$ad1 = new Ad;
			$ad1->content = "first ad, it contains word1";
			$ad2 = new Ad;
			$ad2->content = "second ad";
			$ad3 = new Ad;
			$ad3->content = "third ad, it contains word2";
			$ad4 = new Ad;
			$ad4->content = "fourth ad";
			$ad5 = new Ad;
			$ad5->content = "fifth ad, it contains word1";

			$filtered = $pp->filterOut(array($ad1, $ad2, $ad3, $ad4, $ad5));
			$this->assertEquals(3, count($filtered));

		}
}

function justAMock(){
	echo __METHOD__;
	print_r(func_get_args());
	$arg = func_get_args();
	$ad1 = new Ad;
	$ad1->content = "first ad, it contains word1";
	$ad2 = new Ad;
	$ad2->content = "second ad";
	$ad3 = new Ad;
	$ad3->content = "third ad, it contains word2";
	$ad4 = new Ad;
	$ad4->content = "fourth ad";
	$ad5 = new Ad;
	$ad5->content = "fifth ad, it contains word1";

	$block1 = array($ad1, $ad2, $ad3, $ad4, $ad5);

	switch ($arg[0]) {
		case 'm-usC1&pag1':
			$output = $block1;
			break;
		case 'm-usC1&pag2':
			$output = array();
			break;		
		default:
			$output = array();
			break;
	}
	return $output;
}



?>