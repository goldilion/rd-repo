<style>
table, th, td {
    border: 1px solid black;
}
</style>
<script type="text/javascript" src="js/checkbox.js"></script>
<?php 
include("includes/db_connection.php");

        $category_list = "
        SELECT c.cid AS CID, c.category_name AS CAT_NAME, c.key AS XKEY 
        FROM rtbl_category c
        ORDER BY c.category_name
        ";

        /*$get_channels = "
        SELECT id, category_id, radio_name, radio_image, radio_url
        FROM rtbl_radio 
        WHERE category_id = '".$catkey."'
        ";*/


        $arrCategory = mysql_query($category_list);

        echo '
        <form method="get" action="">
        <select name="catkey">
            ';
        
            if($arrCategory) {
                while ($arr = mysql_fetch_assoc($arrCategory)) {
                    $selected = $arr[CID] == $_GET[catkey] ? 'selected' : '';
                    echo '<option value='.$arr["CID"].' '.$selected.'>'.$arr["CAT_NAME"].'</option>';
                }
            } 
            
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
        
        //$qtotals = "SELECT count(id) as total FROM rtbl_radio WHERE category_id = '".$catkey."'";
        $qchannel = "SELECT id, category_id, radio_name, radio_image, radio_url, is_deleted FROM rtbl_radio WHERE category_id = '".$catkey."' LIMIT $startRow, $perpage";
       
        $arrChannels = mysql_query($qchannel);
        
        // total
        //$arrTotals = mysql_query($qtotals);
        //$queryResult = mysql_fetch_assoc($arrTotals);
        //$totalRow = $queryResult['total'];

        //$totalPage = ceil($totalRow / $perpage);
//echo $start.'-'.$totalPage.'-'.$totalRow.'-'.$perpage;
        // total

    showPagination('rtbl_radio', $catkey);
        
        echo '<br />';
        echo '<form method="get" action="">';
        echo '<table>';
        /*
        echo '<tr><td colspan="6">';
        
        while ($start <= $totalPage) 
            {
                echo '<a href="?catkey='.$catkey.'&page='.$start.'">'.$start.'</a>';
                if ($start < $totalPage)
                    echo " | ";
         
                $start++;
            }

        echo '</td></tr>';
        */
        //echo '<tr>';
        echo '<tr><td>id</td><td>cat id</td><td>name</td><td>url</td><td>is deleted</td></tr>';
        if($arrChannels){
          $no = 0;
           while ($link = mysql_fetch_assoc($arrChannels)){
                if ($startcheck) {
                    //print_r($link);
                    //$f = fopen($link['radio_url'], 'r');
                    $f = file_get_contents($link['radio_url']);
                    $meta = stream_get_meta_data($f);
                    //$suc = var_dump($meta['mode']);
                    $uri = var_dump(is_writable($meta['uri']));
                    print_r($f);
                    echo '<br />';
                    /*
                    $connected = fsockopen($link, 80);

                    if ($connected) {
                            // success
                            $coloring = "chartreuse";
                            $dataIdSuccess[] = $link["id"];
                    } else {
                        // network error or server down
                        $coloring = "red";
                        $dataIdFailed[] = $link["id"];
                    }
                    */

                    /*
                    $c = curl_init($link['radio_url']);

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
                    */
                }
                $no++;

                 //if($link["is_deleted"] == 1) { $colorstat = "#FF0000"; } else { $colorstat = "#00FF00"; } 

                echo '<tr style="background-color:'.$coloring.';">';
                //echo '<td>'.$no.'</td>';
                echo '<td>'.$link["id"].'</td>';
                echo '<td>'.$link["category_id"].'</td>';
                echo '<td>'.$link["radio_name"].'</td>';
                echo '<td>'.$link["radio_url"].'</td>';
                echo '<td bgcolor="'.$colorstat.'">'.$link["is_deleted"].'</td>';
                echo '</tr>';
                
                #curl_close($c);
                fclose($connected);
            }

            
            //$queryUpdate = "UPDATE rtbl_radio SET is_deleted = ".$joinArr[status]." WHERE id IN ('$joinArr[ids]')";
            $impSuccess = implode($dataIdSuccess, ",");
            $impFailed = implode($dataIdFailed, ",");
            echo 'Sukses: '.$impSuccess;
            echo '<br />';
            echo 'Gagal: '.$impFailed;
            
            //mysql_query("UPDATE rtbl_radio SET is_deleted = NULL WHERE id IN ($impSuccess)");
            //mysql_query("UPDATE rtbl_radio SET is_deleted = '1' WHERE id IN ($impFailed)");

        }
        echo '</table>';
        echo '<input type="hidden" name="startcheck" value="startcheck">';
        echo '<input type="hidden" name="catkey" value='.$catkey.'>';
        //echo '<input type="hidden" name="is_deleted" value="$is_deleted">';
        echo '<input type="hidden" name="page" value="'.$start.'">';
        echo '<input type="submit" name="submit" value="Start">';
        echo '</form>';

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

function showPagination($tableName, $catkey, $limit = 10)
{
    $countTotalRow = mysql_query('SELECT COUNT(id) AS total FROM '.$tableName.' WHERE category_id = '.$catkey.'');
    $queryResult = mysql_fetch_assoc($countTotalRow);
    $totalRow = $queryResult['total'];
 
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