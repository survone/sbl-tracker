<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="cache-control" content="no-cache" />

<title>SBL Blacklist Tracker</title>

<style>

body {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
  line-height: 20px;
  background: #eee;
}

span {
  color: #444;
  display: block; 
  float: left;
  width: 120px;
  padding-left: 5px;
}

input {
  width: 200px;
  margin: 3px 3px;
  height: 20px;
  font-size: 12px;
  border: 1px solid #ccc;
}

textarea {
  font-size: 12px;
  border: 1px solid #ccc;  
}

.red {
  background: #ffdddd;
  height: 30px;
  line-height: 28px;
}

.blue {
  background: #ddddff;
  height: 30px;
  line-height: 28px;
}

.inputButton input {
  border: 1px solid #888;
  background: lightgray;
}

.inputButton input:hover {
  border: 1px solid #888;
  background: lightgreen;
}

</style>

</head>
<body>

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

<?php 
  
  require("config.php");

  $rbl_id = $_GET["id"];
  $query = "SELECT * FROM `rbl_listings` WHERE rbl_id=" . $rbl_id;
  if ($result = mysqli_query($dbh, $query)) {
    $row = mysqli_fetch_assoc($result);
  } else { echo $query; }

?>

<a href="index.php">Back to index</a>
<form method="POST" action="engine.php">
  <input type="hidden" name="action" value="rbl-update" />
<div class="edit-sbl">
<div class="blue">
  <span>#  </span><input name="rbl_id" readonly="readonly" value="<?php echo $row["rbl_id"]; ?>"/>
</div>
<div class="red"> 
  <span>SBL entry: </span><input name="sbl_id" readonly="readonly" value="<?php echo $row["id"]; ?>"/>
</div>
<div class="blue">
  <span>IP range: </span><input name="ip_range" value="<?php echo $row["ip_range"]; ?>"/>
</div>
<div class="red">
  <span>Server: </span><input name="server_name" value="<?php echo $row["server_name"]; ?>"/>
</div>
<div class="blue">
  <span>Status: </span>
  <select id="status" name="status">
    <option value="active">Active</option>
    <option value="closed">Closed</option>
  </select>
</div>
<div class="red">
  <span>Initiated by: </span><input name="initiated_by" value="<?php echo $row["initiated_by"]; ?>"/>
</div>

<div class="blue">
  <span>Brand: </span>
  <select id="brand" name="brand">
    <?php
      foreach ( $brands as $brand_key => $brand_desc ) {
        print "<option value=\"$brand_key\">$brand_desc</option>";
      }
    ?>
  </select>
</div>

<script>
<?php
  reset($brands); $sel_value = key($brands); // get the first key in the array

  foreach ( $brands as $brand_key => $brand_desc ) {
    if (strcmp($row["brand"], "$brand_key") == 0 ) { $sel_value = "$brand_key"; }
  }
?>
  var sel = document.getElementById("brand").value = "<?php echo $sel_value; ?>";
</script>

<div class="red">
  <span>Ticket ID: </span><input name="ticket_id" value="<?php echo $row["ticket_id"]; ?>"/>
</div>
<div class="blue">
  <span>Client ID: </span><input name="client_id" value="<?php echo $row["client_id"]; ?>"/>
</div>
<div class="red">
  <span>Comment:</span>
</div>
<div style="background: #ffdddd;">
<textarea cols="80" rows="10" name="comment">
<?php 
  $c_text = urldecode($row["comment"]); 
  echo $c_text;
?>
</textarea>
</div>
<div class="blue">
  <span class="inputButton"><input type="submit" value="Update" /></span>
</div>
</div>

</form>

<?php 
  mysqli_close($dbh);
?>

</body>
</html>
