<?php
// radio check tunein

$link_number = "8110";

			$radio_urls = array(
                "http://legato.radiotime.com/Tune.ashx?id=s",
                #"http://dev-opml.tunein.com/Tune.ashx?id=s",
                "http://stage-opml.tunein.com/Tune.ashx?id=s",
                "http://opml.radiotime.com/Tune.ashx?id=s"
                );

            shuffle($radio_urls);
            //print_r($radio_url[0]);

            //$f = fopen($link['radio_url'], 'r');
            //$radio_url = 'http://legato.radiotime.com/Tune.ashx?id=s'.$link['note'].'';
            //$radio_url = 'http://dev-opml.tunein.com/Tune.ashx?id=s'.$link['note'].'';
            //$radio_url = 'http://stage-opml.tunein.com/Tune.ashx?id=s'.$link['note'].'';
            //$radio_url = 'http://opml.radiotime.com/Tune.ashx?id=s'.$link['note'].'';
            $radio_url = $radio_urls[0].$link_number;
            echo $radio_url;
            echo '<br />';

            $f = file_get_contents($radio_url);

            echo $f;
            echo '<br />';

            if (strpos($f, '#STATUS: 400') == true) {
                echo 'insert';
            } else {
                echo 'false';
            }

            /*if($f != "#STATUS: 400 ") {
                echo 'clear';
            }*/

?>