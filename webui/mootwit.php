<?php

// mootwit webui library
// steve "mookie" kong
//
// licensed under gplv3
// http://www.gnu.org/licenses/gpl-3.0.html

error_reporting(E_ERROR | E_PARSE);


$sitename = "mootwit";
$siteurl = "http://someotherplace.doesnotexist";
$indexNum = 20;
$numOfEntries = getNumEntries();

function showEntriesIndex($num) {

        $query = "select id from mootwit order by id desc limit $num";
        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		printEntry($row['id']);
        }
}

function showEntriesArchive($num,$pnum) {

        if($pnum == 1) {
                $offset = 1;
        } else {
                $offset = ($pnum-1) * $num;
        }

        $query = "select id from mootwit order by id desc limit $offset,$num";
        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		printEntry($row['id']);
        }
}

function printEntry($id) {
       
	$query = "select url, text, datediff(created_at, UTC_TIMESTAMP()) as date from mootwit where id = '$id'";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if (ereg(".*http.*",$row['text'])) {
                $text = makeLinks($row['text'],$row['url']);
        } else {
                $text = $row['text'];
        }

        echo "<p class=\"entry\">" . $text . " </p>";
	if ($row['date'] == 0) {
                echo "<p class=\"timedate\"><a href=\"https://twitter.com/#!/-/statuses/$id\">today</a></p><hr />";
	} else {
		$diff = $row['date'] * -1;
		if ($diff == 1) {
			$days = " day";
		} else {
			$days = " days";
		}
                echo "<p class=\"timedate\"><a href=\"https://twitter.com/#!/-/statuses/$id\">$diff $days ago</a></p><hr />";
	}
		
}

function makeLinks($text,$url) {
        $chunk = preg_split("/[\s,]+/", $text);
        $size = count($chunk);

        for($i=0;$i<$size;$i++) {
                if(ereg("^http",$chunk[$i])) {
                        $new = "<a href=\"$url\" rel=\"nofollow\" target=\"blank\">$url</a>";
                        $total = $total . " " . $new;
                } else {
                        $total = $total . " " . $chunk[$i];
                }
        }

        return $total;
}

function getNumEntries() {
	$query = "select count(id) from mootwit";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

        return($row['count(id)']);
}
