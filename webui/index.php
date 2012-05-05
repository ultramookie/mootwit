<?php
	include_once("header.php");
?>

<?php
	showEntriesIndex($indexNum,$printReplies);

	echo "<a href=\"" . $siteUrl  . "archive.php?pagenum=2\" class=\"box\">next &#187;</a>";
?>

<?php
	include_once("footer.php");
?>

