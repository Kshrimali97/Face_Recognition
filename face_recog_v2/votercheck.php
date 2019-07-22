<?php
require_once('connection.php');
if( isset( $_POST['id'] ) &&  isset( $_POST['tokenupdtvtr'] ) ){
	$sql    = "SELECT * FROM `voters` WHERE id = ".$_POST['id']." AND vote_status = 1";
	$check   = $conn->query($sql);

	if ( $check->num_rows > 0) {
		echo 'voted';die();
	}
	else{
		$sql    = "UPDATE `voters` SET vote_status = 1 WHERE id = ".$_POST['id']." AND vote_status = 0";
		$conn->query($sql);
		echo 'notvoted';die();
	}
}

?>