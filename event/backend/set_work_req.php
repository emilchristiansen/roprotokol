<?php
include("../../rowing/backend/inc/common.php");
require("inc/utils.php");
verify_right("admin","vedligehold");
$data = file_get_contents("php://input");
$d=json_decode($data);
$stmt=$rodb->prepare("UPDATE worker SET requirement=? WHERE assigner='vedligehold' AND member_id IN (SELECT id FROM Member WHERE MemberID=?)")
     or dbErr($rodb,$res,"set work");
$stmt->bind_param("ds", $d->worker->requirement,$d->worker->id) || dbErr($rodb,$res,"set work b");
$stmt->execute() or dbErr($rodb,$res,"set work exe");
invalidate("work");
echo json_encode($res);
