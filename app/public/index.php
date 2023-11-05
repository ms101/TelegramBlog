<?php

// enable or disable update checks by Telegram bot
$update = true;

// define blog title/topic
$title = "2023-11 Exampletrip";

// define description
$descr = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.";

// define order of new entries (asc/desc)
$order = "desc"

?><!doctype html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title><?=$title?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="leftpane">
	<h1>Blog</h1>
</div>
<div id="rightpane">

<header>
	<h2><?=$title?></h2>
	<p id='descr'><?=$descr?></p>
</header>

<?php

// Check for updates in group
if($update)
	include "./updatesite.php";

// fill content via loaded html files
$incdir = "./incs";
$files = array();

if(!is_dir($incdir))
	mkdir($incdir);

$handle = opendir($incdir) or die("[!] error with incdir file handle");

while (($entry = readdir($handle)) !== FALSE) {
	if($entry != "." &&
	   $entry != ".." &&
	   preg_match("/^inc[a-z0-9_-]*\.html$/", $entry)) {
		$files[] = $entry;
	}
}
closedir($handle);

if($order === "desc") {
	sort($files);
} else {
	rsort($files);
}

if(!empty($files)) {
	foreach($files as $file) {
		readfile($incdir . "/" . $file);
		echo PHP_EOL;
	}
} else {
	echo "<p>No content yet!</p>";
}

?>
<p id='symbol'>&#3484;</p>

</div> <!-- rightpane -->
</body>
</html>
