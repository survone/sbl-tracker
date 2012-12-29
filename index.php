<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>SBL Blacklist Tracker</title>

<style>

body {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
  line-height: 20px;
}

.rbl_header {
  display: block;
  width: 100%;
  height: 20px;
  background: #fe9494;
  border-bottom: 1px solid #999;
  border-left: 1px solid #999;
  border-right: 1px solid #999;
  font-weight: bold;
}

.rbl_entry {
  display: block;
  width: 100%;
  height: 20px;
  background: #eee;
  border-bottom: 1px solid #999;
  border-left: 1px solid #999;
  border-right: 1px solid #999;
}

span {
  color: #444;
  display: block; 
  float: left;
  width: 120px;
  height: 20px;
}

.idx-tab {
  font-weight: bold; 
  width: 40px;
  padding-left: 5px;
}

.ip-tab {
  width: 190px;
}

.server-tab {
  font-size: 10px;
}

ul, li {
  list-style: none;
  margin: 0;
  padding: 0;
}

#comment_div {
  width: 100%;
  border: 1px solid #999;
}

</style>

</head>
<body>

<a href="index.php">Home</a>&nbsp;
<a href="engine.php?action=fetch-sbl">Fetch fresh records from SBL</a>&nbsp;
<a href="index.php?status=closed">Show closed entries</a>&nbsp;

<?php
  require("config.php");
?>

<script>
function makeRequest(url, reqid, reqtype, parameters, callback) {
 
  var httpRequest;
  if (window.ActiveXObject) {
        try {
            httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    } else if (window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
        if (httpRequest.overrideMimeType) {
            httpRequest.overrideMimeType('text/xml');
        }
    }
    
    if (!httpRequest) {
        alert('HttpRequest object creation failed.');
        return false;
    }
    httpRequest.onreadystatechange = function() {gotResult(httpRequest,reqid,callback);};
    if (reqtype == 'GET'){
        httpRequest.open('GET', url, true);
        httpRequest.send('');
    } else {
        httpRequest.open('POST', url, true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.setRequestHeader("Content-length", parameters.length);
        httpRequest.setRequestHeader("Connection", "close");
        httpRequest.send(parameters);
    }
    return true;
}

function gotResult(httpRequest, reqid, callback) {
    if (httpRequest.readyState == 4) {
        if (httpRequest.status == 200) {

            callback(httpRequest.responseText);
        } else {
            alert('Request failed: '+reqid+", status:"+httpRequest.status );
        }
    }
}

function show_comment(bl_id){
  makeRequest('engine.php?action=get-comment&id='+bl_id,'get_comment','GET','',function(x){ document.getElementById("comment_div").innerHTML = x; });  
}

</script>

<ul>
  <li>
    <div class="rbl_header">
    <span class="idx-tab">#</span>
    <span>SBL entry</span>
    <span class="ip-tab">IP range</span>
    <span>Server</span>
    <span>Brand</span>
    <span>Client ID</span>
    <span>Ticket ID</span>
    <span>Actions</span>
    </div>
  </li>

<?php

  $flt_status = "active";
  if (isset($_GET['status'])) { $flt_status = $_GET['status']; }

  $query = "SELECT * FROM `rbl_listings` WHERE status=\"" . 
          $flt_status . "\" AND parent_id is NULL";

  if ($result = mysqli_query($dbh, $query)) {

    for ($i = 0; $i < mysqli_num_rows($result); $i++ ) {
      mysqli_data_seek($result, $i);
      $row = mysqli_fetch_assoc($result);

      // output row
      echo "  <li>\n";
      echo "    <div class=\"rbl_entry\">\n";
      echo "    <span class=\"idx-tab\"><a href=\"#\" onclick=\"show_comment('".$row["rbl_id"]."');\">" . $row["rbl_id"] . "</a></span>\n";
      echo "    <span>" . $row["id"] . "</span>\n";
      echo "    <span class=\"ip-tab\"&nbsp;>" . $row["ip_range"] . "</span>\n";
      echo "    <span class=\"server-tab\">&nbsp;" . $row["server_name"]. "</span>\n"; // server name
      echo "    <span>&nbsp;" . $row["brand"] . "</span>\n";
      echo "    <span>&nbsp;" . $row["client_id"] . "</span>\n"; // client id
      echo "    <span>&nbsp;" . $row["ticket_id"]. "</span>\n"; // ticket id
      echo "    <span>&nbsp;<a href=\"engine.php?action=add-srv&id=" . 
      $row["id"] . "\">add srv</a>&nbsp;&nbsp;<a href=\"edit.php?id=" .
      $row["rbl_id"] . "\">edit</a></span>\n"; // tools
      echo "    </div>\n";
      echo "  </li>\n";

      // child entries
      $chld_query = "SELECT * FROM `rbl_listings` WHERE parent_id=\"" . 
          $row["id"] . "\"";
      if ($chld_result = mysqli_query($dbh, $chld_query)){
        for ($j = 0; $j < mysqli_num_rows($chld_result); $j++ ) {
          mysqli_data_seek($chld_result, $j);
          $chld_row = mysqli_fetch_assoc($chld_result);
          //output child row
          echo "  <li>\n";
          echo "    <div class=\"rbl_entry\">\n";
          echo "    <span class=\"idx-tab\"><a href=\"#\" onclick=\"show_comment('".$chld_row["rbl_id"]."');\">" . $chld_row["rbl_id"] . "</a></span>\n";
          echo "    <span>&nbsp;" . $chld_row["id"] . "</span>\n";
          echo "    <span class=\"ip-tab\">&nbsp;" . $chld_row["ip_range"] . "</span>\n";
          echo "    <span>&nbsp;</span>\n"; // server name
          echo "    <span>&nbsp;" . $chld_row["brand"] . "</span>\n";
          echo "    <span>&nbsp;" . $chld_row["client_id"] . "</span>\n"; // client id
          echo "    <span>&nbsp;" . $chld_row["ticket_id"] . "</span>\n"; // ticket id
          echo "    <span>&nbsp;<a href=\"engine.php?action=del-srv&id=" . 
          $chld_row["rbl_id"] . "\">del</a>&nbsp;&nbsp;<a href=\"edit.php?id=" . 
          $chld_row["rbl_id"] . "\">edit</a></span>\n"; // tools
          echo "    </div>\n";
          echo "  </li>\n";
        }
      }
    }
  }

  mysqli_close($dbh);
?>


</ul>
<!-- SBL table end -->

<strong>Comments:</strong> <br />

<div id="comment_div">&nbsp; click to a # link to see the comment for the selected entry.</div>


<script>

function colorifyTable(){
    var divs;
    var clr = 1;
    divs = document.getElementsByTagName("div");
    for (var i=0; i<divs.length; i++) {
        if (divs[i].className == "rbl_entry"){
            if (clr == 1) {
                divs[i].style.background = '#eee';
            } else {
                divs[i].style.background = '#ddd';
            }
            clr = 1 - clr;
        }
    }
}

colorifyTable();
</script>

<br /></br />
SBL Tracker v2.0 &copy; 2012 Rustam Tsurik

</body>
</html>
