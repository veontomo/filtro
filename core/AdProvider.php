<?php
/**
* interface for url pages containing advertisments. 
* Examples:
* <ol><li>http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/ </li>
* <li>http://www.subito.it/annunci-lazio/vendita/giardino-fai-da-te/ </li>
* <li>http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/ </li></ol>
* The url pages are usually sort of 'entry point' for the whole bunch of pages
* ordered chronologically. 
* <ul><li>For "Subito" site, the set of the pages have the following format:</li> 
* <ul><li>http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/?th=1&o=2</li>
* <li>http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/?th=1&o=3</li>
* <li>http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/?th=1&o=4</li></ul>
* <li>The same thing for "Portaportese":</li>
* <ul><li>http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag1</li>
* <li>http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/m-usC72&pag2</li></ul>
* </ul>
* @author A.Shcherbakov
* @author veontomo@gmail.com
* @version 0.0.2, 09 Oct 2013 22:22
*
*/
interface AdProvider{
	/**
	* gets the url for the whole bunch of the pages, e.g.: http://www.subito.it/annunci-lazio/vendita/giardino-fai-da-te/
	* @return String
	*/
	public function url();

	/**
	* gets the url pattern for the pages, e.g.: for "Subito" it is sort of "?th=1&o=1", 
	* for "Portaportese" - "m-usC72&pag2"
	* @return String
	*/
	public function urlPattern();

	/**
	* produces the content of the page $url
	* @param String $url url of the page
	* @return String
	*/
	public function retrievePage($url);
}

?>