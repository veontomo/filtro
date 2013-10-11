<?php

$output = array();
$content =  file_get_contents('resources/subito/th=1&o=2');

$previousSetting = libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($content);
libxml_use_internal_errors($previousSetting); // set the initial value of libxml_use_internal_errors
$xpath = new DOMXpath($doc);

// $ads = $xpath->query("//*/ul[@class='list']/li");
// foreach ($ads as $ad) {

	// $adDivs = $ad->getElementsByTagName("div");
	// foreach ($adDivs as $adDiv) {
	// 	if($adDiv->hasAttribute('class') && $adDiv->getAttribute('class') == 'date'){
	// 		echo $adDiv->nodeValue .'<br />';
	// 	}
	// 	if($adDiv->hasAttribute('class') && $adDiv->getAttribute('class') == 'descr'){
	// 		echo trim($adDiv->nodeValue).'<br />';
	// 		$links = $adDiv->getElementsByTagName('a');
	// 		foreach ($links as $link) {
	// 			echo basename($link->getAttribute('href')).'<br />';
	// 		}
	// 	}
	// }
	// }

	$ads = $xpath->query("//*/ul[@class='list']/li");
	foreach ($ads as $ad) {
		$date = $xpath->query("div[@class='date']", $ad)->item(0);
		echo $date->nodeValue;

	}

?>