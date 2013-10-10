<?php
require_once 'AdProvider.php';
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
	private $urlPattern;
	
	/**
	* Getter for the url
	* @param void
	* @return String 	url for the 'entry page'
	*/
	public function url(){
		return $this->url;
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
	* @return 	Array() 	an array each element of which is an instance of class Ad.
	*/
	public function retrievePage(){
		return null;
	}
}


?>