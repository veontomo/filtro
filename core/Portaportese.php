<?php
require_once 'AdProvider.php';
require_once 'FileRetrieval.php';
require_once 'Ad.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'formatTime.php';

/**
* Represents a specific case of AdProvider class corresponding to "Portaportese" page.
* @author A.Shcherbakov
* @author veontomo@gmail.com
* @version 0.0.1 
*/
class Portaportese implements AdProvider{
	/**
	* @var 	string $url url of the 'entry page'
	* @example 'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/'
	*/
	private $url;

	/**
	* @var 	string $urlPattern a pattern to parametrize all pages inside the 'entry page'
	* @example 'm-us75&pag5' corresponds to the second page (PLACEHOLDER2 = 5) in the pages
	* published 18/10/2013 (PLACEHOLDER1 = 75)
	*/
	private $urlPattern = 'm-usCPLACEHOLDER1&pagPLACEHOLDER2';



	/**
	* @var 		string 	$page url of the specific page inside 'entry page'
	* @example	if for the 'entry page' $url is 'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/'
	* then $page might be equal to 'm-usC72&pag4', so that complete url of the page is 
	* 'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag4'
	*/
	public $page;

	/** 
	* the max value of the ad publication time. Default value is set to now.
	* @var integer  the earliest possible time of the publication of ad. 
	*/
	private $timeMax;

	/** 
	* the min value of the ad publication time. Default value is set to 1 hour before now.
	* @var integer  the latest possible time of the publication of ad. 
	*/
	private $timeMin;


	/**
	* Constructor: imposes the default value of the timeMax (current time) and timeMin (yesterday)
	*/
	public function __construct(){
		$this->timeMax = time();
		$this->timeMin = strtotime('-7 day');
	}
	
	/**
	* Getter for the url
	* @param 	void
	* @return 	string 		url for the 'entry page'
	*/
	public function url(){
		return $this->url;
	}

	/**
	* Setter for the timeMax
	* If the argument is a string, then it is transformed into an integer corresponding to the time format 
	* and if this operation is successful, the timeMax is set to  that integer
	* If the argument is an integer, the timeMax is set to this integer
	* @param 	mixed 	$str 	string or integer
	* @return 	boolean 		true if assignment is successeful, false - otherwise
	*/
	public function setTimeMax($arg){
		if(is_int($arg)){
			$this->timeMax = $arg;
			return true;
		}
		if(is_string($arg)){
			$attempt = strtotime($arg);
			if($attempt){
				$this->timeMax = $attempt;
				return true;
			}
		}
		return false;
	}


	/**
	* Getter for the timeMax
	* @param void
	* @return integer 	timeMax
	*/
	public function timeMax(){
		return $this->timeMax;
	}

	/**
	* Setter for the timeMin
	* If the argument is a string, then it is transformed into an integer corresponding to the time format 
	* and if this operation is successful, the timeMin is set to  that integer
	* If the argument is an integer, the timeMin is set to this integer
	* @param mixed 		$str 	string or integer
	* @return boolean 			true if assignment is successeful, false - otherwise
	*/
	public function setTimeMin($arg){
		if(is_int($arg)){
			$this->timeMin = $arg;
			return true;
		}
		if(is_string($arg)){
			$attempt = strtotime($arg);
			if($attempt){
				$this->timeMin = $attempt;
				return true;
			}
		}
		return false;
	}

	/**
	* Getter for the timeMin
	* @param 	void
	* @return 	integer 	timeMin
	*/
	public function timeMin(){
		return $this->timeMin;
	}

	/**
	* Setter for the url
	* @param 	string  $url	url for the 'entry page'
	* @return 	void
	*/
	public function setUrl($url){
		$this->url = $url;
	}

	/**
	* Getter for the urlPattern
	* @param 	void
	* @return 	string 	a pattern to parametrize all pages inside the 'entry page'
	*/
	public function urlPattern(){
		return $this->urlPattern;
	}

