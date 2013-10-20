<?php
/**
* Represents an advertisement
* @author 	A.Shcherbakov
* @author 	veontomo@gmail.com
* @version 	0.0.2
*
*/

class Ad{
	/**
	* @var Integer 	$date 	publication date of the ad
	*/
	public $date;

	/**
	* @var String $url 		link to the ad (where it is published)
	*/
	public $url;
	/**
	* @var String $content 	text of the ad	
	*/
	public $content;
	/**
	* @var String $author 		author of the ad
	*/
	public $author;

	/**
	* check whether the ad contains given string (case insensitive). 
	* Empty string is supposed to be contained in all strings (including empty one).
	* Empty string is supposed to contain only emtpy string.
	* @param 	String 		$string 	check the presence of this string in the ad's content
	* @return 	Boolean 	true if the content attribute contains $string, false otherwise
	*/
	public function containsString($string){
		if($string === '') { return true;}
		if(empty($this->content)) {return false;}
		$text = strtolower($this->content);
		$needle = strtolower($string);
		return !(strpos($text, $needle) === false);
	}

	/**
	* check whether the ad contains at least one of the words in the string of comma separated string (case insensitive). 
	* Empty string is supposed to be contained in all strings (including empty one).
	* Empty string is supposed to contain only emtpy string.
	* @param 	string 		$css 	string of comma separated words
	* @return 	boolean 	true if the content attribute contains at least one of the words in $css, false otherwise
	*
	*/
	public function containsAnyOf($css){
		if($css === '') {
			return true;
		}
		if(empty($this->content)) {return false;}
		$output = false;
		$keywordArray = array_map('trim', explode(',', strtolower($css)));
		$text = strtolower($this->content);
		foreach ($keywordArray as $keyword) {
			if($this->containsString($keyword)){
				$output = true;
				break;
			}
		}
		return $output;

	}

	/**
	* Gives a string collecting all the info about the ad in an html form
	* @return string 	html representation of the ad
	*/
	public function showAsHtml(){
		return $this->date.' <a href="'.$this->url.'">'.$this->content.'</a> '.$this->author;
	}

}
?>