<?php

function incrementalHash($len = 1){
	  $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	  $base = strlen($charset);
	  $result = '';

	  $now = explode(' ', microtime())[1];
	  while ($now >= $base){
	    $i = $now % $base;
	    $result = $charset[$i] . $result;
	    $now /= $base;
	  }
	  return substr($result, 4, 1);
	}

	echo incrementalHash();
?>