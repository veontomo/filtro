<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'formatTime.php';
class formatTimeTest extends PHPUnit_Framework_TestCase
{
	public function testFormatTime(){
 	    $today = date("d M Y");
 	    $yesterday = date("d M Y", strtotime("-1 day"));
 	    $year = date("Y");
 	    $this->assertEquals(formatTime("Oggi 12:48"), "$today 12:48");
 	    $this->assertEquals(formatTime("Oggi12:48"), "$today 12:48");
 	    $this->assertEquals(formatTime("Ieri 02:48"), "$yesterday 02:48");
 	    $this->assertEquals(formatTime("Ieri02:48"), "$yesterday 02:48");
 	    $this->assertEquals(formatTime("20 ago 22:14"), "20 Aug $year 22:14");
 	    $this->assertEquals(formatTime("01 gen 22:14"), "01 Jan $year 22:14");
 	    $this->assertEquals(formatTime("01 Feb 22:14"), "01 Feb $year 22:14");
 	    $this->assertEquals(formatTime("01 mar 2:14"), "01 Mar $year 02:14");
 	    $this->assertEquals(formatTime("venerdì 11 Ott"), "11 Oct $year 00:00");
 	    $this->assertEquals(formatTime("lunedì 9 Mar"), "09 Mar $year 00:00");
 	    $this->assertEquals(formatTime("mercoledì 21 Lug"), "21 Jul $year 00:00");
 	    $this->assertEquals(formatTime("mercoledì 18 Dic"), "18 Dec $year 00:00");

 	}
 }

?>