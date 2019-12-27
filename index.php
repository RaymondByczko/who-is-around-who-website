<?php
require 'vendor/autoload.php';
session_name('sn-WhoIsAroundWho-website');
session_start();

 
// Tell log4php to use our configuration file.
Logger::configure('config/config.xml');
 
// Fetch a logger, it will inherit settings from the root logger
$log = Logger::getLogger('index');
 
// Start logging
// $log->trace("trace message.");
$log->debug("index: start");
$sid = session_id();
$log->debug("session id=".$sid);

?>

<?php
$storageDir = 'jsondata/';
$jsonFiles = \WhoIsAroundWho\JSONArchiveApi::getAvailableJSONFiles($storageDir);

/* @todo Possible poor name for jsonYearMonths because its not json encoded. */
$jsonYearsMonths = \WhoIsAroundWho\JSONArchiveApi::getAvailableJSONYearsMonths($storageDir);
$log->debug("jsonYearsMonths=".var_export($jsonYearsMonths, TRUE));
$jsonEncodeYM = json_encode($jsonYearsMonths);
?>
<script>
var jsonEncodeYM=<?php echo $jsonEncodeYM; ?>;
</script>
<?php
$pathName = 'jsondata/193204.json';
$pathJSONData = 'jsondata/';
$resultArchive = '';
$foundHere = NULL;
try {
	if (isset($_POST['findThis']))
	{
		$year = $_POST['spYear'];
		$month = $_POST['spMonth'];
		$newPathName = $pathJSONData.$year.$month.'.json';
?>
		<script>alert('newPathName='.<?php echo $newPathName; ?>);</script>
<?php

		$findThis = $_POST['findThis'];
		$testContents = \WhoIsAroundWho\JSONArchiveApi::readContentsJSON($newPathName);
		\WhoIsAroundWho\JSONArchiveApi::checkStructure();
		$resultArchive .= $newPathName.': read, structure status: TODO';

		$foundHere = \WhoIsAroundWho\JSONArchiveApi::find($findThis, 'lead_paragraph');
	}
}
catch (Exception $e)
{
	$resultArchive .= $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta name="author" content="Raymond Byczko">
	<link rel="stylesheet" href="./css/styles.css">
	<title>WhoIsAroundWho Website | Welcome</title>
</head>
<body>
	<header>
		<div class="container">
	        <div id="branding">
	        	<h1><span class="highlight">PHP</span> WhoIsAroundWho</h1>
	        </div>
	        <nav>
	          <ul>
	            <li class="current"><a href="index.php">HOME</a></li>
	            <li><a href="help.php">HELP</a></li>
	            <li><a href="github.php">GITHUB</a></li>
	          </ul>
	        </nav>
	    </div>
	</header>
<p>
<section>
<div class="container2">
<div id="form_maken">
<form action="/index.php" method="post">
Pick Year:<br>
<input type="text" name="someYear" value="0">
<br><br>
Pick Month:<br>
<input type="text" name="someMonth" value="">
<br><br>
Find:<br>
<input type="text" name="findThis" value="">
<br><br>
PICK YEAR:<br>
<select id="id_year" name="spYear">
<?php
foreach ($jsonYearsMonths as $y=>$valM)
{
?>
	<option value=<?php echo '"'.$y.'">'.$y;?></option>
<?
}
?>
</select>
<br><br>
PICK MONTH:<br>
<select id="id_month" name="spMonth">
</select>
<br><br>
<input type="submit" value="Submit">
</form>
</div>
</div>
</section>
<section>
<?php
$resultOutput = NULL;
try {
	if ($foundHere != NULL)
	{
		foreach($foundHere as $key=>$value)
		{

			$pd = \WhoIsAroundWho\JSONArchiveApi::$m_phpContent['response']['docs'][$value]['pub_date'];
			$lp = \WhoIsAroundWho\JSONArchiveApi::$m_phpContent['response']['docs'][$value]['lead_paragraph'];
			echo '<br> ... '.$pd.' #### '.$lp;
		}
	}
}
catch (Exception $e)
{
	$resultOutput .= $e->getMessage();
}
?>
</section>
<script>
var y = document.getElementById("id_year");
y.addEventListener("change", function() {
	alert("changed year: new year="+y.value);
	let monthsAvail = jsonEncodeYM[y.value];
	alert("monthsAvail: " + monthsAvail);

	var m = document.getElementById("id_month");
	var i;
	for (i=m.length-1; i>=0;i--)
	{
		m.remove(i);
	}
	monthsAvail.forEach( (item,index)=> {
		var option = document.createElement("option");
		option.text = item;
		m.add(option);
	});
});

var m = document.getElementById("id_month");
var option = document.createElement("option");
option.text = "05";
m.add(option);
</script>
</body>
</html>
