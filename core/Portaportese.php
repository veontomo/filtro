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
	* @var string 	provider domain name 
	*/
	private $providerUrl = 'http://www.portaportese.it';

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
	* the keywords according to which the retrieved ads are supposed to be filtered out
	* @example if it is equal to "php, programmat, scrivania", that the ads containing
	* at least one of the strings "php", "programmat" or "scrivania" should pass through the filter.
	* If it is set to the empty string, all ads should pass through the filter. 
	* @var string the comma separated string of the keywords
	*/
	public $keywords;


	/**
	* Constructor: imposes the default value of the timeMax (current time) and timeMin (yesterday)
	*/
	public function __construct(){
		$this->timeMax = time();
		$this->timeMin = strtotime('-7 day');
		$this->keywords = NULL;
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
	* Retrieve all the advertisments for the time interval specified by $this->timeMax and $this->timeMin.
	* @param 	void
	* @return 	array 	an array each element of which is an instance of class Ad.
	*/
	public function retrieveAds(){
		$pagesToRetrieve = $this->linksInDateRange();
		$ads = array();
		foreach ($pagesToRetrieve as $key => $value) {
			$pageNumber = preg_replace('/m-usC/', '', $key);
			$currentDateAds = $this->retrieveAdsFixedDate($pageNumber);
			$ads = array_merge($ads, $currentDateAds);
		}
		return $ads;
	}

	/**
	* Retrieve all the advertisments from the "entry page" and forthcoming ones defined by the url pattern
	* @param 	integer 	$pageNumber  the first placeholder value that corresponds to the ads published at the same date 
	* @return 	array 	an array each element of which is an instance of class Ad.
	*/
	public function retrieveAdsFixedDate($pageNumber){
		$counter = 1;
		$ads = array();
		do{
			$page = preg_replace(array('/PLACEHOLDER1/', '/PLACEHOLDER2/'), 
				array($pageNumber, $counter), $this->urlPattern);
			$adsOnePage = $this->retrieveAdsOnePage($page);
			$counter++;
			if(is_array($adsOnePage) && !empty($adsOnePage)){
				$adsfiltered = array_filter($adsOnePage, function($ad){
					return $ad->containsAnyOf($this->keywords);
				});
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
	* Removes a previously saved page from the repository
	* @param string 	$page url of the page which local version should be removed from the repo 
	*/
	public function eraseFromRepo($page){
		$urlComplete = $this->url.$page;
		$retr = new FileRetrieval;
		$retr->setUrl($urlComplete);
		$retr->setRepoDir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'repository'.DIRECTORY_SEPARATOR);
		$retr->eraseFromRepo();
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
		$this->log($ads->length . " ads are retrieved. \n");

		// if no ads were found on the page, delete that page from the repo 	
		if($ads->length == 0){
			$this->eraseFromRepo($page);
			$this->log("the file {$this->url}$page  is supposed to be deleted.\n");

		};
		foreach ($ads as $ad) {
			$adCurrent = new Ad;
			$dates = array();
			$dateNodes = $xpath->query('div/div/span[@class="data"]', $ad);
			// it is supposed to be just one date in the ad, so if others are present, neglect them
			if($dateNodes->length > 0){
				$adCurrent->date = formatTime($dateNodes->item(0)->nodeValue);
			}

			$descrNodes = $xpath->query('div/div[@class="primary"]', $ad);
			// it is supposed to be just one description in the ad, so if others are present, neglect them
			if($descrNodes->length > 0){
				$descrNode = $descrNodes->item(0);
				$adCurrent->content = trim(preg_replace('/(\s)+/', ' ', htmlentities($descrNode->nodeValue)));
				$linkNodes = $descrNode->getElementsByTagName('a');
				if($linkNodes->length > 0){
					$adCurrent->url = $this->providerUrl.$linkNodes->item(0)->getAttribute('href');
				}
			}
			$output[] = $adCurrent;
		}

		return $output;
	}


	/**
	* connects to the url given by $this->url() and finds out the dates of publications.
	* @return array 	key => value pairs of the form  'm-usC73' => '04 Oct 2013 00:00'
	* where 'm-usC73' is a final part of the url "/rubriche/Lavoro/Lavoro_qualificato/m-usC73"
	* corresponding to ads published 04 Oct 2013.
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

		// the dates are items if an unordered list with class="filterList". One has to remove the first
		// item in the list, because it corresponds to "Tutti", not to ads published in a specific date. 
		$dates = $xpath->query('//*/ul[@class="filterList"]/li[position()>1]');
		
		$output = array();
		foreach ($dates as $date) {
			$links = $date->getElementsByTagName('a');
			if(!empty($links)){
				$link = $links->item(0)->getAttribute('href');
			}
			$prefix = basename($link);
			$output[$prefix] = formatTime($date->nodeValue);
		}
		return $output;
	}

	/**
	* Selects the links corresponding to the ads published within the range [$timeMin, $timeMax].
	* @param integer|NULL $timeMin 	starting time (if is not set, $this->timeMin is used.)
	* @param integer|NULL $timeMax 	ending time (if is not set, $this->timeMax is used.)
	*
	*/
	public function linksInDateRange($timeMin = NULL, $timeMax = NULL){
		if(!$timeMax) {
			$timeMax = $this->timeMax;
		}
		if(!$timeMin) {
			$timeMin = $this->timeMin;
		}

		$links = $this->retrieveDates();
		$output = array_filter($links, function($timeStr) use ($timeMin, $timeMax){
			$timeInt = strtotime($timeStr);
			return (($timeInt >= $timeMin) && ($timeInt <= $timeMax));
		});
		return $output;
	}


	/**
	* Writes info in the log file. 
	* For this class, the log file name is chosen to be Portaportese.log and is located in the directory "logs"
	* @param string 	$content 	message to put into the log file 
	* @return void
	*/
	private function log($content){
		$targetDir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'logs';
		// if the folder does not exist and there is no file with this name, then create the folder
		if(!is_dir($targetDir) && !file_exists($targetDir)){
			mkdir($targetDir);
		}
		$traceInfo = debug_backtrace();
		$callerFile = $traceInfo[0]['file'];
		$lineNumber = $traceInfo[0]['line'];
		$infoToWrite = date('Y d M H:i:s ', time())."\nFile: $callerFile, line: $lineNumber\n"
			. $content."\n\n";
		$fileName = $targetDir.DIRECTORY_SEPARATOR.basename(__FILE__,'.php').'.log';
		file_put_contents($fileName, $infoToWrite, FILE_APPEND);
		
	}


}


?>