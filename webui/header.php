<?php 
include_once("db.php");
include_once("mootwit.php");

$id = $_GET['number'];
$description = getArticleDesc($id);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><? echo "$sitename"; ?> </title>
<script src="http://yui.yahooapis.com/3.5.0/build/yui/yui-min.js"></script>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.0/build/cssreset/cssreset-min.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.0/build/cssfonts/cssfonts-min.css">
<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
<link rel="alternate" type="application/rss+xml" title="<?php echo "$sitename"; ?> (RSS 2.0)" href="<?php echo "$siteurl"; ?>/rss.php"  />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
<meta name="description" content="<? echo "$description"; ?>" />
</head>
<body>
<div class="main">
<h2 class="title"><b><a href="<? echo "$siteurl"; ?>" class="title"><? echo "$sitename"; ?></a></b></h2>
<p class="menu">
<?php
	echo "entries: " . $numOfEntries;
?>
</p>
