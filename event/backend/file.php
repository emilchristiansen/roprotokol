<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");




function sanestring($s) {
   $allowedchars=".:;@abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ01234567890_-#";
    $r="";
    for ($i=0; $i<100 && $i < strlen($s) ;$i++) {
        $c=$s[$i];
        if (strpos($allowedchars,$c)>=1){
            $r.=$c;
        }        
    }
    return $r;
} 

$forum=sanestring($_REQUEST['forum']);
$filename==sanestring($_REQUEST['file']);


$s="SELECT mime_type as mt,filename,file FROM forum_file WHERE forum=? AND filename=?";

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("ss", $forum,$filename);
    $stmt->execute();
    $result= $stmt->get_result() or die("Error in file query: " . mysqli_error($rodb));
    if ($result) {
        $file=$result->fetch_assoc();
        error_log("RES $forum, $filename:  ". print_r($file,true));
        header("Content-length: ".strlen($file['file']));
        header("Content-type: ".$file['mt']);
//        header("Content-Disposition: attachement; filename=\"".$file["filename"]."\"");
        header("Content-Disposition: inline; filename=\"".$file["filename"]."\"");
        echo $file['file'];
    } else {
        error_log("File not fould $filename");
    }
} else {
    dbErr($rodb,$res,"forum file");
}
