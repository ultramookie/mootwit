<?php
	include_once("header.php");
?>

<h1>average entries per day</h1>
<div id="mychart"></div>
<script>
YUI().use('charts', function (Y) { 
    var myDataValues = [ 
	<?php
		printDailyChartDate();
	?>
    ];

    var mychart = new Y.Chart({dataProvider:myDataValues, render:"#mychart", type:"column"});

});

</script>


<?php
	include_once("footer.php");
?>

