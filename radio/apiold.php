<?php include("includes/db_connection.php");

if(isset($_GET['cat_id']))
	{
		// to show channel list per category
		$cat_id = $_GET['cat_id'];	
		/*
		if(isset($_GET['catkey'])) {
			$catkey = $_GET['catkey'];
			$where "WHERE c.key='".$catkey."'";
		} else if(isset($_GET['cat_id'])) {
			$cat_id = $_GET['cat_id'];
			$where "WHERE c.cid='".$cat_id."'";
		}
		*/
		$query = "
		SELECT r.id, r.category_id, r.radio_name, r.radio_image, r.radio_url 
		FROM rtbl_radio r
		JOIN rtbl_category c ON c.cid = r.category_id
		WHERE c.cid='$cat_id'
		AND r.is_deleted IS NULL OR r.is_deleted = '0'
		ORDER BY r.radio_name
		";
	}
	else if(isset($_GET['catkey']))
	{
		// to show the latest channels added
		$limit = $_GET['latest'];	 	
		$catkey = $_GET['catkey']; 	

		$query = "
		SELECT id, category_id, radio_name, radio_image, radio_url
                FROM rtbl_radio 
		LEFT JOIN rtbl_category ON rtbl_radio.category_id = rtbl_category.cid
		AND rtbl_radio.is_deleted IS NULL OR rtbl_radio.is_deleted = '0' 
		WHERE rtbl_category.key = '".$catkey."'
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
     
     echo $val= str_replace('\\/', '/', json_encode($set));
	 	 
	 
?>