<?php
// test file to try with xpath
$content = <<<START
<div>
	<div class="external">
		<div>
			<div class="block1">
				block 1_1
			</div>
			<div class="block1">
				block 1_2
			</div>
			<div class="block2">
				block 1_2
			</div>
		</div>
	</div>
	<div class="external">
		<div>
			<div class="block1">
				block 2_1
			</div>
			<div class="block2">
				block 2_2
			</div>
		</div>
	</div>
	<div class="external">
		<div class="block1">
			block 3_0
		</div>
		<div>
			<div class="block1">
				block 3_1
			</div>
			<div class="block2">
				block 3_2
			</div>
		</div>
	</div>
</div>
START;

$doc = new DOMDocument();
$doc->loadHTML($content);
$xpath = new DOMXpath($doc);
$ads = $xpath->query('//*/div[@class="external"]');
echo '<ul>';
foreach ($ads as $ad) {
	echo '<li>';
	echo $ad->nodeValue . '<br />';
	$nodes1 = $xpath->query('*[@class="block1"]', $ad);
	echo 'node contains '.$nodes1->length. ' items';
	echo '<ul>';
	foreach ($nodes1 as $node1) {
		echo '<li>';
		echo $node1->nodeValue;
		echo '</li>';
	}
	echo '</ul>';
	echo '</li>';
}
echo '</ul>';
?>