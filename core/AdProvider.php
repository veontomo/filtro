<?php
interface AdProvider{
	public $url;
	public $urlPattern;

	public function retrievePage($url){}
}

?>