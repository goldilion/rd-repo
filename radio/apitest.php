<?php include("includes/db_connection.php");
header('Content-Type: text/json; charset=utf-8');

if(isset($_GET['cat_id']))
	{
		// to show channel list per category
		$cat_id = $_GET['cat_id'];	
		
		$query = "
		SELECT r.id, r.category_id, r.radio_name, IF(radio_image = 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'http://apitv.wakootv.com/radio/img/1.gif', CONCAT('http://apitv.wakootv.com/radio/img/', r.note, '.gif')) AS radio_image, 
			SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url 
		FROM rtbl_radio_new r
		JOIN rtbl_category c ON c.cid = r.category_id
		WHERE c.cid='$cat_id'
		AND (r.radio_url != '#STATUS: 400 ' OR r.radio_url IS NULL)
		AND r.is_deleted != 1
                   UNION
		SELECT
		    cid + 50000 AS id,
                    cat_id AS category_id,
		    channel_name_new,
		    CONCAT('http://apitv.wakootv.com/radio/img/', SUBSTRING_INDEX(channel_img,  '/', -1)) AS channel_img,
			channel_source
		FROM tbl_channels_radio_grab2
		WHERE cat_id = '$cat_id'
		ORDER BY radio_name
		";
	} else if(isset($_GET['rcat_id']))
	{
		// to show channel list per category
		$rcat_id = $_GET['rcat_id'];	

		$query = "
		SELECT r.id, r.category_id, r.radio_name, IF(radio_image = 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'http://apitv.wakootv.com/radio/img/1.gif', CONCAT('http://apitv.wakootv.com/radio/img/', rtbl_radio_new.note, '.gif')) AS radio_image, 
			SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url 
		FROM rtbl_radio_new r
		JOIN rtbl_category c ON c.cid = r.category_id
		WHERE c.cid='$rcat_id'
		AND (r.radio_url != '#STATUS: 400 ' OR r.radio_url IS NULL)
		AND r.is_deleted != 1
                    UNION
		SELECT
		    cid + 50000 AS id,
                    cat_id AS category_id,
		    channel_name_new,
		    CONCAT('http://apitv.wakootv.com/radio/img/', SUBSTRING_INDEX(channel_img,  '/', -1)) AS channel_img,
			channel_source
		FROM tbl_channels_radio_grab2
		WHERE cat_id = '$rcat_id'
		ORDER BY radio_name
		";
	}
	else if(isset($_GET['catkey']))
	{
		// to show the latest channels added
		//$limit = $_GET['latest'];	 	
		$catkey = $_GET['catkey']; 	

		$query = "
		SELECT id, category_id, radio_name, IF(radio_image = 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'http://apitv.wakootv.com/radio/img/1.gif', CONCAT('http://apitv.wakootv.com/radio/img/', rtbl_radio_new.note, '.gif')) AS radio_image, 
			SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url
                FROM rtbl_radio_new 
		LEFT JOIN rtbl_category ON rtbl_radio_new.category_id = rtbl_category.cid
		AND rtbl_radio_new.radio_url != '#STATUS: 400 '
                WHERE rtbl_category.key = '".$catkey."'
		AND rtbl_radio_new.radio_url != ''
                AND rtbl_radio_new.is_deleted != 1
		   UNION
		SELECT
		    cid + 50000 AS id,
                    cat_id AS category_id,
		    channel_name_new,
		    CONCAT('http://apitv.wakootv.com/radio/img/', SUBSTRING_INDEX(channel_img,  '/', -1)) AS channel_img,
			channel_source
		FROM tbl_channels_radio_grab2
		WHERE cat_id = (SELECT cid FROM rtbl_category WHERE `key` = '".$catkey."')
                AND tbl_channels_radio_grab2.is_deleted != 1
		ORDER BY radio_name
		";
	}
	else if(isset($_GET['ckey']) && $_GET['fr'] == 'featured')
	{
		// to show the latest channels added
		//$limit = $_GET['latest'];	 	
		$catkey = $_GET['ckey']; 	

		$query = "
		SELECT id, category_id, radio_name, IF(radio_image = 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', 'http://apitv.wakootv.com/radio/img/1.gif', CONCAT('http://apitv.wakootv.com/radio/img/', rtbl_radio_new.note, '.gif')) AS radio_image, 
			SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url
                FROM rtbl_radio_new 
		LEFT JOIN rtbl_category ON rtbl_radio_new.category_id = rtbl_category.cid
		AND rtbl_radio_new.radio_url != '#STATUS: 400 '
                WHERE rtbl_category.key = '".$catkey."'
		AND rtbl_radio_new.radio_url != ''
		AND rtbl_radio_new.is_deleted != 1
                   UNION
		SELECT
		    cid + 50000 AS id,
                    cat_id AS category_id,
		    channel_name_new,
		    CONCAT('http://apitv.wakootv.com/radio/img/', SUBSTRING_INDEX(channel_img,  '/', -1)) AS channel_img,
			channel_source
		FROM tbl_channels_radio_grab2
                AND is_deleted != 1
		WHERE cat_id = (SELECT cid FROM rtbl_category WHERE `key` = '".$catkey."')
		ORDER BY RAND() LIMIT 0, 5
		";
	}
        else if(isset($_GET['gkey']))
	{
		$gkey = $_GET['gkey'];	 	

		$query = "
		SELECT c.cid, c.category_name, c.category_image
		FROM rtbl_category c
		JOIN rtbl_group_category gc ON c.cid = gc.cid
		JOIN rtbl_group g ON g.gid = gc.gid
		WHERE g.key = '$gkey'
		ORDER BY c.category_name 
		";
	}
	else if(isset($_GET['cachegkey']))
	{
		$gkey = $_GET['cachegkey'];	 	

		$query = "
		SELECT c.cid as cid, c.category_name, c.category_image
		FROM rtbl_category c
		JOIN rtbl_group_category gc ON c.cid = gc.cid
		JOIN rtbl_group g ON g.gid = gc.gid
		WHERE g.key = '$gkey'
		ORDER BY c.category_name 
		";
	}
	else if(isset($_GET['cachekey']))
	{
		// to show cache data	
		$cachekey = $_GET['cachekey']; 	

		$query = "
		SELECT cache_content
                FROM rtbl_cache 
		WHERE cache_id = '".$cachekey."'
		";
	}
	else if(isset($_GET['apikey']))
	{	
		$apikey = $_GET['apikey'];
		if ($apikey == '3a87a9c212295bbed9ab89b1ac8fcfe1') {
			$query = "SELECT cid, category_name, category_image FROM rtbl_category ORDER BY rtbl_category.category_name";
		} /*else {
 			$query = "SELECT cid, category_name, category_image FROM rtbl_category WHERE key = $apikey ORDER BY rtbl_category.category_name";
 		}*/
	} else {
		echo '';
	}
	
	$resouter = mysql_query($query);
     //print_r($query);
    $set = array();
     
    $total_records = mysql_num_rows($resouter);
    if($total_records >= 1){
     
      while ($link = mysql_fetch_array($resouter, MYSQL_ASSOC)){
	   
        $set['Radio App'][] = $link;
        
      }
    }
     //print_r($set);
     echo $val= str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
	 	 
	 
?>