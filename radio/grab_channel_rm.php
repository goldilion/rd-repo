<?php
include("includes/db_connection.php");
//ini_set('display_errors', 1);
//$connect = new PDO('mysql:host=localhost;dbname=apitvserver;charset=utf8mb4', 'root', '');

$stmt = $connect->query('SELECT cid FROM tbl_category_grab3 WHERE is_grabbed IS NULL ORDER BY RAND() LIMIT 0, 1');
$results = $stmt->fetch(PDO::FETCH_ASSOC);
$countData = $stmt->rowCount();

if($stmt->rowCount() > 0) {
  $url = "http://www.radiomoob.com/radiomoob/api.php?cat_id=".$results['cid'];
  $server_url = "http://www.radiomoob.com/radiomoob/upload/";

  $html = file_get_contents($url);

  echo '<table>';
  if(isset($html)) {
  $getyt =json_decode($html, true);
    foreach ($getyt['Json'] as $cid) {

    	$img = $server_url.$cid["radio_image"];
    	$img_name = substr(md5($cid['id']), 0, 11).'.jpg';
      copy($img, 'img/logo3/'.$img_name);
      //echo '<tr><td>'.$cid['id'].'</td><td>'.$cid['category_id'].'</td><td>'.$cid['radio_name'].'</td><td>'.$img_name.'</td><td>'.$cid['radio_url'].'</td></tr>';

       // INSERT DATA
      //$stmt = $connect->prepare("INSERT INTO tbl_channels_radio_grab3 (cid, cat_id, channel_name, channel_img, channel_source) VALUES (?, ?, ?, ?, ?); ");
      //$stmt->execute(array($cid['id'], $cid['category_id'], $cid['radio_name'], $img_name, $cid['radio_url']));

      $sql_insert = "INSERT INTO tbl_channels_radio_grab3 (cid, cat_id, channel_name, channel_img, channel_source, is_deleted)
      VALUES ('".$cid['id']."', '".$cid['category_id']."', '".$cid['radio_name']."', '".$img_name."', '".$cid['radio_url']."', '0');";
      #echo $sql_insert;
      #echo '<br />';

      $connect->exec($sql_insert);

   }
   	echo $results['cid'];
    // UPDATE DATA
      $stmt = $connect->prepare("UPDATE tbl_category_grab3 SET is_grabbed='1' WHERE cid=?");
      $stmt->execute(array($results['cid']));
  }

  echo '</table>';
} else {
  mail("templatemega@gmail.com", "Grab3 Finish", "Stop it.");
}

?>