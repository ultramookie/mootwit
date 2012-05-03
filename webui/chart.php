<?php
	include_once("header.php");
?>

<h1>18 months</h1>
<div id="mychart"></div>
<script>
YUI().use('charts', function (Y) { 
    var myDataValues = [ 
	<?php
		$months = 18;
		printChartData($months);
	?>
    ];

    var mychart = new Y.Chart({dataProvider:myDataValues, render:"#mychart"});

});

</script>


<?php
	include_once("footer.php");
?>

