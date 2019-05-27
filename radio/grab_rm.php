<?php
include("includes/db_connection.php");

#$connect = new PDO('mysql:host=localhost;dbname=apitvserver;charset=utf8mb4', 'root', '');

#$stmt = $connect->query('SELECT channel_username FROM videoyt WHERE channel_id IS NULL ORDER BY RAND() LIMIT 0, 1');
#$results = $stmt->fetch(PDO::FETCH_ASSOC);

$html = file_get_contents("http://www.radiomoob.com/radiomoob/api.php");
echo '<table>';
if(isset($html)) {
$getyt =json_decode($html, true);
  foreach ($getyt['Json'] as $cid) {
     $id = $cid['cid'];
     echo '<tr><td>'.$cid['cid'].'</td><td>'.$cid['category_name'].'</td><td>'.$cid['category_image'].'</td><td>'.$cid['category_image2'].'</td><td>'.$cid['category_continent'].'</td></tr>';
     //echo $results['channel_username'];
     // INSERT DATA
    $stmt = $connect->prepare("INSERT INTO tbl_category_grab3 (cid, category_name, category_image, category_image2, category_continent) VALUES (?, ?, ?, ?, ?); ");
    $stmt->execute(array($cid['cid'], $cid['category_name'], $cid['category_image'], $cid['category_image2'], $cid['category_continent']));

 }
}

echo '</table>';

?>