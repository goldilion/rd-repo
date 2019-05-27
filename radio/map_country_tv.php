<?php
include("includes/db_connection.php");

header('Content-Type: text/html; charset=utf-8');
//$connect = new PDO('mysql:host=localhost;dbname=apitvserver;charset=utf8mb4', 'root', '');
//error_reporting(0);
// SELECT CONCAT('insert into tbl_group (gid, group_name, `key`) values (NULL,"', category_name,'","',`key`,'");') FROM `rtbl_category` ORDER BY category_name;

$getGroup = $connect->query("SELECT gid AS GID, group_name AS GNAME FROM tbl_group");

		echo '
        <form method="get" action="">
        Group: <select name="group">
            ';
        			//echo '<option value="">Blank</option>';
            if($getGroup->rowCount() > 0) {
                while ($arr1 = $getGroup->fetch(PDO::FETCH_ASSOC)) {
                    $selected = $arr1["GID"] == $_GET["group"] ? 'selected' : '';
                    echo '<option value="'.$arr1["GID"].'" '.$selected.'>'.$arr1["GID"].' '.$arr1["GNAME"].'</option>';
                }
            } 

		echo '
        </select>
        <br />
        <input type="submit" name="submit" value="Check">
        </form>
        <br />
        <a href="map_country_tv.php">Back to Home</a>
        ';

if(isset($_GET['submit'])) {
	//$_POST['Countries'];
	getList($_GET['group'], $connect);

} elseif(isset($_POST['save']) && ($_POST['save'] == 'Save')) {
	
	saveList($_POST['item'], $_POST['group_id'], $connect);

} else {
	echo '';
}

function getList($group_id, $connect) {

		$getCategory = $connect->query("SELECT cid, category_name, `key` FROM tbl_category order by cid");

		//$getGroupCategory = $connect->prepare("SELECT gid, cids FROM tbl_group_categories WHERE gid = :gid");
		$getGroupCategory = $connect->prepare("SELECT gid, cids FROM tbl_group WHERE gid = :gid");
		$getGroupCategory->execute(array(
			':gid' => $group_id
			));

		$arr3 = $getGroupCategory->fetch();

		echo '<form method="POST" action="map_country_tv.php">';
		echo '<table border="1">';
        echo '<tr>';
        echo '<td><input type="hidden" name="group_id" value="'.$group_id.'"> <input type="hidden" name="save" value="Save">';
        echo '<input type="submit" name="submit" value="Save"></td>';
        echo '</tr>';
		echo '<tr class="isi-konten">';
		echo '<td> <input type="checkbox"></td>';
		
		echo '<td> Category/Country Name</td>';
		echo '<td> CID</td>';

			if($getCategory->rowCount() > 0) {
                while ($arr2 = $getCategory->fetch(PDO::FETCH_ASSOC)) {
                	echo '<tr class="isi-konten">';

                	$checked = in_array($arr2["cid"],explode(',', $arr3["cids"])) ? 'checked' : '';

                    echo '<td><input type="checkbox" name="item[]" value="'.$arr2["cid"].'" '.$checked.'></td>';
					echo '<td>'.$arr2["category_name"].'</td>';
					echo '<td>'.$arr2["cid"].'</td>';
					echo '</tr>';
                }
            } 

		echo '<tr>';
        echo '<td><input type="hidden" name="group_id" value="'.$group_id.'">';
        echo '<input type="hidden" name="save" value="Save">';
        echo '<input type="submit" name="submit" value="Save" ></td>';
        echo '</tr>';
		echo '</table>';
        echo '</form>';
}

function saveList($item, $group_id, $connect) {


$items = implode(', ', $item);
//print_r($items);

$check = $connect->prepare("SELECT gid, cids FROM tbl_group WHERE gid = :group_id");
$check->execute(array(
	':group_id' => $group_id
	));

	if($check->rowCount() > 0) {
		
		// update
		echo 'update';
		$stmt = $connect->prepare("UPDATE tbl_group SET cids = :cids WHERE gid = :gid;");
		$stmt->execute(array(
			':cids' => $items,
			':gid' => $group_id
		));

	} else {

		// insert
		$stmt = $connect->prepare("INSERT INTO tbl_group (gid, cids) VALUES (:gid, :cids);");
		$stmt->execute(array(
			':gid' => $group_id,
			':cids' => $items
		));

	}

}

?>