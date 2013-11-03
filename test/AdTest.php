<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Ad.php';
class AdTest extends PHPUnit_Framework_TestCase
{
    public function testPresenceOfProperties(){
        $this->assertClassHasAttribute('date', 'Ad');
        $this->assertClassHasAttribute('url', 'Ad');
        $this->assertClassHasAttribute('author', 'Ad');
        $this->assertClassHasAttribute('content', 'Ad');
     

        // $origin = $this->getMock('Origin', array('starttime'));

        // $origin->expects($this->any())
        //          ->at('starttime')
        //          ->will($this->returnValue('time()'));
        //$this->assertEquals(60*60, $origin->starttime - $origin->endtime);
    }

    public function testContains(){
        $ad = new Ad;
       	$this->assertTrue(method_exists('Ad', 'containsString'), 'Class does not have method "containsString()"');

        $ad->content = "cercasi php5.1 new java Docente";
        $this->assertTrue($ad->containsString(""));
        $this->assertTrue($ad->containsString("pHp"));
        $this->assertTrue($ad->containsString("Cercasi"));
        $this->assertTrue($ad->containsString("Cerca"));
        $this->assertTrue($ad->containsString("cente"));
        $this->assertFalse($ad->containsString("Ruby"));
        $this->assertFalse($ad->containsString("Cercasii"));
        $this->assertFalse($ad->containsString("ente."));

        $ad->content = "";
        $this->assertTrue($ad->containsString(""));
        $this->assertFalse($ad->containsString("pHp"));
        $this->assertFalse($ad->containsString("Cercasi"));

        $ad->content = "cercasi php5.1 new java Docente";
        $this->assertTrue($ad->containsAnyOf(""));
        $this->assertTrue($ad->containsAnyOf("pHp , rUby"));
        $this->assertTrue($ad->containsAnyOf("Cercasi, 5.1"));
        $this->assertTrue($ad->containsAnyOf("kimono, Cerca"));
        $this->assertTrue($ad->containsAnyOf("cente"));
        $this->assertFalse($ad->containsAnyOf("Ruby"));
        $this->assertFalse($ad->containsAnyOf("Cercasii, kimono"));
        $this->assertFalse($ad->containsAnyOf("ente., old"));

	    $ad->content = ""; 
        $this->assertTrue($ad->containsAnyOf(""));
        $this->assertFalse($ad->containsAnyOf("Cercasii, kimono"));
        $this->assertFalse($ad->containsAnyOf("ente., old"));
    }


    public function testShowAsHtml(){
        $ad = $this->getMock('Ad', array('date', 'url', 'content', 'author'));
        // $ad->expects($this->once())->method('date')->will($this->returnValue('01 Sept 2013 20:23'));
        // $ad->expects($this->once())->method('url') ->will($this->returnValue('www.million.com'));
        // $ad->expects($this->once())->method('content')->will($this->returnValue('1000$/h for you'));
        // $ad->expects($this->once())->method('author')->will($this->returnValue('B. Gates'));
        $ad->date = '01 Sept 2013 20:23';
        $ad->url = 'www.million.com';
        $ad->content = '1000$/h for you';
        $ad->author = 'B. Gates';
        $this->assertTrue(method_exists('Ad', 'showAsHtml'));
        $this->assertEquals($ad->showAsHtml(), 
            '01 Sept 2013 20:23 <a href="www.million.com" target="_blank">1000$/h for you</a> B. Gates');
    }
}
?>