<?php
/**
* convert a date writen in italian into english into the following format:  d M Y H:i
* transform human date string into a computer format 
* @param String $str 
* @return String Return a time formatted as "d M Y H:i"
* @author A.Shcherbakov
* @version 0.0.2, 26 Oct 2013
* @example if now is 10 Oct 2013 10:25, then formatTime must do the following:
*   Oggi 01:48 		-> 10 Oct 2013 01:48 (time in past)
*	Oggi 12:48 		-> 10 Oct 2013 12:48 (time in future, but if word "Oggi" is present, the year remains 2013)
* 	10 Oct 12:48 	-> 10 Oct 2012 12:48 (this year 10 Oct 12:48 is yet to come, so it should refer to last year)
*	Oggi12:48 		-> 10 Oct 2013 12:48 (the case of no space btw "Oggi" and time)
* 	15 Oct 21:09 	-> 15 Oct 2012 21:09 (this year 15 Oct is yet to come, so if should refer to last year)
*	Ieri 14:03 		-> 11 Sep 2013 14:03 (time in past)
*	Ieri14:03 		-> 11 Sep 2013 14:03 (time in past, no space)
*	6 ago 20:21 	-> 6 Aug 2013 20:21  (time in past)
*   martedì 16 ottobre 2013 -> 16 Oct 2013
*/
function formatTime2($str){
	echo "input: ".$str.PHP_EOL;
	$today = date("d M");
	$yearNow = date('Y');
	if(preg_match('/oggi/i', $str)){
		$result = preg_replace('/oggi/i', $today.' '.$yearNow.' ', $str);
		return date('d M Y H:i', strtotime($result));
	}
	$yesterday = date("d M", strtotime("-1 day"));
	$monthEng = array('Jan', 'Feb', 'Mar', 
		'Apr', 'May', 'Jun', 
		'Jul', 'Aug', 'Sep', 
		'Oct', 'Nov', 'Dec');
	$monthIt = array('/gennaio/i', '/febbraio/i', '/marzo/i', '/aprile/i',
						'/maggio/i', '/giugno/i', '/luglio/i', '/agosto/i', 
						'/settembre/i', '/ottobre/i', '/novembre/i', '/dicembre/i');

	$pattern = array('/Oggi/i', '/Ieri/i', 
		'/gen/i', '/feb/i', '/mar/i', 
		'/apr/i', '/mag/i', '/giu/i', 
		'/lug/i', '/ago/i', '/set/i', 
		'/ott/i', '/nov/i', '/dic/i');
	$repl = array_map(function($arg){
		return $arg.date(" Y");
		}, 
		array_merge(array($today, $yesterday), $monthEng)
		);

	$str = preg_replace($monthIt, $monthEng, $str);
	$str = preg_replace($pattern, $repl, $str);
	echo "str2: ".$str.PHP_EOL;
	
	$str =  preg_replace('/lunedì|martedì|mercoledì|giovedì|venerdì|sabato|domenica/', '', $str);
	echo "str3: ".$str.PHP_EOL;

	
	echo "str: ".$str.PHP_EOL;
	$result = date("d M Y H:i", strtotime($str));
	echo "result: ".$result.PHP_EOL;

	if(strtotime($result)>time()){
		$yearBefore = date("Y", strtotime($result))-1;
		$result = date("d M $yearBefore H:i", strtotime($result));
	}
	return $result;
}	


function formatTime($str){
	// elaborate "oggi", "ieri"
	$today = date('d M');
	$yearToday = date('Y');
	$yesterday = date('d M', strtotime('-1 day'));
	$yearYest = date('Y', strtotime('-1 day'));

	if(preg_match('/oggi|ieri/i', $str)){
		$output = preg_replace(array('/oggi/i', '/ieri/i'), 
			array(" $today $yearToday ", " $yesterday $yearYest "),  $str);
		return date('d M Y H:i', strtotime($output));
	}

	// drop days of week
	$daysOfWeek = array('/luned(i|ì)/i', '/marted(i|ì)/i', '/mercoled(i|ì)/i', 
						'/gioved(i|ì)/i', '/ven(erd(i|ì))?/i', '/sabato/i', '/domenica/i');
	
	$output = preg_replace($daysOfWeek, '', $str);

	// whether four digits are present in the input string
	$yearDetected = preg_match('/\d{4}/', $output);
	// If the year is present in the input, $year is empty. 
	// Otherwise,  $year is the current year.
	$year = $yearDetected ? '' : ' '.$yearToday;

	// converts month names form italian to english
	$monthIt = array('/gen(naio)?/i', '/feb(braio)?/i', '/mar(zo)?/i', 
		'/apr(ile)?/i',	'/mag(gio)?/i', '/giu(gno)?/i', 
		'/lug(lio)?/i', '/ago(sto)?/i', '/set(tembre)?/i', 
		'/ott(obre)?/i', '/nov(embre)?/i', '/dic(embre)?/i');
	$monthEng = array('Jan'.$year, 'Feb'.$year, 'Mar'.$year, 
			'Apr'.$year, 'May'.$year, 'Jun'.$year, 
			'Jul'.$year, 'Aug'.$year, 'Sep'.$year, 
			'Oct'.$year, 'Nov'.$year, 'Dec'.$year);
	$output = preg_replace($monthIt, $monthEng, $output);

	$output = date("d M Y H:i", strtotime($output));

	$outputTime = strtotime($output);
	// if the found time turns out to be in future and the 
	// year was guessed (it was not present in the input), 
	// then replace it by the previous year one
	if($outputTime>time() && !$yearDetected){
		$yearBefore = date('Y', strtotime("-1 year"));
		$output = date("d M $yearBefore H:i", $outputTime);
	}

	return $output;
}	

?>