<?php
/**
* Retrieves all the ads satisfying the input requirements.
* The function produces an array of ads that satisfy the requirements: category, keywords, timeMin, timeMax.
* @author A.Shcherbakov
* @version 0.0.1
* @param string 	$str 	json encoded hash with keys: category, keywords, timeMin, timeMax. 
* Both timeMin and timeMax are integers.
* @return array 	array of ads
*/
function retrieveAds($input){
	if(!array_key_exists('category', $input) || empty($input['category'])){
		return array('success' => false, 'message' => 'Categoria mancata');
	}
	$timeMin = array_key_exists('timeMin', $input) ? $input['timeMin'] : strtotime('-1 hour');
	$timeMax = array_key_exists('timeMax', $input) ? $input['timeMax'] : time();
	$keywords = array_key_exists('keywords', $input) ? htmlspecialchars($input['keywords']) : '';

	if($timeMax < $timeMin){
		return array('success' => false, 'message' => 'Il tempo di inizio non pu&ograve; essere dopo quello di fine.');
	}

	$adsOutput = array();
	$categ = $input['category'];
	$urls = categoryToUrls($categ);
	foreach ($urls as $provider => $urlArr) {
		$adsCurrent = NULL; // reset the content of this variable to avoid duplicating data when looping
		$Provider = ucfirst($provider);
		$fnName = 'retrieveFrom'.$Provider;
		if(function_exists($fnName)){
			$inputData = array('urlArr'    => $urlArr, 
								'timeMin'  => $timeMin, 
								'timeMax'  => $timeMax, 
								'keywords' => $keywords);
			$adsCurrent = $fnName($inputData);
		}else{
			$info = date('Y d M H:i:s ', time()).__FUNCTION__.'unknown provider: '.$provider
				.', function '.$fnName. ' does not exist.'."\n\n";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'processform.log', $info, FILE_APPEND);
		}
		if(is_array($adsCurrent)){
			$adsOutput = array_merge($adsOutput, $adsCurrent);
		}

	}
	$filteredAds = array();
	foreach ($adsOutput as $ad) {
		if($ad->containsAnyOf($keywords)){
			$filteredAds[] = $ad;
		}
	}
	return array('success' => true, 'result' => $filteredAds);

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
	 			'portaportese' => array(
	 				'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/',
	 				'http://www.portaportese.it/rubriche/Lavoro/Lavoro_generico/',
	 				'http://www.portaportese.it/rubriche/Lavoro/Scuola_e_lezioni_private/'
	 				),
	 			'subito' => array('http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/')
	 		);
	 		break;
	 	case 'arredamento':
	 		$urls = array(
	 			'subito' => array('http://www.subito.it/annunci-lazio/vendita/arredamento-casalinghi/'),
	 			'portaportese' => array(
	 				'http://www.portaportese.it/rubriche/Casa/Arredamento_Mobili/',
	 				'http://www.portaportese.it/rubriche/Casa/Antiquariato_Quadri/',
	 				'http://www.portaportese.it/rubriche/Regali/'
 				)
 			);
	 		break;
	 	case 'veicoli':
	 		$urls = array(
	 			'subito' => array(
	 				'http://www.subito.it/annunci-lazio/vendita/auto/',
		 			'http://www.subito.it/annunci-lazio/vendita/accessori-auto/',
		 			'http://www.subito.it/annunci-lazio/vendita/moto-e-scooter/',
		 			'http://www.subito.it/annunci-lazio/vendita/accessori-moto/',
		 			'http://www.subito.it/annunci-lazio/vendita/caravan-e-camper/',
		 			'http://www.subito.it/annunci-lazio/vendita/altri-veicoli/'),
	 			'portaportese' => array(
	 				'http://www.portaportese.it/rubriche/Veicoli/Auto_italiane/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Auto_straniere/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Fuoristrada/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Auto_d_epoca_Speciali/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Grandi_veicoli_e_da_lavoro/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Moto/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Bici/',
	 				'http://www.portaportese.it/rubriche/Veicoli/Accessori_ricambi/'
	 				)
	 		);
			break;
	 	default:
			$urls = array();

	}
	return $urls;
}


function retrieveFromSubito($inputData){
	require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Subito.php';
	$urls = $inputData['urlArr'];
	$timeMax = $inputData['timeMax'];
	$timeMin = $inputData['timeMin'];
	$subito = new Subito;
	$output = array();
	foreach ($urls as $url) {
		$subito->setUrl($url);
		$subito->setTimeMax($timeMax);
		$subito->setTimeMin($timeMin);
		$adsCurrent = $subito->retrieveAds();
		if(is_array($adsCurrent) && !empty($adsCurrent)){
			$output = array_merge($output, $adsCurrent);
		}
	}
	return $output;
}


/**
* @todo for each url connect to the site and find the list of available dates of publication.
* For example, for http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/ on the left
* there is a list <ul class="filterList"> which contains links to all available ads sorted by date:
* <a href="/rubriche/Lavoro/Lavoro_qualificato/m-usC77">venerdì 18 ottobre 2013</a>
*/

function retrieveFromPortaportese($inputData){
	require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Portaportese.php';
	$urls = $inputData['urlArr'];
	$timeMax = $inputData['timeMax'];
	$timeMin = $inputData['timeMin'];

	$output = array();

	$pp = new Portaportese;
	$pp->keywords = $inputData['keywords'];
	foreach ($urls as $url) {
		$adsCurrent = NULL;
		$pp->setUrl($url);
		$pp->setTimeMax($timeMax);
		$pp->setTimeMin($timeMin);
		$adsCurrent = $pp->retrieveAds();
		if(is_array($adsCurrent) && !empty($adsCurrent)){
			$output = array_merge($output, $adsCurrent);
		}
	}
	return $output;

}

?>