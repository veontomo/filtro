<?php
//require_once('FirePHPCore/fb.php');
// ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script type="text/javascript" src="js/jquery.simple-dtpicker.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link type="text/css" href="css/jquery.simple-dtpicker.css" rel="stylesheet" />
</head>
<body>
<?php
if(isset($_POST['submit'])){
	$timeMax = isset($_POST['timeMax']) ? strtotime($_POST['timeMax']) : time();
	$timeMin = isset($_POST['timeMin']) ? strtotime($_POST['timeMin']) : strtotime('-1 hour');
	$category = isset($_POST['category']) ? htmlspecialchars($_POST['category']) : NULL;
	$keywords = isset($_POST['keywords']) ? htmlspecialchars($_POST['keywords']) : "";

	$inputData = array(	'category' => $category, 'keywords' => $keywords, 
						'timeMax' =>  $timeMax,  'timeMin' =>  $timeMin);
	require_once 'php/processform.php';
	// fb('recieved data: '.json_encode($inputData), FirePHP::INFO);
	// fb('timeMax = '.date('Y M d H:i', $timeMax));
	// fb('timeMin = '.date('Y M d H:i', $timeMin));
	$outputData = retrieveAds($inputData);
	// fb('retrieveAds output: '.$outputData, FirePHP::INFO);
}
?>
	<div id="header">
		<p>
			pescannunci
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
				Pescannunci
			</h1>
			<?php
			$categ = array('scegli categoria' => '', 'offerte di lavoro' => 'lavoro', 
				'arredamento' => 'arredamento', 'auto e moto' => 'veicoli');
			?>
			<form method="post">
				<label for="category">Categoria:</label>
				<select name="category" id="category">
				<?php
				foreach ($categ as $key => $value) {
					$selected = (isset($_POST['category']) && ($value==$_POST['category'])) ? " selected" : "";
					echo "<option value=\"$value\"$selected>$key</option>";
				}
				?>
				</select>
				<br />

				<label for="keywords">Parole chiavi:</label>
				<?php
					$keywordsTag = isset($_POST['keywords']) ?  'value="'.htmlspecialchars($_POST['keywords']).'"' : 'placeholder="inserisci le parole chiavi"';
				?>
				<input type="text" name="keywords" id="keywords" <?php echo $keywordsTag;?>><br />
				
				<label for="timeMin">A:</label>
				<input type="text" id="timeMin" name="timeMin" placeholder="<?php echo date('Y-m-d H:i', strtotime('-1 hour'));?>">

				<label for="timeMax">Da:</label>
				<input type="text" id="timeMax" name="timeMax" placeholder="<?php echo date('Y-m-d H:i', time());?>">

				
				<input type="submit" value="Controlla" name="submit" id="submit">
			</form>
			<div id="all-ads">
			<?php
			if(isset($outputData) && $outputData){
				
				if($outputData['success']){
					$ads = $outputData['result'];
					echo count($ads) . ' annunci trovati<br />';
					echo '<ol>';
					foreach ($ads as $ad) {
						echo '<li>' . $ad->showAsHtml() . '</li>';
					}
					echo '</ol>';
				}
			}
			?>

			</div>
		</div>


	</div> <!-- end of main -->

</body>


</html>
