<?php
require_once 'AdProvider.php';
require_once 'FileRetrieval.php';
require_once 'Ad.php';
require_once 'helpers'.DIRECTORY_SEPARATOR.'formatTime.php';

/**
* Represents a specific case of AdProvider class corresponding to "Subito" page.
* @author A.Shcherbakov
* @author veontomo@gmail.com
* @version 0.0.1 
*/
class Subito implements AdProvider{
	/**
	* @var 	String $url url of the 'entry page'
	*/
	private $url;

	/**
	* @var 	String $urlPattern a pattern to parametrize all pages inside the 'entry page'
	*/
	private $urlPattern = '?th=PLACEHOLDER1&o=PLACEHOLDER2';


	/**
	* @var 	String $page url of the specific page inside 'entry page'
	* @example	if for the 'entry page' $url is 'http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/'
	* then $page might be equal to '?th=1&o=2', so that complete url of the page is 
	* 'http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/?th=1&o=2'
	*/
	public $page;

	/**
	* the maximal number of pages to look through.
	* When retrieving ads it might be nesessary to download several pages within a bunch of pages.
	* In order to avoid excessive number of pages to download, $maxPageIterations is introduced.
	*/
	public $maxPageIterations = 3;

	/** 
	* the max value of the ad publication time. Default value is set to now.
	* @var Integer  the earliest possible time of the publication of ad. 
	*/
	private $timeMax;

	/** 
	* the min value of the ad publication time. Default value is set to 1 hour before now.
	* @var Integer  the latest possible time of the publication of ad. 
	*/
	private $timeMin;


	/**
	* Constructor: imposes the default value of the timeMax (current time) and timeMin (yesterday)
	*/
	public function __construct(){
		$this->timeMax = time();
		$this->timeMin = strtotime('-1 day');
	}
	
	/**
	* Getter for the url
	* @param void
	* @return String 	url for the 'entry page'
	*/
	public function url(){
		return $this->url;
	}

	/**
	* Setter for the timeMax
	* If the argument is a string, then it is transformed into an integer corresponding to the time format 
	* and if this operation is successful, the timeMax is set to  that integer.
	* If the argument is an integer, the timeMax is set to this integer
	* @param 	string|integer 	$arg String or Integer
	* @return 	boolean 	true if assignment is successeful, false - otherwise
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
	* @return Integer 	timeMax
	*/
	public function timeMax(){
		return $this->timeMax;
	}

	/**
	* Setter for the timeMin
	* If the argument is a string, then it is transformed into an integer corresponding to the time format 
	* and if this operation is successful, the timeMin is set to  that integer
	* If the argument is an integer, the timeMin is set to this integer
	* @param mixed $str String or Integer
	* @return Boolean true if assignment is successeful, false - otherwise
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
	* @param void
	* @return Integer 	timeMin
	*/
	public function timeMin(){
		return $this->timeMin;
	}

	/**
	* Setter for the url
	* @param String  $url	url for the 'entry page'
	* @return void
	*/
	public function setUrl($url){
		$this->url = $url;
	}

	/**
	* Getter for the urlPattern
	* @param void
	* @return String 	a pattern to parametrize all pages inside the 'entry page'
	*/
	public function urlPattern(){
		return $this->urlPattern;
	}

	/**
	* retrive all the advertisments from the "entry page" and forthcoming ones defined by the url pattern
	* @param 	void
	* @return 	Array 	an array each element of which is an instance of class Ad.
	*/
	public function retrieveAds(){
		$counter = 1;
		$ads = array();
		$isEnough = false;
		do{
			$page = preg_replace(array('/PLACEHOLDER1/', '/PLACEHOLDER2/'), array(1, $counter), $this->urlPattern);
			$adRetrieved = $this->retriveAdsOnePage($page);
			$adRetrievedLen = count($adRetrieved);
			$adFiltered = array();
			for($i = 0; $i < $adRetrievedLen; $i++){
				$adCurrent = $adRetrieved[$i];
				$date = strtotime($adCurrent->date);
				// echo $i.": date: ".$adRetrieved[$i]->date, ", timeMax: ".$this->timeMax.", timeMin = ".$this->timeMin;
				if($date > $this->timeMax){
					// echo 'too young';
					continue;
				}
				if($date < $this->timeMin){
					$isEnough = true;
					// echo 'too old';
					break;
				};
				$adFiltered[] = $adRetrieved[$i];
			}
			$ads = array_merge($ads, $adFiltered);
			$counter++;
		}
		while ($counter < $this->maxPageIterations+1 && !$isEnough); 
		return $ads;
	}


	/** 
	* produces the content of the page which location is given by concatenation of $url and $page
	* @param 	String 	$page 		url of the page parametrized by the $page in the bunch of $this->url  
	* @return 	String 				content of the page given by $url
	* @uses 	FileRetrieval::retrieveFromWeb() to retrieve the content
	*/
	public function pageContent($page){
		$urlComplete = $this->url.$page;
		$retr = new FileRetrieval;
		$content = $retr->retrieveFromWeb($urlComplete);
		return $content;
	}


	/**
	* Produces an array each element of which is an instance of Ad class
	* @uses		Ads
	* @param 	String 		$page 	url of the page parametrized by the $page in the bunch of $this->url
	* @return 	Array 		an array each element of which is an instance of Ad class
	*/
	public function retriveAdsOnePage($page){
		$output = array();
		$content =  $this->pageContent($page);

		$previousSetting = libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		libxml_use_internal_errors($previousSetting); // set the initial value of libxml_use_internal_errors
		$xpath = new DOMXpath($doc);

		$ads = $xpath->query("//*/ul[@class='list']/li");
		foreach ($ads as $ad) {
			$adCurrent = new Ad;
			$dates = array();
			$dateNodes = $xpath->query('div[@class="date"]', $ad);
			// it is supposed to be just one date in the ad, so if others are present, neglect them
			if($dateNodes->length > 0){
				$adCurrent->date =  formatTime($dateNodes->item(0)->nodeValue);
			}

			$descrNodes = $xpath->query('div[@class="descr"]', $ad);
			// it is supposed to be just one description in the ad, so if others are present, neglect them
			if($descrNodes->length > 0){ 								
				$descrNode = $descrNodes->item(0);
				$adCurrent->content = htmlentities(trim(preg_replace('/(\s)+/', ' ', $descrNode->nodeValue)));
				$linkNodes = $descrNode->getElementsByTagName('a');
				if($linkNodes->length > 0){
					$adCurrent->url = $this->url.$linkNodes->item(0)->getAttribute('href');
				}
			}
			$output[] = $adCurrent;
		}

		return $output;

	}

}


?>