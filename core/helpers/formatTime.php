<?php
/**
* convert a date writen in italian into english into the following format:  d M Y H:i
* transform human date string into a computer format 
* @param String $str 
* @return String Return a time formatted as "d M"  
* @example if now is 10 Oct 2013 10:25, then formatTime must do the following:
*   Oggi 01:48 		-> 10 Oct 2013 01:48 (time in past)
*	Oggi 12:48 		-> 10 Oct 2013 12:48 (time in future, but if word "Oggi" is present, the year remains 2013)
* 	10 Oct 12:48 	-> 10 Oct 2012 12:48 (this year 10 Oct 12:48 is yet to come, so it should refer to last year)
*	Oggi12:48 		-> 10 Oct 2013 12:48 (the case of no space btw "Oggi" and time)
* 	15 Oct 21:09 	-> 15 Oct 2012 21:09 (this year 15 Oct is yet to come, so if should refer to last year)
*	Ieri 14:03 		-> 11 Sep 2013 14:03 (time in past)
*	Ieri14:03 		-> 11 Sep 2013 14:03 (time in past, no space)
*	6 ago 20:21 	-> 6 Aug 2013 20:21  (time in past)
* @todo elaborate this case: if today is 1 Jan 2014, then the date 20 Dic will be transformed into "20 Dic 2014 00:00"
* while it must be "20 Dic 2013 00:00"
*/
function formatTime($str){
	$today = date("d M");
	$yearNow = date('Y');
	if(preg_match('/oggi/i', $str)){
		$result = preg_replace('/oggi/i', $today.' '.$yearNow.' ', $str);
		return date('d M Y H:i', strtotime($result));
	}
	$yesterday = date("d M", strtotime("-1 day"));
	$pattern = array('/Oggi/i', '/Ieri/i', 
		'/gen/i', '/feb/i', '/mar/i', 
		'/apr/i', '/mag/i', '/giu/i', 
		'/lug/i', '/ago/i', '/set/i', 
		'/ott/i', '/nov/i', '/dic/i');
	$repl = array_map(function($arg){
		return $arg.date(" Y");
		}, 
		array($today, $yesterday, 
			'Jan', 'Feb', 'Mar', 
			'Apr', 'May', 'Jun', 
			'Jul', 'Aug', 'Sep', 
			'Oct', 'Nov', 'Dec'));
	$str2 = preg_replace($pattern, $repl, $str);
	
	$str3 =  preg_replace('/lunedì|martedì|mercoledì|giovedì|venerdì|sabato|domenica/', '', $str2);
	$result = date("d M Y H:i", strtotime($str3));

	if(strtotime($result)>time()){
		$yearBefore = date("Y", strtotime($result))-1;
		$result = date("d M $yearBefore H:i", strtotime($result));
	}
	return $result;
}	


?>