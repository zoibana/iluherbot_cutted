<?php

function tracer($limit = 100) {
	$trace = debug_backtrace();
	$lines = array();
	for ($i = 1; $i <= $limit; $i++) {
		if (isset($trace[$i]['file'])) {
			$lines[] = "$i : " . $trace[$i]['file'] . ' line ' . $trace[$i]['line'];
		}
	}

	return $lines;
}

function pr($var) {
	echo '<pre>';
	if (is_bool($var)) {
		var_dump($var);
	} else {
		print_r($var);
	}
	echo '</pre>';
}