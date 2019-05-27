<?php
// migration to cache table
include("includes/db_connection.php");
set_time_limit(550);
//header('Content-Type: text/json; charset=utf-8');

// https://dl.dropboxusercontent.com/u/54692075/radio/

	$for = $_GET['for'];
        $laman = $_GET['laman'];

	if(isset($for)) {
		$queryCat = "SELECT category_name, `key` FROM rtbl_category WHERE `key` = '".$for."'";
	} elseif(isset($laman)) {
		$queryCat = "SELECT category_name, `key` FROM rtbl_category limit ".$laman.", 10";
	} else {
                echo '';
        }
	
	$resCat = mysql_query($queryCat);

	while ($data = mysql_fetch_array($resCat, MYSQL_ASSOC)) {

		$query = "
		
		SELECT 
			id, 
			radio_name, 
			IF(radio_image = 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'http://apitv.wakootv.com/radio/img/1.gif', CONCAT('http://apitv.wakootv.com/radio/img/', rtbl_radio_new.note, '.gif')) AS radio_image, 
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
                    CONCAT('http://apitv.wakootv.com/radio/img/', SUBSTRING_INDEX(channel_img,  '/', -1)) AS channel_img,
			channel_source
		FROM tbl_channels_radio_grab2
		WHERE channel_location LIKE '".$data['category_name']."%'
		ORDER BY radio_name
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
	    $val= str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));

	    /*$queryInsert = "
	    INSERT INTO rtbl_cache (cache_id, cache_country, cache_content, cache_last_updated) VALUES ('".$data['key']."', '".$data['category_name']."', '".$val."', NOW());
	    ";
*/
	    $queryUpdate = "
	    UPDATE rtbl_cache SET cache_content = '".$val."', cache_last_updated = NOW() WHERE cache_id = '".$data['key']."';
	    ";

	    #echo $queryUpdate;
	    $res = mysql_query($queryUpdate);
	    //$jml = mysql_num_rows($res);
	    //echo $jml;
	    //if($jml > 0) {
	    	echo "<br />";
	    	echo $data['category_name']." done update...";
	    //}
	    

	    //if($jml < 1){
	    	//mysql_query($queryInsert);
	    	//echo "<br />";
	    	//echo $data['category_name']." done insert...";
	    //}

	}

?>