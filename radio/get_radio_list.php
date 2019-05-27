<?php
include("includes/db_connection.php");

header('Content-Type: text/html; charset=utf-8');

$getGroup = $connect->query("SELECT cid AS CID, category_name AS CNAME, `key` AS CKEY FROM rtbl_category order by category_name");

		echo '
        <form method="get" action="">
        Group: <select name="group">
            ';
        			//echo '<option value="">Blank</option>';
            if($getGroup->rowCount() > 0) {
                while ($arr1 = $getGroup->fetch(PDO::FETCH_ASSOC)) {
                    $selected = $arr1["CKEY"] == $_GET["group"] ? 'selected' : '';
                    echo '<option value="'.$arr1["CKEY"].'" '.$selected.'>'.$arr1["CNAME"].'</option>';
                }
            } 

		echo '
        </select>
        <br />
        <input type="submit" name="submit" value="Check">
        </form>
        <br />
        <a href="get_radio_list.php">Back to Home</a>
        <br />
        <br />
        ';

if(isset($_GET['submit'])) {
	//$_POST['Countries'];
	getList($_GET['group'], $connect);

}

function getList($ckey, $connect) {

        //$getCategory = $connect->query("SELECT cid, category_name, `key` FROM rtbl_category order by category_name");

        //$getGroupCategory = $connect->prepare("SELECT gid, cids FROM tbl_group_categories WHERE gid = :gid");
        $getGroupCategory = $connect->prepare("
            SELECT rg.channel_name AS radio_name
            FROM tbl_channels_radio_grab3 rg
            JOIN tbl_category_grab3 c ON c.cid = rg.cat_id
            WHERE rg.cat_id = (SELECT cid FROM tbl_category_grab3 WHERE `key` = :ckey)
            AND rg.is_deleted != 1
                UNION
            SELECT r.radio_name
            FROM rtbl_radio_new r
            JOIN rtbl_category c ON c.cid = r.category_id
            WHERE c.key = :ckey
            AND (r.radio_url != '#STATUS: 400 ' OR r.radio_url IS NULL)
            AND r.is_deleted != 1
            ORDER BY radio_name
            ");
        $getGroupCategory->execute(array(
            ':ckey' => $ckey
            ));

        //$arr3 = $getGroupCategory->fetch();

        echo '<table border="1">';
        echo '<tr class="isi-konten">';
        //echo '<textarea>';       
        //echo '<td> Category/Country Name</td>';

            if($getGroupCategory->rowCount() > 0) {
                while ($arr2 = $getGroupCategory->fetch(PDO::FETCH_ASSOC)) {

                    //$checked = in_array($arr2["cid"],explode(',', $arr3["cids"])) ? 'checked' : '';

                    //echo '<td>'.$arr2["radio_name"].'</td>';
                    echo $arr2["radio_name"];
                    echo '<br />';
                }
            } 
        //echo '</textarea>';
        echo '</table>';
}
