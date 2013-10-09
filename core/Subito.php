<?php
require_once 'AdProvider.php';
/**
* Represents a specific case of AdProvider class corresponding to "Subito" page.
* @author A.Shcherbakov
* @author veontomo@gmail.com
* @version 0.0.1 
*/
class Subito implements AdProvider{
	
	private $url;
	private $urlPattern;
	
	public function url(){
		return $this->url;
	}
	public function urlPattern(){
		return $this->urlPattern;
	}
	public function retrievePage($url){
		return null;
	}
}


?>