<?php
require_once 'AdProvider.php';
require_once 'FileRetrieval.php';
require_once 'Ad.php';

/**
* Represents a specific case of AdProvider class corresponding to "Portaportese" page.
* @author A.Shcherbakov
* @author veontomo@gmail.com
* @version 0.0.1 
*/
class Portaportese implements AdProvider{
	/**
	* @var 	String $url url of the 'entry page'
	*/
	private $url;

	/**
	* @var 	String $urlPattern a pattern to parametrize all pages inside the 'entry page'
	*/
	private $urlPattern = 'm-usCPLACEHOLDER1&pagPLACEHOLDER2';

	/**
	* @var 	String $repoDir		absolute path to the repository 
	* directory in which local versions of the retrieved pages should be stored
	*/
	private $repoDir;


	/**
	* Setter for the repository directory
	* @param String $repoDir 	absolute path to the repository directory
	* @return void
	*/
	public function setRepoDir($repoDir){
			$this->repoDir = $repoDir;
	}

	/**
	* Getter for the repository directory
	* @param  	void
	* @return 	String 	absolute path to the repository
	*/
	public function repoDir(){
		return $this->repoDir;
	}


	/**
	* full path to a local copy of an external url
	* @var String $page
	* @example if $this->url is "http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/",
	* then localCopy("m-usC72&pag4") should be 
	* "portaportese/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag4"
	*/
	public function localPath($page){
		$urlInfo = parse_url($this->url);
		if(strpos($page,'&pag') === false){
			$page .= '&pag1';
		}
		$host = 'portaportese';
		return $host.$urlInfo['path'].$page;
	}



	/**
	* @var 		String 	$page url of the specific page inside 'entry page'
	* @example	if for the 'entry page' $url is 'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/'
	* then $page might be equal to 'm-usC72&pag4', so that complete url of the page is 
	* 'http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag4'
	*/
	public $page;

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
		$this->timeMin = strtotime('-7 day');
	}
	
	/**
	* Getter for the url
	* @param 	void
	* @return 	String 		url for the 'entry page'
	*/
	public function url(){
		return $this->url;
	}

	/**
	* Setter for the timeMax
	* If the argument is a string, then it is transformed into an integer corresponding to the time format 
	* and if this operation is successful, the timeMax is set to  that integer
	* If the argument is an integer, the timeMax is set to this integer
	* @param mixed $str String or Integer
	* @return Boolean true if assignment is successeful, false - otherwise
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
		return null;
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

		$ads = $xpath->query("//*/div[@class='ris mod']");
		foreach ($ads as $ad) {
			$adCurrent = new Ad;
			$dates = array();
			$dateNodes = $xpath->query('//span[@class="data"]', $ad);
			// it is supposed to be just one date in the ad, so if others are present, neglect them
			if($dateNodes->length > 0){
				$adCurrent->date = $dateNodes->item(0)->nodeValue;
			}

			$descrNodes = $xpath->query('//div[@class="primary"]', $ad);
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

}


?>