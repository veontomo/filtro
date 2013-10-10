<?php
require_once 'AdProvider.php';
require_once 'FileRetrieval.php';
require_once 'Ad.php';

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
	* Getter for the url
	* @param void
	* @return String 	url for the 'entry page'
	*/
	public function url(){
		return $this->url;
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
	* retrive all the advertisments from the 'entry page" and forthcoming ones defined by the url pattern
	* @param void
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
	* convert a date writen in italian into english into the following format:  d M Y H:i
	* @param String $str 
	* @return String Return a time formatted as "d M"  
	* @example
	*	Oggi 12:48 -> 12 Sep 2013 12:48
	*	Ieri 14:03 -> 11 Sep 2013 14:03
	*	6 ago 20:21 -> 6 Aug 2013 20:21
	*
	*/
	public function formatTime($str){
		$today = date("d M");
		$yesterday = date("d M", strtotime("-1 day"));
		$pattern = array('/Oggi/i', '/Ieri/i', '/gen/i', '/feb/i', '/mar/i', '/apr/i', '/mag/i', '/giu/i', '/lug/i', '/ago/i', '/set/i', '/ott/i', '/nov/i', '/dic/i');
		$repl = array_map(function($arg){
			return $arg.date(" Y");
			}, 
			array($today, $yesterday, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dic'));
		$str2 = preg_replace($pattern, $repl, $str);
		$result = date("d M Y H:i", strtotime($str2));
		return $result;
	}	

	/**
	* Produces an array each element of which is an instance of Ad class
	* @uses		Ads
	* @param 	String 		$page 	url of the page parametrized by the $page in the bunch of $this->url
	* @return 	Array 		an array each element of which is an instance of Ad class
	* @todo 	refactor the xpath part in order to move the corresponding block into another class
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

			$adDivs = $ad->getElementsByTagName("div");
			foreach ($adDivs as $adDiv) {
				if($adDiv->hasAttribute('class') && $adDiv->getAttribute('class') == 'date'){
					$adCurrent->pubdate = $this->formatTime($adDiv->nodeValue);
				}
				if($adDiv->hasAttribute('class') && $adDiv->getAttribute('class') == 'descr'){
					$adCurrent->content = trim($adDiv->nodeValue);
					$links = $adDiv->getElementsByTagName('a');
					foreach ($links as $link) {
						$adCurrent->url .= $this->url.basename($link->getAttribute('href'));
					}
				}
			}
			$output[] = $adCurrent;
		}

		return $output;

	}

}


?>