<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$closedtrip=json_decode($data);

$distance = $closedtrip->boat->meter;
if (!empty($closedtrip->boat->corrected_distance)) {
    $distance=$closedtrip->boat->corrected_distance;
}

$rodb->begin_transaction();
$tripId=$closedtrip->boat->trip;

if ($stmt = $rodb->prepare("SELECT 'x' FROM Trip WHERE id=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $tripId);
  $stmt->execute();
  $result= $stmt->get_result();
  if (!$result->fetch_assoc()) {
      $error='notonwater';
      $message="trip $tripId already closed or not on water: ". json_encode($closedtrip,JSON_PRETTY_PRINT);
      error_log($message);
  }
} 

if (!$error) {
    if ($stmt = $rodb->prepare(
        "UPDATE Trip SET InTime = NOW(),Meter=?, Destination=?,Comment=? WHERE id=?;"
    )) { 
        $stmt->bind_param('issi', $distance,$closedtrip->boat->destination ,$closedtrip->boat->comment,$closedtrip->boat->trip);
        $stmt->execute(); 
        $rodb->commit();
    }
}

if ($error) {
    $res['status']='error';
    $res['error']=$error;
}
$res['message']=$message;
$res['boat']=$closedtrip->boat->name;

$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
