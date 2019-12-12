<?php
$_SERVER['DOCUMENT_ROOT']="/data/roprotokol/xx";
#include("../../rowing/backend/inc/common.php");
include("/data/roprotokol/rowing/backend/inc/common.php");
include("utils.php");

$member=7843;
$roer="Hans Peter Hansen";
require('fpdf.php');




$pdf = new FPDF( 'P', 'mm', 'A4' );


$pdf->AddPage();



$qm="SELECT CONCAT(FirstName,' ',LastName) as name FROM Member WHERE MemberID=?";
$mstmt = $rodb->prepare($qm) or die("bevis prep");
$mstmt->bind_param("s", $member);
$mstmt->execute() or die("bevis exe");
$mres=$mstmt->get_result();
$rower = $mres->fetch_assoc() or "$member findes ikke";
$rowername=$rower["name"];
//$mres->close();


$qr="SELECT MemberRight as r,argument,DATE(acquired) as acquired,showname FROM Member,MemberRights,MemberRightType
  WHERE MemberID=? AND Member.id=MemberRights.member_id
AND MemberRightType.member_right=MemberRights.MemberRight AND NOT (MemberRightType.arg <> MemberRights.argument)
AND MemberRightType.active>0 AND MemberRightType.category='roning'
ORDER BY MemberRights.acquired";
$rstmt = $rodb->prepare($qr) or die("bevis right prep ". mysqli_error($rodb));
$rstmt->bind_param("s", $member);
$rstmt->execute() or die("bevis right exe");
$rres=$rstmt->get_result();
$pdf->SetAuthor("Danske Studenters Roklub",true);
$pdf->SetCreator("roprotokollen",true);
$pdf->SetTitle("Rettigheder for DSR medlem",true);

$pdf->SetFont('Times','B',16);

$pdf->Image('../../../media/dsrlogo.jpg',null,null,200);
$pdf->Ln(10);
$pdf->Cell($w=20,0,utf8_decode("Rettigheder for medlem $rowername:"),$border=0, $ln=1,$align='L',$fill=null);
$pdf->Ln(13);
$pdf->SetFont('Helvetica','B',12);

while ($right = $rres->fetch_assoc()) {
    $pdf->Cell($w=0,8,utf8_decode($right["showname"]." ". $right["argument"]." ".$right["acquired"]),$border=0, $ln=1,$align='L',$fill=null);
}

$pdf->Ln(20);
$today=date('d/m Y',time());
$pdf->Cell($w=200,0,utf8_decode("For DSR den $today: ____________________"),$border=0, $ln=1,$align='C',$fill=null);




//header("Content-type: application/pdf");
//header("Content-Length: $len");
// header("Content-Disposition: inline; filename=hello.pdf");
$pdf->Output();
