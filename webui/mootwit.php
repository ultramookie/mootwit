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
        	$query = "select id from mootwit order by created_at desc limit $num";
	} else {
        	$query = "select id from mootwit where text not like '@%' order by created_at desc limit $num";
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
        	$query = "select id from mootwit order by created_at desc limit $offset,$num";
	} else {
        	$query = "select id from mootwit where text not like '@%' order by created_at desc limit $offset,$num";
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

	$query = "select gid from mootwit where id = '$id' and gid is NOT NULL";
        $result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		$query = "select url from moourls where tweetid = '$id'";
       		$result = mysql_query($query);
		for ($i = 0; $i <= mysql_num_rows($result); $i++){
		        while ($row = mysql_fetch_array($result)) {
			$text = $text . "<br /><br />" . makeLinks($row['url']);
        		}
		}
	}

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
        $chunk = preg_split("/[\s]+/", $text);
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
                        	$total = $total . "<br />" . $embed . "<br /><br />";
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

	$rssTitleLen = 64;
        $rssSummaryLen = 1024;

        if ($printRepliesRSS == 1) {
        	$query = "select t.id,t.text,date_format(t.created_at, '%a, %d %b %Y %H:%i:%s') as date, u.url from mootwit t join moourls u on u.tweetid=t.id  order by created_at desc limit $num";
        } else {
        	$query = "select t.id,t.text,date_format(t.created_at, '%a, %d %b %Y %H:%i:%s') as date, u.url from mootwit t join moourls u on u.tweetid=t.id where text not like '@%' order by created_at desc limit $num";
        }

        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		$title = strip_tags(substr($row['text'],0,$rssTitleLen));
		$title = ereg_replace("&nbsp;|\n|\r|\t","",$title);
                $title = htmlspecialchars($title,ENT_COMPAT,UTF-8);
                $shortBody = strip_tags(substr($row['text'],0,$rssSummaryLen));
                $shortBody = ereg_replace("&nbsp;|\n|\r|\t","",$shortBody);
                $shortBody = htmlspecialchars($shortBody,ENT_COMPAT,UTF-8);
                $cleanbody = ereg_replace("&nbsp;|\n|\r|\t","",$row['text']);
                echo "\t<item>\n";
                echo "\t\t<title>" . $title . "...</title>\n";
                echo "\t\t<pubDate>" . $row['date'] . " PST</pubDate>\n";
                echo "\t\t<description><![CDATA[" . $shortBody . "]]>...</description>\n";
                echo "\t\t<content:encoded><![CDATA[" . $cleanbody . "<br /><br />" . $row['url'] . " ]]></content:encoded>\n";
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

function printDailyChartDate() {

	$query = "select (count(id) / count(distinct date_format(created_at, '%m %d %y'))) as average, date_format(created_at, '%w') as day, date_format(created_at, '%W') as longday from mootwit group by day order by day";
	$result = mysql_query($query);

	$rows = mysql_num_rows($result);

	while ($row = mysql_fetch_array($result)) {
		echo "{category:\"" . $row['longday'] . "\", values:" . $row['average'] . "},";
	}
}

function getArticleDesc($id) {

	$cid = mysql_real_escape_string($id);

	$query = "select text from mootwit where id='$cid'";

        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        $shortdesc = mysql_real_escape_string(strip_tags(substr($row['text'],0,251)));

        $returndesc = stripslashes($shortdesc) . "...";

        return $returndesc;
}