	/**
	* retrieve all the advertisments from the "entry page" and forthcoming ones defined by the url pattern
	* @param 	void
	* @return 	array 	an array each element of which is an instance of class Ad.
	*/
	public function retrieveAds(){
		$counter1 = 75;
		$counter2 = 1;
		$ads = array();
		do{
			$page = preg_replace(array('/PLACEHOLDER1/', '/PLACEHOLDER2/'), 
				array($counter1, $counter2), $this->urlPattern);
			$adsOnePage = $this->retrieveAdsOnePage($page);
			$counter2++;
			if(is_array($adsOnePage) && !empty($adsOnePage)){
				$ads = array_merge($ads, $adsOnePage);
				$isEnough = false;
			}else{
				$isEnough = true;
			}
		}
		while (!$isEnough);
		return $ads;
	}


	/** 
	* Produces the content of the page which location is given by concatenation of $url and $page
	* @param 	string 	$page 		url of the page parametrized by the $page in the bunch of $this->url  
	* @return 	string 				content of the page given by $url
	* @uses 	FileRetrieval::retrieveFromWeb() to retrieve the content
	*/
	public function pageContent($page){
		$urlComplete = $this->url.$page;
		$retr = new FileRetrieval;
		$retr->setUrl($urlComplete);
		$retr->setRepoDir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'repository'.DIRECTORY_SEPARATOR);
		$content = $retr->lazyRetrieval();
		if(!$content){
			$content = ''; // if the output of lazyRetrieval is null or false, $content is an empty string
		}
		return $content;
	}



	/**
	* Produces an array each element of which is an instance of Ad class
	* @uses		Ads
	* @param 	string 		$page 	url of the page parametrized by the $page in the bunch of $this->url
	* @return 	array 		an array each element of which is an instance of Ad class
	*/
	public function retrieveAdsOnePage($page){
		$output = array();
		$content =  $this->pageContent($page);
		if(!$content){ return $output;}

		$previousSetting = libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		libxml_use_internal_errors($previousSetting); // set the initial value of libxml_use_internal_errors
		$xpath = new DOMXpath($doc);

		$ads = $xpath->query("//*/div[@class='ris mod']");
		foreach ($ads as $ad) {
			$adCurrent = new Ad;
			$dates = array();
			$dateNodes = $xpath->query('div/div/span[@class="data"]', $ad);
			// it is supposed to be just one date in the ad, so if others are present, neglect them
			if($dateNodes->length > 0){
				$adCurrent->date = $dateNodes->item(0)->nodeValue;
			}

			$descrNodes = $xpath->query('div/div[@class="primary"]', $ad);
			// it is supposed to be just one description in the ad, so if others are present, neglect them
			if($descrNodes->length > 0){
				$descrNode = $descrNodes->item(0);
				$adCurrent->content = trim(preg_replace('/(\s)+/', ' ', $descrNode->nodeValue));
				$linkNodes = $descrNode->getElementsByTagName('a');
				if($linkNodes->length > 0){
					$adCurrent->url = $this->url.$linkNodes->item(0)->getAttribute('href');
				}
			}
			$output[] = $adCurrent;
		}

		return $output;
	}


	/**
	* connects to the url given by $this->url() and finds out the dates of publications
	*/
	public function retrieveDates(){
		$retr = new FileRetrieval;
		$retr->setUrl($this->url());
		$content = $retr->retrieveFromWeb();


		$previousSetting = libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		libxml_use_internal_errors($previousSetting); // set the initial value of libxml_use_internal_errors
		$xpath = new DOMXpath($doc);

		$ads = $xpath->query('//*/ul[@class="filterList"]/li');
		
		$output = array();
		foreach ($ads as $ad) {
			$links = $ad->getElementsByTagName('a');
			if(!empty($links)){
				$link = $links->item(0)->getAttribute('href');
			}
			$prefix = basename($link);
			$time = $ad->nodeValue;
			$timeFormatted = formatTime($time);
			echo "time: $time, after formatting: $timeFormatted".PHP_EOL;
			$output[$prefix] = formatTime($ad->nodeValue);
		}
		return $output;





	}

}


?>