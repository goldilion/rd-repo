<?php include("includes/db_connection.php");
header('Content-Type: text/json; charset=utf-8');

        if(isset($_GET['cachekey']))
	{
		// to show cache data	
		$cachekey = $_GET['cachekey']; 	

		$query = "
		SELECT cache_content
                FROM rtbl_cache 
		WHERE cache_id = '".$cachekey."'
		";
	} else if(isset($_GET['cacheid']))
	{
		// to show cache data	
		$cacheid = $_GET['cacheid']; 	

		$query = "
		SELECT cache_content
                FROM rtbl_cache 
		WHERE cache_cat_id = '".$cacheid."'
		";
	} else {
		echo '';
	}
	
	$resouter = mysql_query($query);
     
    $total_records = mysql_num_rows($resouter);
    if($total_records >= 1){
        $queryResult = mysql_fetch_assoc($resouter);
        $content = $queryResult['cache_content'];
        //echo htmlspecialchars_decode($content);
        echo $content;
    }

?>