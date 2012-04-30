<?php 
include_once("db.php");
include_once("mootwit.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><? echo "$sitename"; ?> </title>
<link rel="stylesheet" type="text/css" href="base-min.css" />
<link rel="stylesheet" type="text/css"  href="reset-fonts.css" />
<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
</head>
<body>
<div class="main">
<h2 class="title"><b><a href="<? echo "$siteurl"; ?>" class="title"><? echo "$sitename"; ?></a></b></h2>
<p class="menu">
<?php
	echo "entries: " . $numOfEntries;
?>
</p>
