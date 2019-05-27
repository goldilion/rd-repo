<style>
table, th, td {
    border: 1px solid black;
}
</style>
<script type="text/javascript" src="js/checkbox.js"></script>
<?php 
include("db_connection.php");
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

        $category_list = $connect->query("
            SELECT c.cid AS CID, c.category_name AS CAT_NAME, c.key AS XKEY 
            FROM rtbl_category c
            JOIN rtbl_radio_new rn ON rn.category_id = c.cid
            GROUP BY c.cid
            ORDER BY c.category_name
        ");

        /*$get_channels = "
        SELECT id, category_id, radio_name, radio_image, radio_url
        FROM rtbl_radio 
        WHERE category_id = '".$catkey."'
        ";*/


        #$arrCategory = mysql_query($category_list);

        echo '
        <form method="get" action="">
        <select name="catkey">
            ';
        
            if($category_list->rowCount() > 0) {
                while ($arr1 = $category_list->fetch(PDO::FETCH_ASSOC)) {
                    $selected = $arr1["CID"] == $_GET["catkey"] ? 'selected' : '';
                    echo '<option value="'.$arr1["CID"].'" '.$selected.'>'.$arr1["CAT_NAME"].' '.$arr1["CID"].'</option>';
                }
            } 
/*
            if($arrCategory) {
                while ($arr = mysql_fetch_assoc($arrCategory)) {
                    $selected = $arr[CID] == $_GET[catkey] ? 'selected' : '';
                    echo '<option value='.$arr["CID"].' '.$selected.'>'.$arr["CAT_NAME"].' - '.$arr["CID"].'</option>';
                }
            } */
            
        echo '
        </select>
        <br />
        Status: <select name="is_deleted">
            <option value="2">All</option>
            <option value="1">Deleted</option>
            <option value="0">Active</option>
        </select>
        <br />
        <input type="hidden" name="listed" value="listed">
        <input type="submit" name="submit" value="Listing">
        </form>
        ';

    if(isset($_GET['catkey'])) {
        $catkey = $_GET['catkey'];
        $is_deleted = $_GET['is_deleted'];
        $startcheck = $_GET['startcheck'];
        $perpage = 10;

        $start = isset($_GET['page']) ? $_GET['page'] : '1';
        $startRow = ($start - 1) * $perpage;
        
        $qchannel = $connect->query("
            SELECT id, category_id, radio_name, radio_image, radio_url, note, is_deleted, DATE_FORMAT(last_checked, '%d %b %Y %H:%i:%s') as last_checked 
            FROM rtbl_radio_new WHERE category_id = '".$catkey."'  
            AND is_deleted != 1 
            LIMIT $startRow, $perpage");
       
        #$arrChannels = mysql_query($qchannel);

        showPagination($connect, 'rtbl_radio_new', $catkey);
        
        echo '<br />';
        echo '<form method="get" action="">';
        echo '<table>';

        echo '<tr><td>id</td><td>cat id</td><td>name</td><td>url</td><td>station id</td><td>is deleted</td><td>last checked</td></tr>';
        
        if($qchannel->rowCount() > 0) {
            while ($arr2 = $qchannel->fetch(PDO::FETCH_ASSOC)) {

                if ($startcheck) {
                    
                    $radio_urls = array(
                        "http://legato.radiotime.com/Tune.ashx?id=s",
                        #"http://dev-opml.tunein.com/Tune.ashx?id=s",
                        "http://stage-opml.tunein.com/Tune.ashx?id=s",
                        "http://opml.radiotime.com/Tune.ashx?id=s");

                    shuffle($radio_urls);

                    $radio_url = $radio_urls[0].$arr2['note'];
                    echo $radio_url;

                    $f = file_get_contents($radio_url);
                    //$meta = stream_get_meta_data($f);
                    //$suc = var_dump($meta['mode']);
                    //$uri = var_dump(is_writable($meta['uri']));
                    $tgl = date("Y-m-d H:i:s");
                    /*$sqlUpdate = $connect->query("
                        UPDATE rtbl_radio_new SET radio_url = "'.$f.'", last_checked = "'.$tgl.'" 
                        WHERE id = '.$link[id].'");*/

                    if(trim($f) != '#STATUS: 400') {
                        echo 'insert';
                        /*$stmt = $connect->prepare("UPDATE rtbl_radio_new SET radio_url = :radio_url, last_checked = :last_checked 
                            WHERE id = :id;");

                        $stmt->execute(array(
                            ':radio_url' => $f,
                            ':last_checked' => $tgl,
                            ':id' => $arr2['id']
                        ));*/   
                    } else {
                        echo 'throw';
                    }


                    #$sqls = "UPDATE rtbl_radio_new SET radio_url = ".$f.", last_checked = ".$tgl." WHERE id = ".$arr2['id'].";";
                    $sqls = "".$arr2['id']."|".$arr2['category_id']."|".$arr2['radio_name']."|".$arr2['radio_url']."|".$f."|".$arr2['note']."|".$tgl."";

                    echo $f;
                    echo '<br />';
                    $filename = "log/updetan_";
                    $strLog = $sqls.';';

                    //AppendSplit($filename, $strLog, false);

                }
                $no++;

                //if($link["is_deleted"] == 1) { $colorstat = "#FF0000"; } else { $colorstat = "#00FF00"; } 

                echo '<tr>';
                //echo '<td>'.$no.'</td>';
                echo '<td>'.$arr2["id"].'</td>';
                echo '<td>'.$arr2["category_id"].'</td>';
                echo '<td>'.$arr2["radio_name"].'</td>';
                echo '<td>'.$arr2["radio_url"].'</td>';
                echo '<td>'.$arr2["note"].'</td>';
                echo '<td bgcolor="'.$colorstat.'">'.$arr2["is_deleted"].'</td>';
                echo '<td>'.$arr2["last_checked"].'</td>';
                echo '</tr>';
            }
        }

        echo '</table>';
        echo '<input type="hidden" name="startcheck" value="startcheck">';
        echo '<input type="hidden" name="catkey" value='.$catkey.'>';
        //echo '<input type="hidden" name="is_deleted" value="$is_deleted">';
        echo '<input type="hidden" name="page" value="'.$start.'">';
        echo '<input type="submit" name="submit" value="Start">';
        echo '</form>';

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

// radio check 
function radioChecker ($channelName) {
    $c = curl_init($channelName);

    curl_setopt($c, CURLOPT_HEADER, FALSE);
    curl_setopt($c, CURLOPT_NOBODY, TRUE);
    curl_setopt($c, CURLOPT_TIMEOUT, 3600);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    if (curl_exec($c)) {
        if (curl_getinfo($c, CURLINFO_HTTP_CODE) == 200) {
            // success
            $coloring = "chartreuse";
            $dataIdSuccess[] = $link["id"];
        }
    } else {
        // network error or server down
        $coloring = "red";
        $dataIdFailed[] = $link["id"];
    }
}

//justintv status check 
function justinTVcheck ($channelName) { 
    $channelName = strtolower($channelName); 
    $json_file = "http://api.justin.tv/api/stream/list.json?channel=" . $channelName;
    $json = file_get_contents($json_file);
    if (strpos($json, 'name')) { 
        return (1); //online 
    } else { 
        return (0); //offline 
    } 
}

// https://api.twitch.tv/kraken/base

//own3d status check 
function ownedTVcheck ($channelName) { 
    $request = 'http://api.own3d.tv/liveCheck.php?live_id='; 
    $arg = $channelName; 
    $session = curl_init($request.$arg); 
    curl_setopt($session, CURLOPT_HEADER, false); 
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
    $response = curl_exec($session); 
    curl_close($session); 

    if (preg_match("/true/",$response, $result)) { 
        return (1); //online 
    } else { 
        return (0); //offline 
    } 
}

//regame status check 
function regameTVcheck ($channelName) { 
    $url = 'http://www.regame.tv/liveview_xml_multiple.php?stream_ids='; 
    $arg = $channelName; 
    $xml = simplexml_load_file($url.$arg); 
    $status=$xml->stream->online; 
    if (strcmp($status,'true')==0) { 
        return (1); //online 
    } else { 
        return (0); //offline 
    } 
}

function showPagination($connect, $tableName, $catkey, $limit = 10)
{
    $stmt = $connect->query('SELECT COUNT(id) AS total FROM '.$tableName.' WHERE category_id = '.$catkey.'  AND is_deleted != 1');
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRow = $results['total'];

    /*$countTotalRow = mysql_query('SELECT COUNT(id) AS total FROM '.$tableName.' WHERE category_id = '.$catkey.'  AND is_deleted != 1');
    $queryResult = mysql_fetch_assoc($countTotalRow);
    $totalRow = $queryResult['total'];*/
 
    $totalPage = ceil($totalRow / $limit);
    
    $page = 1;
    while ($page <= $totalPage) 
    {
        echo '<a href="?catkey='.$catkey.'&page='.$page.'">'.$page.'</a>';
        if ($page < $totalPage)
            echo " | ";
 
        $page++;
    }
    echo '<br />';
    echo 'Total Data: '.$totalRow;
    echo '<br />';
}

/*
    $resouter = mysql_query($get_channels);
     
    $set = array();
     
    $total_records = mysql_num_rows($resouter);
    if($total_records >= 1){
      //while ($link = mysql_fetch_array($resouter, MYSQL_ASSOC)){
        foreach ($resouter) {
            $c = curl_init($url);
            //$f = fopen($filepath, "w")
            //curl_setopt($c, CURLOPT_FILE, $f);
            curl_setopt($c, CURLOPT_HEADER, 0);
            if (curl_exec($c)) {
                if (curl_getinfo($c, CURLINFO_HTTP_CODE) == 200) {
                    // success
                    print_r($resouter);
                } else {
                    // 404 or something, delete file
                    unlink($filepath);
                }
            } else {
                // network error or server down
                echo "";
                break; // abort
            }
            curl_close($c);
        }
    }
     
*/


/*foreach (...) {
    $c = curl_init($url);
    //$f = fopen($filepath, "w")
    //curl_setopt($c, CURLOPT_FILE, $f);
    curl_setopt($c, CURLOPT_HEADER, 0);
    if (curl_exec($c)) {
        if (curl_getinfo($c, CURLINFO_HTTP_CODE) == 200) {
            // success
            echo "";
        } else {
            // 404 or something, delete file
            unlink($filepath);
        }
    } else {
        // network error or server down
        echo "";
        break; // abort
    }
    curl_close($c);
}*/

/*function is_connected()
{
    $connected = @fsockopen("http://kik.com", 80); 
                                        //website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;

}*/

?>