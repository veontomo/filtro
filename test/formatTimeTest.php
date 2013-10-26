<?php

require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'formatTime.php';
class formatTimeTest extends PHPUnit_Framework_TestCase
{
	// public function testFormatTime(){
 // 	    $today = date("d M Y");
 // 	    $todayFutureDate = date('H:i', strtotime('+10 minutes'));
 // 	    $todayPastDate = date('H:i', strtotime('-10 minutes'));
 // 	    $yesterday = date("d M Y", strtotime("-1 day"));
 // 	    $year = date("Y");
 // 	    $yearBefore = $year-1;
 // 	    $this->assertEquals(formatTime("Oggi ".$todayPastDate), "$today ".$todayPastDate);
 // 	    $this->assertEquals(formatTime("Oggi ".$todayFutureDate), "$today ".$todayFutureDate);
 // 	    $this->assertEquals(formatTime("Oggi".$todayPastDate), "$today ".$todayPastDate);
 // 	    $this->assertEquals(formatTime("Oggi".$todayFutureDate), "$today ".$todayFutureDate);
 // 	    $this->assertEquals(formatTime("Ieri 02:48"), "$yesterday 02:48");
 // 	    $this->assertEquals(formatTime("Ieri02:48"), "$yesterday 02:48");
 // 	    $this->assertEquals(formatTime("20 ago 22:14"), "20 Aug $year 22:14");
 // 	    $this->assertEquals(formatTime("01 gen 22:14"), "01 Jan $year 22:14");
 // 	    $this->assertEquals(formatTime("01 Feb 22:14"), "01 Feb $year 22:14");
 // 	    $this->assertEquals(formatTime("01 mar 2:14"), "01 Mar $year 02:14");
 // 	    $this->assertEquals(formatTime("venerdì 11 Ott"), "11 Oct $year 00:00");
 // 	    $this->assertEquals(formatTime("lunedì 9 Mar"), "09 Mar $year 00:00");
 // 	    $this->assertEquals(formatTime("mercoledì 21 Lug"), "21 Jul $year 00:00");
 // 	    $this->assertEquals(formatTime("mercoledì 18 Dic"), "18 Dec $yearBefore 00:00");
 // 	    $this->assertEquals(formatTime("mercoledì 18 dicembre 2013"), "18 Dec 2013 00:00");

 // 	}


 	/**
 	* @group current
 	*/
	public function testFormatTime(){
 	    $today = date("d M Y");
 	    $todayFutureDate = date('H:i', strtotime('+10 minutes'));
 	    $todayPastDate = date('H:i', strtotime('-10 minutes'));
 	    $yesterday = date("d M Y", strtotime("-1 day"));
 	    $year = date("Y");
 	    $yearBefore = $year-1;


 	    $this->assertEquals(formatTime('oggi 12:45'), $today.' 12:45');
 	    $this->assertEquals(formatTime('Oggi 12:45'), $today.' 12:45');
 	    $this->assertEquals(formatTime('oggi12:45'), $today.' 12:45');
 	    $this->assertEquals(formatTime('Oggi12:45'), $today.' 12:45');

 	    $this->assertEquals(formatTime('ieri 12:45'), $yesterday.' 12:45');
 	    $this->assertEquals(formatTime('Ieri 12:45'), $yesterday.' 12:45');
 	    $this->assertEquals(formatTime('ieri12:45'), $yesterday.' 12:45');
 	    $this->assertEquals(formatTime('Ieri12:45'), $yesterday.' 12:45');

 	    $this->assertEquals(formatTime('10 gennaio 2008 11:10'), '10 Jan 2008 11:10');
 	    $this->assertEquals(formatTime('10 gen 2008 11:10'), '10 Jan 2008 11:10');

 	    $this->assertEquals(formatTime("Oggi ".$todayPastDate), "$today ".$todayPastDate);
 	    $this->assertEquals(formatTime("Oggi ".$todayFutureDate), "$today ".$todayFutureDate);
 	    $this->assertEquals(formatTime("Oggi".$todayPastDate), "$today ".$todayPastDate);
 	    $this->assertEquals(formatTime("Oggi".$todayFutureDate), "$today ".$todayFutureDate);
 	    $this->assertEquals(formatTime("Ieri 02:48"), "$yesterday 02:48");
 	    $this->assertEquals(formatTime("Ieri02:48"), "$yesterday 02:48");
 	    $this->assertEquals(formatTime("20 ago 22:14"), "20 Aug $year 22:14");
 	    $this->assertEquals(formatTime("01 gen 22:14"), "01 Jan $year 22:14");
 	    $this->assertEquals(formatTime("01 Feb 22:14"), "01 Feb $year 22:14");
 	    $this->assertEquals(formatTime("01 mar 2:14"), "01 Mar $year 02:14");
 	    $this->assertEquals(formatTime("venerdì 11 Ott"), "11 Oct $year 00:00");
 	    $this->assertEquals(formatTime("lunedì 9 Mar"), "09 Mar $year 00:00");
 	    $this->assertEquals(formatTime("mercoledì 21 Lug"), "21 Jul $year 00:00");
 	    $this->assertEquals(formatTime("mercoledì 18 Dic"), "18 Dec $yearBefore 00:00");
 	    $this->assertEquals(formatTime("mercoledì 18 dicembre 2013"), "18 Dec 2013 00:00");

 	}

 }

?>