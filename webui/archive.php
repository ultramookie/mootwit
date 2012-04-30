<?php
	include_once("header.php");
?>

<?php

        if (!$_GET['pagenum']) {
                $pagenum = 1;
        } else {
                $pagenum = $_GET['pagenum'];
        }

	showEntriesArchive($indexNum,$pagenum);

	$pagenum++;

	echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?pagenum=" . $pagenum . "\" class=\"box\">older &#187;</a>";
?>

<?php
	include_once("footer.php");
?>

