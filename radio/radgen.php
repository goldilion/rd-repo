<?php
// migration to cache table
include("includes/db_connection.php");
set_time_limit(550);
header('Content-Type: text/json; charset=utf-8');

        $for = $_GET['for'];
        $laman = $_GET['laman'];

	if(isset($for)) {
		$queryCat = "SELECT category_name, `key` FROM rtbl_category WHERE `key` = '".$for."'";
	} else {
                echo '';
        }
	
	$resCat = mysql_query($queryCat);
	
	while ($data = mysql_fetch_array($resCat, MYSQL_ASSOC)) {
		$query = "
		SELECT 
			id, 
			radio_name, 
			REPLACE(radio_image, 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'https://dl.dropboxusercontent.com/u/19699721/BP/img/radio/logos/1.png') AS radio_image, 
			SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url
	    FROM rtbl_radio_new 
		LEFT JOIN rtbl_category ON rtbl_radio_new.category_id = rtbl_category.cid
		AND rtbl_radio_new.radio_url != '#STATUS: 400 '
		WHERE rtbl_category.key = '".$data['key']."'
		AND (rtbl_radio_new.radio_url != '')
			UNION
		SELECT
		    cid + 50000 AS id,
		    channel_name_new,
		    REPLACE(channel_img, 'http://online-television.net/uploads/uploads/', 'https://dl.dropboxusercontent.com/u/54692075/radio/') AS channel_img,
			channel_source
		FROM tbl_channels_radio_grab2
		WHERE channel_location LIKE '%".$data['category_name']."%'
		ORDER BY radio_name
		";


		$resouter = mysql_query($query);
	     //print_r($query);
	    $set = array();
	     
	    $total_records = mysql_num_rows($resouter);
	    if($total_records >= 1){
	     
	      while ($link = mysql_fetch_array($resouter, MYSQL_ASSOC)){
	      	   $datax['id'] = $link['id'];
		   $datax['radio_name'] = $link['radio_name'];
		   $datax['radio_image'] = $link['radio_image'];
		   $datax['radio_url'] = $link['radio_url'];
		   
	        //$set['Radio App'][] = $link[radio_name];
	        $set['Radio App'][] = $datax;
	      }
	    }
	     
	    #print_r($set); 
	    $val= str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE)); echo $val;

	    
	}

?>