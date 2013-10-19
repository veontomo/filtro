<?php
/**
* Retrieves all the ads satisfying the input requirements.
* The function produces an array of ads that satisfy the requirements: category, keywords, timeMin, timeMax.
* @author A.Shcherbakov
* @version 0.0.1
* @param string 	$str 	json encoded hash with keys: category, keywords, timeMin, timeMax
* @return array 	array of ads
*/
function retrieveAds($str){
	$input = json_decode($str, true);
	if(!array_key_exists('category', $input)){
		return json_encode(array('success' => false, 'message' => 'Categoria mancata'));
	}
	$categ = $input['category'];
	$urls = categoryToUrls($categ);
	foreach ($urls as $provider => $urlArr) {
		$Provider = ucfirst($provider);
		$fnName = 'retrieveFrom'.$Provider;
		if(function_exists($fnName)){
			$ads = $fnName($urlArr);
		}else{
			$info = date('Y d M H:i:s ', time()).__FUNCTION__.'unknown provider: '.$provider
				.', function '.$fnName. ' does not exist.'."\n\n";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'processform.log', $info, FILE_APPEND);
		}

		return $ads;

	}

}

/**
* Converts category into urls.
* The function recieves a category name and transforms it into urls of supported ads providers: subito, portaportese.
* @author 	A.Shcherbakov
* @version 	0.0.1
* @param 	string 	$str 	category name
* @return 	array 	array of urls
*/
function categoryToUrls($categ){
	switch ($categ) {
	 	case 'lavoro':
	 		$urls = array(
	 			'subito' => array('http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/'),
	 			'portaportese' => array(
	 				'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/',
	 				'http://www.portaportese.it/rubriche/Lavoro/Lavoro_generico/',
	 				'http://www.portaportese.it/rubriche/Lavoro/Scuola_e_lezioni_private/'
	 			)
	 		);
	 		break;
	 	default:
			$urls = array();

	}
	return $urls;
}


function retrieveFromSubito($urls){
	require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Subito.php';
	$subito = new Subito;
	$output = array();
	foreach ($urls as $url) {
		$subito->setUrl($url);
		$subito->setTimeMax(time());
		$subito->setTimeMin('-1 day');
		$adsCurrent = $subito->retrieveAds();
		if(is_array($adsCurrent)){
			$output = array_merge($output, $adsCurrent);
		}
	}
	file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'processform.log', json_encode($output), FILE_APPEND);
	return $output;
}

function retrieveFromPortaportese($urls){

}

?>