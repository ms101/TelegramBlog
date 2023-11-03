<?php

// switch debugging on manually
$DEBUG = 0;
// fill in access token of your bot
$token = "";
// TODO for future usage, restrict to specific group via chat id
$chatid = "";

// activate debugging via command line argument, e.g. as cronjob command: php -f /path/to/updatesite.php debug
if($argv[1] === "debug") $DEBUG = 1;
if($DEBUG) error_reporting(E_ALL);

$imgdir = "images";
$offsetfile = "offset";

function logger($l) {
	global $DEBUG;
	if($DEBUG) echo $l . "<br />" . PHP_EOL;;
}

// read offset from file
$offset = file_get_contents("./" . $offsetfile);
if($offset)
	logger("[*] offset: " . $offset);

// getUpdates
$msg = file_get_contents("https://api.telegram.org/bot$token/getUpdates?offset=$offset");
$messages = json_decode($msg, true);

if($DEBUG) {
	echo "<pre>";
	var_dump($messages);
	echo "</pre>";
}

if(empty($messages["result"])) {
	logger("[*] No new messages");
	return;
}

// create HTML snippets as files
$date = date('Y-m-d_H-i-s', time());
$entryfile = fopen("./incfiles/inc_$date.html", "w");

// walk through all new messages
foreach($messages["result"] as $msg) {
	if($DEBUG) {
		echo "<pre>";
		var_dump($msg);
		echo "</pre><br />";
	}

	// set new offset
	logger("new offset: " . $msg["update_id"] + 1);
	file_put_contents($offsetfile, $msg["update_id"] + 1);

	// get photo if part of message
	if(isset($msg["message"]["photo"])) {
		// get file_id of hi res photo (assuming [3] has highes res)
		$id = $msg["message"]["photo"][3]["file_id"];
			
		// load photo from telegram (to bot)
		$ret = file_get_contents("https://api.telegram.org/bot$token/getFile?file_id=$id");
		$retdecode = json_decode($ret);

		if($DEBUG) {
			echo "<pre>";
			var_dump($retdecode);
			echo "</pre><br />";
		}

		// check if download to bot worked
		if($retdecode->ok) {
			$dlurl = "https://api.telegram.org/file/bot$token/" . $retdecode->result->file_path;
			$filename = substr($retdecode->result->file_unique_id, 0, -1) . ".jpg";

			logger($dlurl . "<br />");

			// download photo to server
			$wgetoutput = system("wget $dlurl -O './$imgdir/" . $filename . "'");

			logger($wgetoutput);

			$serverurl = "./$imgdir/$filename";
			logger("[*] Writing: <img src='$serverurl' alt='' class='images' />");
		} else {
			logger("[!] return code false, while downloading to bot:\n");
			if($DEBUG) print_r($retdecode);
		}

		// write snippet file to include dir
		if(isset($serverurl)) {
			fwrite($entryfile, "<img src='$serverurl' alt='' class='images' />");
		}
			
		// add caption if any
		if(isset($msg["message"]["caption"])) {
			logger("<p class='captions'>" . htmlspecialchars($msg["message"]["caption"]) . "</p>");
			fwrite($entryfile, PHP_EOL . "<p class='captions'>" . htmlspecialchars($msg["message"]["caption"]) . "</p>" . PHP_EOL);
		} else {
			logger("no caption");
		}
	} elseif(isset($msg["message"]["text"])) {
		// write blog post (text only)
		$text = $msg["message"]["text"];

		logger("[*] writing post with text: " . $text);
		fwrite($entryfile, "\n<br><p class='post'><span>" . date("d.m. H:i", $msg["message"]["date"]) . " Uhr</span><br />" . htmlspecialchars($text) . "</p>");
	}
}

fclose($entryfile);

?>
