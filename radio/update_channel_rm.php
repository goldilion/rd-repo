<?php
/*
http://radiomoob.com/radioworld/api/get_category_index?api_key=cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV&pak=com.allradio.radiofm

API key cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV

    @GET("api/get_category_index?api_key=cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV&pak=com.allradio.radiofm")
    @Headers({"Cache-Control: max-age=0", "Data-Agent: Your Radio App"})
    Call<CallbackCategory> getAllCategories();

    @GET("api/get_category_detail?api_key=cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV&pak=com.allradio.radiofm")
    @Headers({"Cache-Control: max-age=0", "Data-Agent: Your Radio App"})
    Call<CallbackCategoryDetails> getCategoryDetailsByPage(@Query("id") int i, @Query("page") int i2, @Query("count") int i3, @Query("api_key") String str);

    @GET("api/get_category_detail_gps?api_key=cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV&pak=com.allradio.radiofm")
    @Headers({"Cache-Control: max-age=0", "Data-Agent: Your Radio App"})
    Call<CallbackRadio> getCountryRadio(@Query("id") String str, @Query("page") int i, @Query("api_key") String str2);

    @GET("api/get_privacy_policy")
    @Headers({"Cache-Control: max-age=0", "Data-Agent: Your Radio App"})
    Call<Settings> getPrivacyPolicy();

    @GET("api/get_recent_radio?api_key=cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV&pak=com.allradio.radiofm")
    @Headers({"Cache-Control: max-age=0", "Data-Agent: Your Radio App"})
    Call<CallbackRadio> getRecentRadio(@Query("page") int i, @Query("count") int i2);

    @GET("api/get_search_results?api_key=cda11RgBjsZdDEA8pq5lykwirzT7m1eSbOuo6vJWx0h92tFnIV&pak=com.allradio.radiofm")
    @Headers({"Cache-Control: max-age=0", "Data-Agent: Your Radio App"})
    Call<CallbackRadio> getSearchPosts(@Query("search") String str, @Query("count") int i);

    
*/

include("includes/db_connection.php");
//ini_set('display_errors', 1);
//$connect = new PDO('mysql:host=localhost;dbname=apitvserver;charset=utf8mb4', 'root', '');
$days = 90;
//$stmt = $connect->query('SELECT cid FROM tbl_category_grab3 WHERE is_grabbed IS NULL ORDER BY RAND() LIMIT 0, 1');
$stmt = $connect->query("SELECT cid FROM tbl_category_grab3 WHERE datediff(CURDATE(), last_updated) > $days ORDER BY RAND() LIMIT 0, 1");

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
      // check channels
      $ifExist = checkChannel($connect, $cid['radio_url'], $cid["radio_image"]);

      $img = $server_url.$cid["radio_image"];
      $img_name = substr(md5($cid['id']), 0, 11).'.jpg';

      if($ifExist == '0') {
        // insert data
        copy($img, 'img/logo3/'.$img_name);

        $sql_insert = "INSERT INTO tbl_channels_radio_grab3 (cid, cat_id, channel_name, channel_img, channel_source, is_deleted)
        VALUES ('".$cid['id']."', '".$cid['category_id']."', '".$cid['radio_name']."', '".$img_name."', '".$cid['radio_url']."', '0');";
        $connect->exec($sql_insert);
        
        $strLog = $sql_insert;        
        $filename = 'log/insert_radiomoob_';
        AppendSplit($filename, $strLog, false);

      } else {
        // update data
        $sql_update = "UPDATE tbl_channels_radio_grab3 SET channel_source = '".$cid['radio_url']."' WHERE channel_img = '".$img_name."'";
        $connect->exec($sql_update);

        $strLog = $sql_update;
        $filename = 'log/update_radiomoob_';
        AppendSplit($filename, $strLog, false);
      }

      //echo '<tr><td>'.$cid['id'].'</td><td>'.$cid['category_id'].'</td><td>'.$cid['radio_name'].'</td><td>'.$img_name.'</td><td>'.$cid['radio_url'].'</td></tr>';

       // INSERT DATA
      //$stmt = $connect->prepare("INSERT INTO tbl_channels_radio_grab3 (cid, cat_id, channel_name, channel_img, channel_source) VALUES (?, ?, ?, ?, ?); ");
      //$stmt->execute(array($cid['id'], $cid['category_id'], $cid['radio_name'], $img_name, $cid['radio_url']));
      
      #echo $sql_insert;
      #echo '<br />';



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

function checkChannel($connect, $channel_source, $channel_img) {
  $queryCheck = "SELECT cid FROM tbl_channels_radio_grab3 WHERE 
  channel_source = '".$channel_source."' AND channel_img = '".$channel_img."'; ";
  $stmt = $connect->query($queryCheck); 
  $row = $stmt->fetchObject();
  $countData = $stmt->rowCount();

  if ($countData > 0) {
      return $row->cid;
    } else {
      return 0;
    }
}

function AppendSplit($filename, $strLog, $bypass_time = false) {
    if ($bypass_time) {
        $log_file = $filename.".log";
        $strMessage = $strLog. "\n";
    }
    else {
        $log_file = $filename.'_'.date("Y_m_d").".log";
        $strMessage = $strLog. "\n";
    }
    
//      if ($conf['log'])
        file_put_contents($log_file, $strMessage, FILE_APPEND);
}

?>