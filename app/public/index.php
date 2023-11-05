<?php

require_once realpath(__DIR__ . '/../../vendor/autoload.php');

use Dotenv\Dotenv;

(Dotenv::createImmutable(__DIR__ . '/../../'))->load();

?><!doctype html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title><?= $_ENV['APP_TITLE'] ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="leftpane">
	<h1>Blog</h1>
</div>
<div id="rightpane">

<header>
	<h2><?= $_ENV['APP_TITLE'] ?></h2>
	<p id='descr'><?= $_ENV['APP_DESCRIPTION'] ?></p>
</header>

<?php

// Check for updates in group
if($_ENV['TELEGRAM_UPDATE'] === 'true')
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

match ($_ENV['ENTRY_ORDER']) {
    'desc' => sort($files),
    default => rsort($files)
};

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
