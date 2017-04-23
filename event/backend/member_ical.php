<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
require('../../phplib/vendor/autoload.php');
$s="SELECT Member.MemberId AS owner, event.name, BoatCategory.Name as boat_category, start_time,end_time, 
    distance, TripType.Name as trip_type, max_participants, location, category, preferred_intensity, comment
    FROM Member, (event LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category) 
                  LEFT JOIN TripType ON TripType.id=event.trip_type 
    WHERE Member.id=event.owner AND start_time >= NOW()";

$result=$rodb->query($s);

if ($result) {
$vCalendar = new \Eluceo\iCal\Component\Calendar('aftaler.danskestudentersroklub.dk');


while ($row = $result->fetch_assoc()) {
    $vEvent = new \Eluceo\iCal\Component\Event();
    $endtime=$row['end_time'];
    if (empty($endtime)) {
        $endtime=new \DateTime($row['start_time']);
        error_log("et".print_r($endtime,true));
        date_modify($endtime, '+2 hour');
        error_log("Eet".print_r($endtime,true));
    } else {
        $endtime=new \DateTime($row['end_time']);
    }
    $summary=$row['owner'] . ": " .  $row['comment'];
    if (!empty($row['boat_category'])) {
        $summary .= "Der ros i " . $row['boat_category'];
    }
    
    $vEvent
        ->setDtStart(new \DateTime($row['start_time']))
        ->setDtEnd($endtime)
        ->setNoTime(false)
//        ->setLocation($row['location'])
        ->setSummary($summary);
    $vCalendar->addComponent($vEvent);    
}


header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="roaftaler.ics"');


echo $vCalendar->render();
} else {
    http_response_code(500);
}