<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="maxkammerater.csv"');

$s='SELECT Concat(Member.FirstName," ",Member.LastName) as roer,Member.MemberID as medlemsnummer,COUNT(distinct tmo.member_id) as rokammerater
FROM Member, Trip,TripMember tm, TripMember tmo
WHERE tm.TripID=Trip.id AND tm.member_id=Member.id AND tmo.TripID=Trip.id AND Member.id!=tmo.member_id
AND YEAR(Trip.OutTime)=YEAR(NOW())
GROUP By Member.id
ORDER BY rokammerater DESC
LIMIT 10;
';

$result=$rodb->query($s) or dbErr($rodb,$res,"maxmates");
process($result,"xlsx","maxmates","_auto");
