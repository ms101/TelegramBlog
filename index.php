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
	<style>
	* { margin: 0; padding: 0; }
	html, body {
		background-color: #d1d1d1;
		font-family: sans-serif;
	}
	li a, li a:link, li a:active {
		color: #fff; /* orangerot f20 blau 008fff */
		text-decoration: none;
	}
	li a:hover { color: #2b82cb; }
	#leftpane {
		width: 80px;
		height: 100%;
		position: fixed;
		padding: 20px 0;
		background-color: #444;
		border-left: 3px solid #00adee; /* 2b82cb */
	}
	#leftpane h1 {
		writing-mode: vertical-lr;
		text-orientation: mixed;
	}
	#rightpane {
		width: auto;
		margin: 0 0 60px 0;
		padding: 60px 0 0 80px;
	}
	.images {
		display: block;
		width: 60%;
		margin: 28px auto 0;
		box-shadow: 2px 2px 3px black;
		box-shadow: 1px 1px 4px black;
		-moz-transition-duration: .2s; /* firefox */
		-webkit-transition-duration: .2s; /* chrome, safari */
		-o-transition-duration: .2s; /* opera */
		-ms-transition-duration: .2s; /* ie 9 */
	}
	@media (max-width: 1200px) {
		.images {
			width: 90%;
		}
	}
	.images:hover {
		width: 100%;
	}
	#logo { /* color #2b82cb */
		display: inline;
		width: 210px;
		margin-left: 20%;
	}
	h1 {
		color: white;
		margin: 0 20%;
		font-size: 44px;
		font-family: serif;
	}
	h2 {
		margin: 0 20% 24px;
		font-size: 160%;
		color: #d1d1d1;
		color: #444;
		font-family: serif;
		text-align: left;
		text-shadow: 0 0 5px #fff;
	}
	body > ul li {
		list-style-type: none;
		margin-bottom: 1px;
		color: #aaa;
	}
	body > ul li a {
		width: 100%;
		padding: 0 32px 0 26px;
		border-left: 6px solid #444;
		background-color: #444;
	}
	body > ul li a:hover {
		border-left: 6px solid #00adee;
		color: #fff;
	}
	p, ul, ol {
		width: 55%;
		margin: 0 auto;
		font-size: 110%;

	}
	header > ol { width: 47%; }
	header > ol li { margin-top: 6px; }
        header > ul { width: 47%; }
        header > ul li { margin: 6px 0; } 
	header { margin-bottom: 40px; }
	pre { text-align: left; }
	.captions {
		margin-top: 6px;
		text-align: center;
	}
	.post {	text-align: left; }
	.post span { opacity: .5 ; }
	#symbol {
		font-size: 90px;
		color: #444;
		text-align: center;
	}
	</style>
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
$incdir = "./incfiles";
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
