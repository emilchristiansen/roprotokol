<?php
require("inc/common.php");
header('Content-type: application/json');

$s="SELECT JSON_OBJECT(
           'id', Boat.id,
           'name', Boat.Name,
           'spaces',BoatType.Seatcount,
           'description', Boat.Description,
           'category',BoatType.Name,
           'boattype', BoatCategory.Name,
           'location', Boat.Location,
           'brand',Boat.brand,
           'level',Boat.level) as json
    FROM Boat
         INNER JOIN BoatType ON (BoatType.id=BoatType)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)         
    WHERE 
         Boat.Decommissioned IS NULL
    GROUP BY Boat.id
    ";
//echo $s;
$result=$rodb->query($s) or die("Error in boats query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";	  
	  echo $row['json'];
}
echo ']';
$rodb->close();
