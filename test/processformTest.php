<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'processform.php';
class ProcessFormTest extends PHPUnit_Framework_TestCase
{
	public function testExists(){
		$this->assertTrue(function_exists('retrieveAds'));
	}


	public function testIncompleteInput(){
		$input = array('keywords' => ''); // key 'category' is not present
		$output = retrieveAds($input);
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));


		$input = array('keywords' => '', 'category' => ''); // key 'category' is present, but its value is empty
		$output = retrieveAds($input);
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));

		$input = array('keywords' => '', 'category' => NULL); // key 'category' is present, but its value is NULL
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));


	}


	public function testWrongDates(){
		$input = array('category' => 'lavoro', 'keywords' => 'Domestico', 'timeMin' => 1223, 'timeMax' => 21);
		$output = retrieveAds($input);
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('success', $output));
		$this->assertFalse($output['success']);
		$this->assertTrue(array_key_exists('message', $output));
	}

/**
* @group current
*/
	public function testCategoriesToUrl(){
		$output = categoryToUrls('non existing category');
		$this->assertTrue(is_array($output));
		$this->assertTrue(empty($output));

		$output = categoryToUrls('lavoro');
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('portaportese', $output));
		$this->assertTrue(array_key_exists('subito', $output));
		$this->assertContains('http://www.portaportese.it/rubriche/Lavoro/Lavoro_qualificato/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Lavoro/Lavoro_generico/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Lavoro/Scuola_e_lezioni_private/',
			$output['portaportese']);
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/offerte-lavoro/',
			$output['subito']);

		$output = categoryToUrls('arredamento');
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('portaportese', $output));
		$this->assertTrue(array_key_exists('subito', $output));
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/arredamento-casalinghi/',
			$output['subito']);
		$this->assertContains('http://www.portaportese.it/rubriche/Casa/Arredamento_Mobili/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Casa/Antiquariato_Quadri/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Regali/',
			$output['portaportese']);

		$output = categoryToUrls('veicoli');
		$this->assertTrue(is_array($output));
		$this->assertTrue(array_key_exists('portaportese', $output));
		$this->assertTrue(array_key_exists('subito', $output));
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/auto/',
			$output['subito']);
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/accessori-auto/',
			$output['subito']);
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/moto-e-scooter/',
			$output['subito']);
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/accessori-moto/',
			$output['subito']);
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/caravan-e-camper/',
			$output['subito']);
		$this->assertContains('http://www.subito.it/annunci-lazio/vendita/altri-veicoli/',
			$output['subito']);
		

		
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Auto_italiane/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Auto_straniere/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Fuoristrada/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Auto_d_epoca_Speciali/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Grandi_veicoli_e_da_lavoro/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Moto/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Bici/',
			$output['portaportese']);
		$this->assertContains('http://www.portaportese.it/rubriche/Veicoli/Accessori_ricambi/',
			$output['portaportese']);

	}


	// public function testFirstTry(){
	// 	$input = array('category' => 'lavoro', 'keywords' => 'Domestico');
	// 	$output = retrieveAds($input);
	// 	$this->assertTrue(is_array($output));
	// 	$this->assertTrue(array_key_exists('success', $output));
	// 	$this->assertTrue($output['success']);
	// 	$this->assertTrue(array_key_exists('result', $output));
	// 	$this->assertTrue(is_array($output['result']));
	// 	$this->assertFalse(empty($output['result']));
	// }

	// public function testFunctionCalled(){
	// 	$input = array('category' => 'lavoro', 'keywords' => 'Domestico');
	// 	retrieveAds($input);
		

	// }



}