<?php

// mootwit webui library
// steve "mookie" kong
//
// licensed under gplv3
// http://www.gnu.org/licenses/gpl-3.0.html

error_reporting(E_ERROR | E_PARSE);


$sitename = "mootwit";
$siteurl = "https://github.com/ultramookie/mootwit";
$indexNum = 7;
$numOfEntries = getNumEntries();
$printReplies = 0;
$printRepliesRSS = 0;

function showEntriesIndex($num,$printReplies) {

	if ($printReplies == 1) {
        	$query = "select id from mootwit order by id desc limit $num";
	} else {
        	$query = "select id from mootwit where text not like '@%' order by id desc limit $num";
	}

        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		printEntry($row['id']);
        }
}

function showEntriesArchive($num,$pnum,$printReplies) {

        if($pnum == 1) {
                $offset = 1;
        } else {
                $offset = ($pnum-1) * $num;
        }

	if ($printReplies == 1) {
        	$query = "select id from mootwit order by id desc limit $offset,$num";
	} else {
        	$query = "select id from mootwit where text not like '@%' order by id desc limit $offset,$num";
	}

        $result = mysql_query($query);
	$numEntries = mysql_num_rows($result);

        while ($row = mysql_fetch_array($result)) {
		printEntry($row['id']);
        }

	$pagenum = $pnum + 1;

	if ($numEntries == $num) {
		echo "<a href=\"page-" . $pagenum . "\" class=\"box\">next &#187;</a>";
	}
}

function printEntry($id) {
     
	$hoursecs = 60 * 60; 
	$daysecs = 60 * 60 * 24;
 
	$query = "select text, unix_timestamp(UTC_TIMESTAMP()) - unix_timestamp(created_at) as secdiff from mootwit where id = '$id'";
        $result = mysql_query($query);
        if (mysql_num_rows($result) == 0) {
                return;
        }
        $row = mysql_fetch_array($result);

        if (ereg(".*http.*",$row['text'])) {
                $text = makeLinks($row['text']);
        } else {
                $text = $row['text'];
        }

	$timediff = $row['secdiff'];
	$hours = (int)($timediff / $hoursecs);
	$days = (int)($hours / 24);

        echo "<p class=\"entry\">" . $text . " </p>";
	if ($timediff < $hoursecs) {
		$time = (int)($timediff / 60) . " minutes";
	} else if (($hours > 1) && ($hours < 24)) {
			$time = "$hours hours";
	} else if ($hours == 1) {
			$time = "$hours hour";
	} else if ($days == 1) {
			$time = "$days day";
	} else {
			$time = "$days days";
	}
                
	echo "<p class=\"timedate\"><a href=\"/$id\">$time ago</a></p><hr />";
		
}

function makeLinks($text) {
        $chunk = preg_split("/[\s,]+/", $text);
        $size = count($chunk);

        for($i=0;$i<$size;$i++) {
                if(ereg("^http",$chunk[$i])) {
			$query = "select url from moourls where short='$chunk[$i]'";
        		$result = mysql_query($query);
			$shortened = mysql_num_rows($result);

			if ($shortened == 0) {
				$realurl = $chunk[$i];
			} else {
		        	$row = mysql_fetch_array($result);
				$realurl = $row['url'];
			}

               		if(ereg("^http.*youtube\.com.*watch",$realurl)) {
                        	$embed = makeYouTube($realurl);
                        	$total = $total . "<br /><br />" . $embed . "<br /><br />";
			} else {
                        	$new = "<a href=\"$realurl\" rel=\"nofollow\" target=\"blank\">$realurl</a>";
                        	$total = $total . " " . $new;
			}

                } else {
                        $total = $total . " " . $chunk[$i];
                }
        }

        return $total;
}

function makeRSSLinks($text) {
        $chunk = preg_split("/[\s,]+/", $text);
        $size = count($chunk);

        for($i=0;$i<$size;$i++) {
                if(ereg("^http",$chunk[$i])) {
			$query = "select url from moourls where short='$chunk[$i]'";
        		$result = mysql_query($query);
		        $row = mysql_fetch_array($result);
			$realurl = $row['url'];
	        	$total = $total . " " . $realurl;
                } else {
                        $total = $total . " " . $chunk[$i];
                }
        }

        return $total;
}

function makeYouTube($in_url) {

        list($blah,$args) = split("\?",$in_url,2);

        if ($args) {
                $argsList = split("\&",$args);
                $num = count($argsList);
                for($i=0;$i<=$num;$i++) {
                        list($key,$value) = split("=",$argsList[$i]);
                        $$key = $value;
                }
                if ($v) {
                        $youtube = "<iframe width=\"640\" height=\"360\" src=\"https://www.youtube-nocookie.com/embed/$v\" frameborder=\"0\" allowfullscreen></iframe>";
                } else {
                $youtube = "<a href=\"$youtube_url\">$youtube_url</a>";
                }
        }

        return ($youtube);
}

function getNumEntries() {
	$query = "select count(id) from mootwit";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

        return($row['count(id)']);
}

function printRSS($num,$printRepliesRSS,$siteurl) {

        if ($printRepliesRSS == 1) {
        	$query = "select id,text,date_format(created_at, '%a, %d %b %Y %H:%i:%s') as date from mootwit order by id desc limit $num";
        } else {
        	$query = "select id,text,date_format(created_at, '%a, %d %b %Y %H:%i:%s') as date from mootwit where text not like '@%' order by id desc limit $num";
        }

        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		$url = makeRSSLinks($row['text']);
                echo "\t<item>\n";
                echo "\t\t<title>" . htmlspecialchars($url,ENT_COMPAT,UTF-8) . "</title>\n";
                echo "\t\t<pubDate>" . $row['date'] . " GMT</pubDate>\n";
                echo "\t\t<guid>" . $siteurl . "/" . $row['id'] . "</guid>\n";
                echo "\t\t<link>" . $siteurl  . "/" . $row['id'] . "</link>\n";
                echo "\t</item>\n";
        }
}

function printChartData($months) {

	$query = "select count(id),date_format(created_at, '%Y/%m') as date,date_format(created_at, '%b/%y') as mon from mootwit group by date desc limit $months";
	$result = mysql_query($query);

	$rows = mysql_num_rows($result);

	while ($row = mysql_fetch_array($result)) {
		echo "{category:\"" . $row['mon'] . "\", values:" . $row['count(id)'] . "},";
	}
}
