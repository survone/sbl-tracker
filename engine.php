<?php

if (isset($_GET['action'])) { $req = $_GET['action']; } else { $req = NULL; }
if (isset($_POST['action'])) { $req = $_POST['action']; }

require("config.php");

if ($req == 'get-comment'){

	$rbl_id = $_GET["id"];
	$query = "SELECT * FROM rbl_listings WHERE rbl_id=\"" . $rbl_id . "\"";

	if ($result = mysqli_query($dbh, $query)) {
		$row = mysqli_fetch_assoc($result);

		$replace_n = array("\r\n", "\n", "\r");
		$comment = str_replace($replace_n,"<br />",urldecode($row["comment"]));
		if (strcmp($comment, "") == 0){
			echo "No comments found.<br />\n";
		} else {
			echo $comment;
		}
	} else {
		echo "No comments found.\n";
	}

    exit;
}

if ($req == 'add-srv'){

	$sbl_id = $_GET["id"];
	$query = "INSERT INTO rbl_listings (`parent_id`, `ip_range`) VALUES (\"" . $sbl_id . "\", \"NA\")";
	$result = mysqli_query($dbh, $query);

	header("Location: index.php");
	echo "Click <a href=\"index.php\">HERE</a> to return to the index page.";
	exit;
}

if ($req == 'del-srv'){

	$rbl_id = $_GET["id"];
	$query = "DELETE FROM rbl_listings WHERE rbl_id=" . $rbl_id;
	$result = mysqli_query($dbh, $query);

	header("Location: index.php");
	echo "Click <a href=\"index.php\">HERE</a> to return to the index page.";
	exit;
}

if ($req == 'rbl-update'){

	$rbl_id = $_POST["rbl_id"];
	$id = $_POST["sbl_id"];
	$ip_range = $_POST["ip_range"];
	$brand = $_POST["brand"];
	$ticket_id = $_POST["ticket_id"];
	$client_id = $_POST["client_id"];
	$server_name = $_POST["server_name"];
	$initiated_by = $_POST["initiated_by"];
	$status = $_POST["status"];
	$comment = urlencode($_POST["comment"]);

	$query = "UPDATE `rbl_listings` SET comment=\"" . $comment . 
	"\", ip_range=\"" . $ip_range . 
	"\", brand=\"" . $brand . 
	"\", ticket_id=\"" . $ticket_id . 
	"\", client_id=\"" . $client_id . 
	"\", server_name=\"" . $server_name . 
	"\", initiated_by=\"" . $initiated_by . 
	"\", status=\"" . $status . 
	"\" WHERE rbl_id=" . $rbl_id;

	$result = mysqli_query($dbh, $query);

	header("Location: index.php");
	echo "Click <a href=\"index.php\">HERE</a> to return to the index page.";
	exit;
}

if ($req == 'fetch-sbl'){
	
	$curlh = curl_init();
	curl_setopt($curlh, CURLOPT_URL, $fetch_url);
	curl_setopt($curlh, CURLOPT_RETURNTRANSFER, TRUE);

	$sbl_raw = curl_exec($curlh);

	curl_close($curlh);

	$DOM = new DOMDocument;
	$DOM->loadHTML($sbl_raw);

	$tr_items = $DOM->getElementsByTagName('b');
	$next_b = 0;
	$prev_val = "";

	foreach ($tr_items as $tr_node) {
		$val = $tr_node->nodeValue;

		if ( $next_b == 1 ) {
			$next_b = 0;
			echo $val . " -- " . $prev_val . "\n";
			if ($result = mysqli_query($dbh, "INSERT INTO `rbl_listings` (`id`, `ip_range`,`status`) VALUES ( \"" . $prev_val . "\" , \"" . $val . "\",\"active\" )" )) {
			echo "  -> added to DB<br />" ;
			}

		} 
		if (preg_match ("/^SBL/i", $val)) {
			// look up the record in DB
			if ($result = mysqli_query($dbh, "SELECT * FROM rbl_listings WHERE id = \"".$val."\"")) {
				if (mysqli_num_rows($result) == 0) {
					echo $val . " not found in the database<br />";
					$next_b = 1; $prev_val = $val;
				}
				mysqli_free_result($result);
			}
		}
	}

	echo "Done.<br />";
	echo "Click <a href=\"index.php\">HERE</a> to return to the index page.";

}

mysqli_close($dbh);

?>
