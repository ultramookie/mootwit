<?php
	include_once("header.php");
?>

<?php

        if (!$_GET['pagenum']) {
                $pagenum = 1;
        } else {
                $temppagenum = $_GET['pagenum'];
                if (preg_match('/^[0-9]+$/',$temppagenum)) {
                        $pagenum = $temppagenum;
                } else {
                        $pagenum = 1;
                }
        }

	showEntriesArchive($indexNum,$pagenum,$printReplies);

	$pagenum++;

?>

<?php
	include_once("footer.php");
?>

