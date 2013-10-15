<?php
/** Retrieves files from web 
* 
* @author A.shcherbakov
* @author veontomo@gmail.com
* @version 0.0.1
*/
class FileRetrieval{
	/**
	* @var String $url url of the file to retrieve
	*/
	private $url;

	/**
	* @var String $localPath path to a local copy of the resource given by $this->url
	* @example 
	* if $this->url is "http://www.abc.com/a/b/c/d.html", then $this->localPath() is "www.abc.com/a/b/c/d.html"
	* if $this->url is "http://www.abc.com/a/b/c/", then $this->localPath() is "www.abc.com/a/b/c/index.html"
	* if $this->url is "http://www.abc.com/a/b/c/d", then $this->localPath() is "www.abc.com/a/b/c/d"
	* if $this->url is "http://www.abc.com/a/b/c/d?aa=1", then $this->localPath() is "www.abc.com/a/b/c/dQUERYaa=1"
	*/
	private $localPath;


	/**
	* getter of $url
	* @var void
	* @return string url 
	*/
	public function url(){
		return $this->url;
	}

	/**
	* Setter of $url. Together with url, the path for storage of the url should be set.
	* @var string $url
	* @return void
	*/
	public function setUrl($url){
		$this->url = $url;
		$urlInfo = parse_url($this->url);
		$host = array_key_exists('host', $urlInfo) ? $urlInfo['host'] : 'unknownHost';
		$path = array_key_exists('path', $urlInfo) ? $urlInfo['path'] : 'unknownPath';
		$query = array_key_exists('query', $urlInfo) ? 'Q'.$urlInfo['query'] : '';
		// $pathInfo = pathinfo($path);
		$filename = preg_match('/\/$/i', $path) ? 'default' :  '';
		$this->localPath = implode(DIRECTORY_SEPARATOR, explode('/', $host.$path.$filename.$query));

	}


	/**
	* @var 	string $repoDir		absolute path to the repository 
	* directory in which local versions of the retrieved pages should be stored
	*/
	private $repoDir;


	/**
	* Setter for the repository directory
	* @param string $repoDir 	absolute path to the repository directory
	* @return void
	*/
	public function setRepoDir($repoDir){
			$this->repoDir = $repoDir;
	}

	/**
	* Getter for the repository directory
	* @param  	void
	* @return 	string 	absolute path to the repository
	*/
	public function repoDir(){
		return $this->repoDir;
	}


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


	/**
	* Getter to a local path. The local path is set automatically inside inside $this->setUrl().
	* @return string filename under which the file is supposed to be saved locally in $this->repoDir folder
	* @example if $this->url is "http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/test.html",
	* then $this->localPath should be 
	* "www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/test.html"
	*/
	public function localPath(){
		return $this->localPath;
	}


	/**
	* Retrieves the content of the file $this->localPath located in the repository.
	* If the file does not exist, returns false.
	* @return string|false content of the file or false if it does not exist.
	*/
	public function retrieveFromRepo(){
		$fn = $this->repoDir.$this->localPath;
		echo __CLASS__.':  '.$fn;
		if(file_exists($fn)){
			return file_get_contents($fn);
		}
		else{
			echo "file $fn does not exist!";
			return false;
		}
	}

}
?>