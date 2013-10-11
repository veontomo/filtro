<?php
require DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Subito.php';
class SubitoTest extends PHPUnit_Framework_TestCase
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
		$this->assertClassHasAttribute('url', 'Subito');
		$this->assertClassHasAttribute('urlPattern', 'Subito');

 		$this->assertTrue(method_exists('Subito', 'url'));
 		$this->assertTrue(method_exists('Subito', 'urlPattern'));
 		$this->assertTrue(method_exists('Subito', 'retrieveAds'));

 	}


 	public function testPageContent(){
 		$startInfo = $this->createExternalUrl('webImitation2');
 		$fileContent = $startInfo['content'];
 		$fileName = $startInfo['filename']; 
 		$subito = new Subito;
 		$subito->setUrl("http://localhost/filtro/webImitation2/");
 		$page = $subito->pageContent($fileName);
 		$this->assertEquals($fileContent, $page);
 		$this->removeExternalUrl(array('dirname' => 'webImitation2', 'filename' => $fileName));

 	}

 	public function testFormatTime(){
 		$subito = new Subito;
 	    $today = date("d M Y");
 	    $yesterday = date("d M Y", strtotime("-1 day"));
 	    $year = date("Y");
 	    $this->assertEquals($subito->formatTime("Oggi 12:48"), "$today 12:48");
 	    $this->assertEquals($subito->formatTime("Oggi12:48"), "$today 12:48");
 	    $this->assertEquals($subito->formatTime("Ieri 02:48"), "$yesterday 02:48");
 	    $this->assertEquals($subito->formatTime("Ieri02:48"), "$yesterday 02:48");
 	    $this->assertEquals($subito->formatTime("20 ago 22:14"), "20 Aug $year 22:14");
 	    $this->assertEquals($subito->formatTime("01 gen 22:14"), "01 Jan $year 22:14");
 	    $this->assertEquals($subito->formatTime("01 Feb 22:14"), "01 Feb $year 22:14");
 	    $this->assertEquals($subito->formatTime("01 mar 2:14"), "01 Mar $year 02:14");
 	}   


 	public function testRetriveAdsOnePage(){
 		$this->assertTrue(file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'resources'
 			.DIRECTORY_SEPARATOR.'subito'.DIRECTORY_SEPARATOR.'th=1&o=2'), "In order to make the test pass, save 
 			a page with advertisements from www.subito.it in the folder resources/subito/th=1&o=2");
 		$subito = new Subito;
 		$subito->setUrl('http://localhost/filtro/resources/subito/');
 		$result = $subito->retriveAdsOnePage('th=1&o=2');
 		$this->assertEquals('array', gettype($result));
 		$this->assertFalse(empty($result));
 		$this->assertEquals(50, count($result));
 		foreach ($result as $ad) {
 			$this->assertEquals('Ad', get_class($ad));
 		}

		$subito = new Subito;
 	    $today = date("d M Y");
 	    $yesterday = date("d M Y", strtotime("-1 day"));

 		$this->assertEquals('Operatore macchine a controllo numerico', $result[0]->content);
 		$this->assertEquals($today.' 08:54', $result[0]->date);
 		//$this->assertEquals('http://www.subito.it/offerte-lavoro/operatore-macchine-a-controllo-numerico-latina-75363838.htm', $result[0]->url);

 		$this->assertEquals('Analista programmatore J2EE', $result[6]->content);
 		$this->assertEquals($today.' 08:32', $result[6]->date);

 		$this->assertEquals('Dialogatori Save the children contratto garantito', $result[48]->content);
 		$this->assertEquals($yesterday.' 18:00', $result[48]->date);

 		$this->assertEquals('Agenti e consulenti alla vendita retribuzione 12.000 €', $result[3]->content);
 		$this->assertEquals($today.' 08:39', $result[3]->date);
 		 

 	}

}
?>