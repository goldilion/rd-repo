<?php
// migration to cache table
include("includes/db_connection.php");
//set_time_limit(550);
//header('Content-Type: text/json; charset=utf-8');
//header('Content-Type: text/html; charset=utf-8');
error_reporting(1);

	if(isset($_GET['for'])) {
		$for = $_GET['for'];
		$queryCat = "SELECT category_name, `key` FROM `rtbl_category` WHERE `key` = '".$for."';";
	} elseif(isset($_GET['laman'])) {
		$laman = $_GET['laman'];
		$queryCat = "SELECT category_name, `key` FROM `rtbl_category` limit ".$laman.", 10;"; // laman=10, 20, 30, 40, 50
	} elseif(isset($_GET['all'])) { 
		$queryCat = "SELECT category_name, `key` FROM `rtbl_category`;";
	} else {
        echo '';
    }
	
	//$results = $pdo->query('SELECT category_name, `key` FROM rtbl_category WHERE `key` = ".$for."')->fetchAll(PDO::FETCH_ASSOC);
	
	$stmt = $connect->query($queryCat)->fetchAll();
  	//$row = $stmt->fetchObject();
  	//$countData = $stmt->rowCount();

	foreach ($stmt as $rower) {
	    //echo $row['category_name'];

	    $query = "
	    	SELECT
				rg.cat_id AS cid,
				c.category_name AS category_name,
				'' AS category_image,
				rg.cid + 50000 AS id,
				rg.cat_id AS category_id,
				rg.channel_name AS radio_name,
				CONCAT('', SUBSTRING_INDEX(rg.channel_img,  '/', -1)) AS radio_image,
			    rg.channel_source AS radio_url
			FROM tbl_channels_radio_grab3 rg
			JOIN tbl_category_grab3 c ON c.cid = rg.cat_id
			WHERE rg.cat_id = (SELECT cid FROM tbl_category_grab3 WHERE `key` = '".$rower['key']."')
			AND rg.is_deleted != 1
				UNION
			SELECT 
				r.category_id AS cid,
				c.category_name AS category_name,
				'' AS category_image,
				r.id,
				r.category_id AS category_id,
				r.radio_name, 
				IF(radio_image = 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', '1.gif', CONCAT('', r.note, '.gif')) AS radio_image,
				SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url
			FROM rtbl_radio_new r
			JOIN rtbl_category c ON c.cid = r.category_id
			WHERE c.key = '".$rower['key']."'
			AND (r.radio_url != '#STATUS: 400 ' OR r.radio_url IS NULL)
			AND r.is_deleted != 1
			ORDER BY radio_name
			";

			$set=array();
			$stmt = $connect->query($query);
		  	$row = $stmt->fetchObject();
		  	$countData = $stmt->rowCount();
		  	if($stmt->rowCount() > 0) {
		        while ($arr = $stmt->fetch(PDO::FETCH_ASSOC)) {
		          	$set['Json'][] = $arr;
		        }
		    } 

		    $add_key = incrementalHash();

    		$val= $add_key.base64_encode(str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE)));
		    //$val= str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));

    		$filename_catname = $rower['category_name'];
		    $filename = $rower['key'];
            //$strLog = $strQuery.';';
            //$log_file = 'json/'.$filename.'-r-'.strtolower($rower['domain']).'.json';
            $log_file = 'json/'.$filename.'.json';
            $strMessage = $val;
			//echo $log_file;
            file_put_contents($log_file, $strMessage);
            //echo $val;
		    echo $rower['category_name']." done built...";
		    echo "<br />";
	}

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

 /*

// grab2

           UNION
SELECT
	rg.cat_id AS cid,
	c.category_name AS category_name,
	'' AS category_image,
    rg.cid + 20000 AS id,
	rg.cat_id AS category_id,
    rg.channel_name_new AS radio_name,
	CONCAT('', SUBSTRING_INDEX(rg.channel_img,  '/', -1)) AS radio_image,
    rg.channel_source AS radio_url
FROM tbl_channels_radio_grab2 rg
JOIN rtbl_category c ON c.cid = rg.cat_id
WHERE rg.cat_id = (SELECT cid FROM rtbl_category WHERE `key` = '".$rower['key']."')
        AND rg.is_deleted != 1

// grab2

SELECT 
	id, 
	radio_name, 
	REPLACE(radio_image, 'http://d1i6vahw24eb07.cloudfront.net/s0s.gif', '1.png') AS radio_image, 
	SUBSTRING_INDEX(radio_url, ' ', 1) AS radio_url
FROM rtbl_radio_new 
LEFT JOIN rtbl_category ON rtbl_radio_new.category_id = rtbl_category.cid
AND rtbl_radio_new.radio_url != '#STATUS: 400 '
WHERE rtbl_category.key = '".$rower['key']."'
AND (rtbl_radio_new.radio_url != '')
	UNION
SELECT
    cid + 50000 AS id,
    channel_name_new,
    REPLACE(channel_img, 'http://online-television.net/uploads/uploads/', '') AS channel_img,
	channel_source
FROM tbl_channels_radio_grab2
WHERE channel_location LIKE '%".$rower['category_name']."%'
ORDER BY radio_name

 */

?>