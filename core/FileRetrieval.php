<?php
/** Retrieves files from web 
* 
* @author A.Shcherbakov
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
	* If the url is of the form "http://www.test.com/a/b/c", then local path will be "a\b\c" fow Windows and "a/b/c" for Unix
	* (PHP constant DIRECTORY_SEPARATOR is used to delimiter the nested directories)
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
		if(!is_dir($repoDir)){
			$this->log("Warning: the repostory folder does not exist. It's not crucial, but do not forget to create it!");
		}
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
		if($this->localCopyExists()){
			return file_get_contents($fn);
		}else{
			return false;
		}
	}

	/**
	* Checks whether the local copy exists in the repository
	* @return boolean true if the local copy of $this->url is present in the repository, false otherwise
	*/
	public function localCopyExists(){
		return file_exists($this->repoDir().$this->localPath());
	}

	/**
	* Saves the string in the repository
	* @return boolean true if the content was saved successefully, false otherwise
	*/
	public function saveInRepo($content){
		if($this->createDirInRepo()){
			try {
				file_put_contents($this->repoDir.$this->localPath, $content);
				return true;
			} catch (Exception $e) {
				$traceInfo = debug_backtrace();
				$callerFile = $traceInfo[0]['file'];
				$lineNumber = $traceInfo[0]['line'];
				$this->log($e->getMessage()."\nfirst call from file: $callerFile, line: $lineNumber\n");
				return false;
			}
		}
		return false;
	}

	/**
	* Creates (eventually nested) directories $this->localPath inside the repository
	* @return boolean true if the requested directory is present after running this function, false otherwise
	*/
	public function createDirInRepo(){
		$pathInRepo = explode(DIRECTORY_SEPARATOR, dirname($this->localPath()));
		$len = count($pathInRepo);
		for($i=0; $i < $len; $i++){
			$dirName = $this->repoDir().implode(DIRECTORY_SEPARATOR, array_slice($pathInRepo, 0, $i+1));
			if(is_dir($dirName)){continue;}
			try{
				mkdir($dirName);
			}
			catch(Exception $e){
				$traceInfo = debug_backtrace();
				$callerFile = $traceInfo[0]['file'];
				$lineNumber = $traceInfo[0]['line'];
				$this->log($e->getMessage()."\nfirst call from file: $callerFile, line: $lineNumber\n");
				return false;
			};
		}
		return true;
	}

	/**
	* Returns a content of $this->url. 
	* If the corresponding file is present in the repo, returns the content of that file, otherwise
	* retrieves it from web and saves the copy of that file in the repository. If none of these 
	* operations succeed, returns false.
	* @return string|false content of $this->url
	*/
	public function lazyRetrieval(){
		if($this->localCopyExists()){
			return $this->retrieveFromRepo();
		}else{
			$content = $this->retrieveFromWeb();
			$this->saveInRepo($content);
			return $content;
		}
	}

	/**
	* Writes info in the log file. 
	* @var string 	$content 	message to put into the log file 
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
		$fileName = $targetDir.DIRECTORY_SEPARATOR.'log-'.basename(__FILE__,'.php');
		file_put_contents($fileName, $infoToWrite, FILE_APPEND);
		
	}

}
?>