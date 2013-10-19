<?php
require_once('FirePHPCore/fb.php');
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/main.css">	
</head>
<body>
<?php
if(isset($_POST['submit'])){
	fb('POST[submit] is set', FirePHP::INFO);
	$category = htmlspecialchars($_POST['category']);
	$keywords = htmlspecialchars($_POST['keywords']);

	$inputData = array('category' => $category, 'keywords' => $keywords);
	require_once 'php/processform.php';

	$outputData = retrieveAds(json_encode($inputData));
	// fb('retrieveAds output: '.$outputData, FirePHP::INFO);
}
?>
	<div id="header">
		<p>
			filtro subito
		</p>
	</div>
	
	<div id="main">
		<div id="sidebar">
			<div class="date">
				<?php
					echo date("j M H:i:s", time());
				?>
			</div>
		</div>
		<div id="central">
			<h1>
				Filtra annunci
			</h1>
			<form method="post">
				<label for="category">Categoria:</label>
				<select name="category" id="category">
					<option value=''>
						scegli categoria
					</option>
					<option value='lavoro'>
						offerte di lavoro
					</option>
					<option value='arredamento'>
						arredamento
					</option>
					<option value='veicoli'>
						auto e moto
					</option>
				</select>
				<br />

				<label for="keywords">Parole chiavi:</label>
				<input type="text" name="keywords" id="keywords" placeholder="inserisci le parole chiavi"><br />
				
				<label for="timeMax">Cominciando da:</label>
				<input id="timeMax" name="timeMin" placeholder="ora">
				
				<label for="timeMin">Non pi&ugrave; vecchi di:</label>
				<input id="etimeMin" name="timeMin" value="un'ora">
				
				<input type="submit" value="Controlla" name="submit" id="submit">
			</form>
			<div id="all-ads"> 
			<?php
			if(isset($outputData) && $outputData){
				echo '<ol>';
				foreach ($outputData as $ad) {
					echo '<li>' . $ad->content . '</li>';
				}
				echo '</ol>';
			}
			?>

			</div>
		</div>


	</div> <!-- end of main -->

</body>


</html> 