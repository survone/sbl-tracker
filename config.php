<?php

// config section - customize it as needed
// DB connection settings
$m_host = "localhost";
$m_db = "DATABASE_NAME";
$m_username = "DATABASE_USER";
$m_password = "DATABASE_PASS";

// Brands
$brands = array(
    "shrd" => "Shared Hosting",
    "dedi" => "Dedicated Hosting",
    "vps" => "Virtual Hosting",
    "misc" => "Misc"
);

// URL to fetch SBL listings from
$fetch_url = "http://www.spamhaus.org/sbl/listings/MY-WEB-HOST.COM";

// config section end.

$dbh = mysqli_connect($m_host, $m_username, $m_password, $m_db);
if (!$dbh) {
	die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
}

?>
