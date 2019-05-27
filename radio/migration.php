<?php
// migration to cache table
//include("includes/db_connection.php");
error_reporting(1);
set_time_limit(550);

	$laman = $_GET['laman'];
	$queryCat = "
	SELECT category_name, `key` FROM rtbl_category limit ".$laman.", 10
	";

	$resCat = mysql_query($queryCat);

	while ($data = mysql_fetch_array($resCat, MYSQL_ASSOC)) {

		$query = "
		SELECT id, category_id, radio_name, REPLACE(radio_image, 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'https://dl.dropboxusercontent.com/u/19699721/BP/img/radio/logos/0.png') AS radio_image, SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url
	            FROM rtbl_radio_new 
		LEFT JOIN rtbl_category ON rtbl_radio_new.category_id = rtbl_category.cid
		AND rtbl_radio_new.radio_url != '#STATUS: 400 '
		WHERE rtbl_category.key = '".$data['key']."'
		AND (rtbl_radio_new.radio_url != '')
		ORDER BY rtbl_radio_new.radio_name
		";


		$resouter = mysql_query($query);
	     //print_r($query);
	    $set = array();
	     
	    $total_records = mysql_num_rows($resouter);
	    if($total_records >= 1){
	     
	      while ($link = mysql_fetch_array($resouter, MYSQL_ASSOC)){
		   
	        $set['Radio App'][] = $link;
	      }
	    }
	     
	     //echo 
	    $val= str_replace('\\/', '/', json_encode($set));

	    $queryInsert = "
	    INSERT INTO rtbl_cache (cache_id, cache_country, cache_content, cache_last_updated) VALUES ('".$data['key']."', '".$data['category_name']."', '".$val."', NOW());
	    ";

	    //echo $queryInsert;
	    mysql_query($queryInsert);
	    echo "<br />";
	    echo $data['category_name']." done...";
	}

?>