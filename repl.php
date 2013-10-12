<?php
$pattern = array('/PLACEHOLDER1/', '/PLACEHOLDER2/');
$repl = array("aaa", "bbb");

$page = preg_replace($pattern, $repl, '?th=PLACEHOLDER1&o=PLACEHOLDER2');
echo $page;

?>