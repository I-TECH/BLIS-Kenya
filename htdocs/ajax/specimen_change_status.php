<?php
#
# Changes the status of the specimen
# Called via Ajax from sample collection

require_once("../includes/db_lib.php");
$sid = $_REQUEST['sid'];
$time_collected =date("Y-m-d H:i:s"); //$_REQUEST['tc']; 
set_specimen_status($sid, 0, $time_collected);


?>
