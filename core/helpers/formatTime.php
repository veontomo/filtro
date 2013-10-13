<?php
/**
* convert a date writen in italian into english into the following format:  d M Y H:i
* @param String $str 
* @return String Return a time formatted as "d M"  
* @example if today is 12 September 2013, then
*	Oggi 12:48 -> 12 Sep 2013 12:48
*	Oggi12:48 -> 12 Sep 2013 12:48
*	Ieri 14:03 -> 11 Sep 2013 14:03
*	6 ago 20:21 -> 6 Aug 2013 20:21
* @todo elaborate this case: if today is 1 Jan 2014, then the date 20 Dic will be transformed into "20 Dic 2014 00:00"
* while it must be "20 Dic 2013 00:00"
*/
function formatTime($str){
	$today = date("d M");
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
	echo 'input date: '.$str.', after replace date: '.$str2.', after dropping day of week: '.$str3.PHP_EOL;
	$result = date("d M Y H:i", strtotime($str3));
	if(strtotime($result)>time()){
		$result = date("d M Y H:i", strtotime($result, '-1 year'));
	}
	return $result;
}	


?>