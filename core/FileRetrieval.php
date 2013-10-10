<?php
/** Retrieves files from web 
* 
* @author A.shcherbakov
* @author veontomo@gmail.com
* @version 0.0.1
*/
class FileRetrieval{
	/**
	* url of the file to retrieve
	* @var String $url
	*/
	public $url;

	/**
	* retrieves the content of the resource given by $url. If the argument is not set, $this->url will be used.
	* Headings for the moment are hardcoded.
	* @param mixed $url either String or NULL. If NULL, then $this->url is to be used.
	* @return String the content of the retrieved page. 
	*/
	public function retrieveFromWeb($url=NULL){
		if($url === NULL) {
			$url = $this->url;
		}
		$opts = array('http' =>
		    array(
		        'method'  => 'GET',
		        'user_agent'  => "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2) Gecko/20100301 Ubuntu/9.10 (karmic) Firefox/3.6",
		        'header' => 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8, Host: www.portaportese.it'
		    )
		);
		$context  = stream_context_create($opts);
		// put @ to surpress warnings 
		@$content = file_get_contents($url, false, $context);
		return $content;
	}

}
?>