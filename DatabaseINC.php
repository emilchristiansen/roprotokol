<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// global $db;
include("config.php");
if(!isset($_SESSION))  session_start();
//session_register("DBPATH_session");
//  session_register("SorterEfter_session");
//  session_register("SortOrder_session");
//  session_register("SorterEfter_Boat_session");
//  session_register("SortOrder_boat_session");
//  session_register("navn_session");
//  session_register("Navn_session");
?>
<head>
<link rel="stylesheet" type="text/css" href="roprotokol.css">


<? 
$gDebugSw=true;
$blocal=false;

function get_key($key, $arr,$def=''){
    return isset($arr[$key])?$arr[$key]:$def;
}


function OpenDatabase() {
  extract($GLOBALS);

//  create connection to database
  $db0=new mysqli("localhost","root",$dbpw,"roprotokol");
  return $db0;
} 

function CloseDatabase() {
  extract($GLOBALS);
  mysql_close($db);
  $db=null;
  return $function_ret;
} 

function SendEmail($Postkasse)
{
  extract($GLOBALS);
  OpenDatabase();
  $rs_query=mysql_query("select mobilpostkasse from tblparm",$db);  
  $rs=mysql_fetch_array($rs_query);
  $s=("MobilPostkasse");
  $S=$S."@telia.dk;".$Postkasse."@telia.dk";
  print "Sending to : ".$S;
  closedatabase();
  return $function_ret;
} 

function IsAdminpswOK($Psw) {
  extract($GLOBALS);
  OpenDatabase();
  $rs=$rs_query=mysql_query(("select AdminPSW from tblparm"),$Db);  
  $rs=mysql_fetch_array($rs_query);
  $s=("adminPSW");
  
  if ($s==$psw) {
    $isadminpsw=1;
  } else {
    $isadminpsw=0;
  } 
  closedatabase();
  return $function_ret;
} 

//------------------------------------------------------ START
// Rostatistik
function Rostatistik($RS,$Subgroup,$medlid) {
  extract($GLOBALS);

  $Sorteringsarray[1]="Rank";
  $Sorteringsarray[2]="MembrID";
  $Sorteringsarray[3]="Name";
  $Sorteringsarray[4]="Rank";
  $Sorteringsarray[5]="Trips";
  $Sorteringsarray[6]="AvrLen";
  $Sorteringsarray[7]="HasRedKey";


  if ($Subgroup=="alle") {

?>

<table class="rostat" width=50%>
<tr>
	<td width="95%" class=DetailInfo valign="top"><center>
		<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="95%">
			<tr>
				<td>
					<table border=0>
					<tr><td><p class="almtekst">Find medlem:</p></td>
					<td><input id="medlnr" name="medlnr" type="text" size="4"></td>
					<td><input type="button" value="S�g" onClick="RedirIframe();">
					</td></tr></table>				
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
<br>

	
<SCRIPT LANGUAGE=JavaScript> 
<!-- 
function RedirIframe()
{
  MyMedlNr=document.getElementById("medlnr").value
  window.location.href  = "dsrlist.php?medlid=" + MyMedlNr + "&action=3#" + MyMedlNr; 
}
--> 
</SCRIPT>

<?php 
  } 

  $StrTableStart="<table class=\"rostat\" width=\"600\"><tr>";
  $AltStrTableStart="<table class=\"rostat\" width=\"600\"><tr>";

  $sorter="";
  /*if array_key_exists('SorterEfter',$_SESSION) {*/
 
    $sorter=$_SESSION["SorterEfter"];
    /*}*/
  if ($sorter=="Rank") {
    $SortSymbol="<img border=\"0\" src=\"images/Pilop.gif\" width=\"10\" height=\"10\">";
    if ( array_key_exists("SortOrder",$_SESSION) && $_SESSION["SortOrder"]==0)  {
      $SortSymbol="<img border=\"0\" src=\"images/Pilned.gif\" width=\"10\" height=\"10\">";
    } 
  } 

  $StrTableStart=$StrTableStart."<th class=\"tablehead\"><a href=\"rostat.php?rostataction=Rank"."&ID=0&subgroup=".$Subgroup."\" class=\"menupunkt3\">".$SortSymbol."Nr</a></th>";
  $AltStrTableStart=$AltStrTableStart."<th class=\"tablehead\" ></th>";

  $SortArrayNr=2;
  for ($f=0; $f<=4; $f=$f+1) {
    $SortSymbol="";
    if ($Sorteringsarray[$SortArrayNr]==$_SESSION["SorterEfter"]) {

      $SortSymbol="<img border=\"0\" src=\"images/Pilop.gif\" width=\"10\" height=\"10\">";
      if ($_SESSION["SortOrder"]==0) {
        $SortSymbol="<img border=\"0\" src=\"images/Pilned.gif\" width=\"10\" height=\"10\">";
      } 
    } 

    //error_log(" RS=".RS,0);

    // FIXME $Feltnavn=rs[$f].$Name;
    $Feltnavn='foo';
    if ($f==1) {
      $wStr="width=\"30%\"";
    } else {
      $wStr="width=\"14%\"";
    } 
    $StrTableStart=$StrTableStart."<th class=\"tablehead\"".$wStr."><a href=\"rostat.php?rostataction=".$Sorteringsarray[$SortArrayNr]."&ID=0&subgroup=".$Subgroup."\" class=\"menupunkt3\">".$SortSymbol.$Feltnavn."</a></th>";
    $AltStrTableStart=$AltStrTableStart."<th class=\"tablehead\"".$wStr."></th>";
    $SortArrayNr=$SortArrayNr+1;
  }

  $StrTableStart=$StrTableStart."</tr>";
  $AltStrTableStart=$AltStrTableStart."</tr>";

  print $StrTableStart;

  $AllRows=[];

  for ($i=0; $i<=count($AllRows); $i=$i+1) {
    if (($i%2)==0) {
      $rowhtml="<tr class=\"firstrow\">";
    } else {
      $rowhtml="<tr class=\"secondrow\">";
    } 
    $MemberID=$AllRows[0][$i];

    if (($MemberID)==($medlid)) {
      $rowhtml="<tr class=\"selectedrow\">";
    } 

    $rowhtml=$rowhtml."<td>".($i+1)."</td>";

//Find medlemsnummeret for denne r�kke

    $Fieldnumber=1;
    $Redkey="";
    if (count($AllRows)>4)
    {
      if ($AllRows[5][$i]==1) {
        $RedKey="<img src=\"images/icon_redwrench.gif\" border=0 alt=\"Har ikke deltaget i vintervedligehold\">";
      } 
    } 

    for ($Fieldnumber=0; $Fieldnumber<=count($AllRows)-1; $Fieldnumber=$Fieldnumber+1) {
      $Alignment="Left";
      if ($Fieldnumber>1) {
        $Alignment="Right";
      } 
      switch ($FieldNumber) {
        case 0:
        case 3:

          $rowhtml=$rowhtml."<td><p align=\"".$Alignment."\"><a name=\"".$MemberID."\" href=\"rostat.php?rostataction=ShowTrips&ID=".$MemberID."\" >".$AllRows[$Fieldnumber][$i]."</a></td>";
          break;
        case 1:

          $rowhtml=$rowhtml."<td><p align=\"".$Alignment."\"><a name=\"".$MemberID."\" href=\"rostat.php?rostataction=ShowTrips&ID=".$MemberID."\" >".$AllRows[$Fieldnumber][$i]."</a>".$redkey."</td>";
          break;
        default:

          $rowhtml=$rowhtml."<td><p align=\"".$Alignment."\">".$AllRows[$Fieldnumber][$i]."</td>";
          break;
      } 

    }


    $rowhtml=$rowhtml."</tr>";
    print $rowhtml;

    if ((($i+1)%25)==0) {

      print "</table>";
//Response.Write("<table>")
      print $AltStrTableStart;
    } 
  }
  print "</table>";
 } 

//------------------------------------------------------ 

function Baadstatistik($RS,$Subgroup)
{
  extract($GLOBALS);
  $Sorteringsarray[1]="RankB";
  $Sorteringsarray[2]="NameB";
  $Sorteringsarray[3]="TypeB";
  $Sorteringsarray[4]="RankB";
  $Sorteringsarray[5]="TripsB";
  $Sorteringsarray[6]="AvrLenB";

  print "<table class=\"rostat\" width=\"600\"><tr>";

  if ($_SESSION["SorterEfter_Boat"]=="RankB")
  {

    $SortSymbol="<img border=\"0\" src=\"images/Pilop.gif\" width=\"10\" height=\"10\">";
    if ($_SESSION["SortOrder_boat"]==0)
    {
      $SortSymbol="<img border=\"0\" src=\"images/Pilned.gif\" width=\"10\" height=\"10\">";
    } 
  } 

  print "<th class=\"tablehead\"><a href=\"rostatboat.php?rostataction=RankB"."&ID=0&subgroup=".$Subgroup."\" class=\"menupunkt3\">".$SortSymbol."Nr</a></th>";

  $SortArrayNr=2;
  foreach ($rs as $f) // FIXME
  {
    $SortSymbol="";
    if ($Sorteringsarray[$SortArrayNr]==$_SESSION["SorterEfter_Boat"])
    {

      $SortSymbol="<img border=\"0\" src=\"images/Pilop.gif\" width=\"10\" height=\"10\">";
      if ($_SESSION["SortOrder_boat"]==0)
      {
        $SortSymbol="<img border=\"0\" src=\"images/Pilned.gif\" width=\"10\" height=\"10\">";
      } 
    } 

    print "<th class=\"tablehead\"><a href=\"rostatboat.php?rostataction=".$Sorteringsarray[$SortArrayNr]."&ID=0&subgroup=".$Subgroup."\" class=\"menupunkt3\">".$SortSymbol.$f->name."</a></th>";
    $SortArrayNr=$SortArrayNr+1;
  }
  print "</tr>";

  while(!(($rs==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    print "<td>".$i."</td>";

//Find b�dnavnet for denne r�kke
    foreach ($rs as $f)
    {
      if ($f->name=="B�d")
      {
        $MemberID=$f->value;
      } 
    }

    $Fieldnumber=1;
    foreach ($rs as $f)
    {
      $Alignment="Left";
      if ($Fieldnumber>2)
      {
        $Alignment="Right";
      } 
      if ($f->name=="B�d" || $f->name=="Antal ture")
      {

        print "<td><p align=\"".$Alignment."\"><a href=\"rostat.php?rostataction=BoatSpecs&ID=".$MemberID."\" >".$f->value."</a></td>";
      }
        else
      {

        print "<td><p align=\"".$Alignment."\">".$f->Value."</td>";
      } 

      $Fieldnumber=$Fieldnumber+1;
    }

    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 
  print "</table>";
  

  return $function_ret;
} 

//------------------------------------------------------ 

function DMmotionsroning()
{
  extract($GLOBALS);


  $KmIAlt=0;
  $Roere=0;
  while(!(($rs==0)))
  {

    $KmIAlt=$KmIAlt+$rs["km"];
    $roere=$roere+1;
    $rs=mysql_fetch_array($rs_query);

  } 

  $Base=1000000;
  $OutputExp="";
  $Inputexp=$kmialt;
  $Separator=".";

  while(!($kmialt>$base))
  {

    $Base=$Base/1000;
  } 
  $OutputExp=(intval($Inputexp/$base));
  if ($kmialt>1000)
  {
    $OutputExp=$OutputExp.$separator;
  } 
  $Base=$Base/1000;

  while(!($Base<1))
  {

    if ($base==1)
    {
      $separator="";
    } 
    if ($kmialt>$base)
    {

      $OutputExp=$outputexp.substr("00".(intval($Inputexp/$base)),strlen("00".(intval($Inputexp/$base)))-(3)).$separator;
      $Inputexp=$Inputexp-intval($Inputexp/$base)*$base;
    } 

    $Base=$Base/1000;
  } 

?>
	<h2>Indberetning til DM i motionsroning</h2>
	<table border="0">
	<tr>
		<td><p>Samlet antal roede personkilometer:</p></td><td align="right"><p><b><?   echo $OutputExp;?></b></p></td>
	</tr>
	<tr>
	<td><p>Aktive roere i alt:</p></td><td align="right"><b><?   echo $roere;?></b></td> 
	</tr>
	<tr>
		<td><p>Personkilometer per aktiv roer:</p></td><td align="right"><p><b><?   echo intval($kmialt/$roere*100)/100;?></b></p></td> 
	</tr>
	</table>
	<p></p>
	<p>Det sidste tal indberettes �n gang om m�neden til DFfR. Tallet er opgjort pr. 01-<?   echo substr("0".(strftime("%m",time())),strlen("0".(strftime("%m",time())))-(2));?>-<?   echo strftime("%Y",strftime("%m/%d/%Y %H:%M:%S %p"));?></p>
	
	<? 

  return $function_ret;
} 

//------------------------------------------------------ 

//------------------------------------------------------ 

function Statistikoversigt()
{
  extract($GLOBALS);


  $rs_query=mysql_query(("SELECT Count(Tur.TurID) AS Antal FROM Tur"),$db);  
  $rs=mysql_fetch_array($rs_query);
  $Antalture=$rs["Antal"];
  $rs_query=mysql_query(("SELECT Sum(Tur.Meter) AS Meter FROM Tur"),$db);  
  $rs=mysql_fetch_array($rs_query);
  $AntalKmIalt=intval($rs["Meter"]/1000);
  $rs_query=mysql_query(("SELECT First(Tur.Ud) AS StanderhejsningsDato FROM Tur"),$db);  
  $rs=mysql_fetch_array($rs_query);
  $AntalRodage=$Datediff["d"][$rs["StanderhejsningsDato"]][time()];
  $AntalKmPrDag=intval($AntalKmIalt/$Antalrodage*10)/10;
  $AntalTurePrDag=intval($antalture/$antalrodage*10)/10;
  $rs_query=mysql_query(("SELECT Count(*) As AntalRoere from (SELECT TurDeltager.Navn FROM Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID GROUP BY TurDeltager.Navn)"),$db);  
  $rs=mysql_fetch_array($rs_query);
  $AntalRoere=$rs["AntalRoere"];
  $AntalTurePrRoer=intval($AntalTure/$AntalRoere*10)/10;
  $rs_query=mysql_query(("SELECT Format([Ud],'dd/mm/yy') AS Dato, Count(Tur.TurID) AS AntalTure FROM Tur GROUP BY Format([Ud],'dd/mm/yy') ORDER BY Count(Tur.TurID) DESC"),$db);  
  $rs=mysql_fetch_array($rs_query);
  $Flestturedato=$rs["Dato"];
  $FlestTureAntal=$rs["AntalTure"];
  $rs_query=mysql_query(("SELECT Count(Skade.SkadeID) AS Antal FROM Skade GROUP BY Skade.Repareret HAVING (((Skade.Repareret) Is Null))"),$db);  
  $rs=mysql_fetch_array($rs_query);
  $AntalSkader=$rs["Antal"];

  $RS2_query=mysql_query(("SELECT Gruppe.Navn AS B�dtype, Count(B�d.Navn) AS Boats, Count(qBoatsSkadet.Navn) AS Skadet FROM (Gruppe INNER JOIN B�d ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN qBoatsSkadet ON B�d.B�dID = qBoatsSkadet.FK_B�dID GROUP BY Gruppe.Navn"),$db);  
  $RS2=mysql_fetch_array($RS2_query);

  $RS3_query=mysql_query(("SELECT Hitcounter.Side, Count(Hitcounter.Side) AS Visninger FROM Hitcounter GROUP BY Hitcounter.Side"),$db);  
  $rs3=mysql_fetch_array($RS3_query);

?>

<h2>Oversigt over data i roprotokollen</h2><br><br>
	
	<h3>Roaktivitet</h3>
	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="400">
		<tr>
			<td width=60%>Antal ture i �r</td><td><?   echo $antalture;?></td>
		</tr>
		<tr>
			<td>Antal km roet i alt</td><td><?   echo $antalkmialt;?></td>
		</tr>
		<tr>
			<td>Antal rodage indtil nu</td><td><?   echo $antalrodage;?></td>
		</tr>
		<tr>
			<td>Antal km pr. dag</td><td><?   echo $antalkmprdag;?></td>
		</tr>
		<tr>
			<td>Antal ture pr. dag</td><td><?   echo $antaltureprdag;?></td>
		</tr>
		<tr>
			<td>Antal aktive roere</td><td><?   echo $antalroere;?></td>
		</tr>
		<tr>
			<td>Antal ture pr. roer</td><td><?   echo $AntalTurePrRoer;?></td>
		</tr>
		<tr>
			<td>Flest ture p� en dag</td><td><?   echo $flestturedato;?> (<?   echo $flesttureantal;?> ture)</td>
		</tr>
	</table>
	
	<h3>Skader</h3>
	
	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="400">
	<tr>
		<td width=60%>Antal skader lige nu</td><td><?   echo $antalskader;?><br></td>
	</tr>
	<tr>
		<td colspan=2>
			<br>
			<table class="rostat" width=100%>
			<tr>
				<th class="tablehead" width=33%>B�dtype</th><th class="tablehead" width=33%>Antal b�de</th><th class="tablehead" width=33%>Heraf skadet</th>
			</tr>	
<? 
  while(!(($rs2==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 

    print "<td>".$rs2["B�dtype"]."</td><td>".$rs2["Boats"]."</td><td>".$rs2["skadet"]."</td></tr>";
    $rs2=mysql_fetch_array($rs2_query);

  } 
?>				
				
			</table>
		</td>
	</tr>
	</table>

	<h3>Brugen af funktioner i roprotokollen</h3>
	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="400">
	<tr>
		<td colspan=2>
			<table class="rostat" width=100%>
			<tr>
				<th class="tablehead" width=50%>Side</th><th class="tablehead" width=50%>Antal visninger</th>
			</tr>	
<? 
  while(!(($rs3==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 

    print "<td>".$rs3["Side"]."</td><td>".$rs3["Visninger"]."</td></tr>";
    $rs3=mysql_fetch_array($rs3_query);

  } 
?>				
				
			</table>
		</td>
	</tr>
	</table>
	
	<? 


  return $function_ret;
} 

//------------------------------------------------------ 

function SkadedeBaade($RS)
{
  extract($GLOBALS);


  $BreddeArray[1]="10%";
  $BreddeArray[2]="20%";
  $BreddeArray[3]="10%";
  $BreddeArray[4]="10%";
  $BreddeArray[5]="40%";
  $BreddeArray[6]="10%";

  print "<table class=\"rostat\" width=\"700\"><tr>";

  $Fnumber=0;
  foreach ($rs as $f)
  {
    $TempNavn=$f->name;
    if ($tempnavn!="BoatID")
    {

      if ($tempnavn=="SkadeID")
      {
        $tempnavn="Klarmeld";
      } 
      print "<th class=\"tablehead\" width=".$BreddeArray[$Fnumber].">".$TempNavn."</th>";
    } 

    $Fnumber=$fnumber+1;
  }
  print "</tr>";

  while(!(($rs==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    foreach ($rs as $f)
    {
      if ($f->name=="BoatID")
      {

        $boatid=$f->value;

      }
        else
      {


        $BColor="<td>";
        $Alignment="Left";
        if ($f->name=="Grad")
        {

          switch ($f->value)
          {
            case 1:
              $Secondfield="<img border=\"0\" src=\"images/icon_skadet1.gif\" width=\"16\" height=\"17\">  Let";
              break;
            case 2:
              $Secondfield="<img border=\"0\" src=\"images/icon_skadet2.gif\" width=\"16\" height=\"17\">  Middel";
              break;
            case 3:
              $Secondfield="<img border=\"0\" src=\"images/icon_skadet3.gif\" width=\"16\" height=\"16\">  Sv�r";
              break;
          } 
        } 


        if ($f->name=="SkadeID")
        {

          print "<td><p align=\"".$Alignment."\"><a href=\"klarmeld.php?Origin=SkadedeB�de&boatid=".$BoatID."&skadeid=".$f->value."\"><u>[Klarmeld]</u></a></td>";
        }
          else
        if ($f->name=="Grad")
        {

          print "<td>".$secondfield."</td>";
        }
          else
        {

          print $Bcolor."<p align=\"".$Alignment."\">".$f->Value."</td>";
        } 


      } 


    }

    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 
  print "</table>";
  

  return $function_ret;
} 

//------------------------------------------------------ 

function BaadePaaVandet($RS)
{
  extract($GLOBALS);



  $BreddeArray[1]="20%";
  $BreddeArray[2]="10%";
  $BreddeArray[3]="10%";
  $BreddeArray[4]="30%";
  $BreddeArray[5]="10%";
  $BreddeArray[6]="20%";


  print "<table class=\"rostat\" width=\"750\"><tr>";

  foreach ($rs as $f)
  {
    print "<th class=\"tablehead\" width=".$BreddeArray[$Fnumber].">".$f->name."</th>";
  }
  print "</tr>";

  $Fieldnumber=1;
  while(!(($rs==0)))
  {



    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    foreach ($rs as $f)
    {

      $TempValue=$f->value;

      $Alignment="Left";
      if ($f->name=="Grad") {
        $Alignment="Right";
      } 

      if ($f->name=="TurID") {

        print "<td><p align=\"".$Alignment."\"><a href=\"rostat.php?rostataction=TripSpecs&ID=".$f->value."\" >".$f->value."</a></td>";
      }
        else
      {
        print "<td><p align=\"".$Alignment."\">".$tempValue."</td>";
      } 
    }

    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

    $Fieldnumber=$Fieldnumber+1;
  } 
  print "</table>";
  

  return $function_ret;
} 

//------------------------------------------------------ 

function Rovagt($rsv) {
  extract($GLOBALS);

  $i=0;
  
  while ($rw = $rsv->fetch_array(MYSQLI_ASSOC)) {
    if ($i==0) {
      print "<table class=\"rostat\"><tr>";
      foreach (array_keys($rw) as $f) {
	if ($f != "Tilg�ngelig") {
	  print "<th class=\"tablehead\">".$f."</th>";
	} 
      }
    }
    print "</tr>\n";      
    $i=$i+1;
    if (! preg_match("/l�nt b�d/",$rw["Navn"])) {
      if (($i%2)==0) {
	print "<tr class=\"firstrow\">";
      } else {
	print "<tr class=\"secondrow\">";
      }       
      foreach (array_keys($rw) as $f) {
	if ($f != "Tilg�ngelig") {
	  $Alignment="Left";
	  print "<td><p align=\"".$Alignment."\">".$rw[$f]."</td>";
	} 
      }      
      print "</tr>\n";
    }
  } 
  print "</table>";
}


//------------------------------------------------------ 

function Rovagt_printer($RS)
{
  extract($GLOBALS);


  if (substr($rs["Navn"],strlen($rs["Navn"])-(8))=="l�nt b�d")
  {
    $rs=mysql_fetch_array($rs_query);

  } 

  print "<table bordercolor=\"#111111\" border=1 style=\"border-collapse: collapse\" width=310><tr>";
  print "<th width=\"40%\">B�d</th><th width=\"60%\">Tildelt</th></tr>";

  while(!(($rs==0)))
  {

    print "<tr><td>".$rs["Navn"]."</td><td></td></tr>";
    $rs=mysql_fetch_array($rs_query);

  } 

  print "</table>";

  return $function_ret;
} 

//------------------------------------------------------ 

function DagensTure($RS)
{
  extract($GLOBALS);



?><table class="rostat" width=700><tr>
<th class="tablehead" width="5%">Tur</th>
<th class="tablehead" width="10%">B�d</th>
<th class="tablehead" width="20%">Destination</th>
<th class="tablehead" width="10%">Ud</th>
<th class="tablehead" width="10%">Ind</th>
<th class="tablehead" width="10%">Forv. ind</th>
<th class="tablehead" width="25%">Roere</th>
</tr>
<? 


  while(!(($rs==0))) {


    if (($i%2)==0) {
      print "<tr class=\"firstrow\">";
    } else {
      print "<tr class=\"secondrow\">";
    } 


    $lastturid=$rs["turid"];
    $thisturid=$lastturid;

    print "<td><p align=\"left\"><a href=\"rostat.php?rostataction=TripSpecs&ID=".$rs["TurID"]."\" >".$rs["TurID"]."</a></td>";
    print "<td>".$rs["b�d"]."</td>";
    print "<td><p align=\"left\">".$rs["Destination"]."</td>";
    print "<td><p align=\"Right\">".substr($rs["udtid"],0,5)."</td>";
    print "<td><p align=\"Right\">".substr($rs["indtid"],0,5)."</td>";
    if ($rs["indtid"]="") {
      print "<td><p align=\"Right\">".substr($rs["forv inde"],0,5)."</td>";
    } else {
      print "<td></td>";
    }
    print "<td>";
    while(!($lastturid!=$thisturid))
    {

      print "<a href=\"rostat.php?rostataction=ShowTrips&ID=".substr($rs["roer"],0,(strpos($rs["roer"]," ") ? strpos($rs["roer"]," ")+1 : 0))."\" >".$rs["roer"]."</a><br>";

      $rs=mysql_fetch_array($rs_query);

      if (($rs==0))
      {

        $thisturid="slut";
      }
        else
      {

        $thisturid=$rs["turid"];
      } 

    } 

    print "</td></tr>";
    $i=$i+1;

  } 

  print "</table>";
  

  return $function_ret;
} 

//------------------------------------------------------ 

function RoerensStamdata($RS)
{
  extract($GLOBALS);


?>
<form method="POST" action="rettelser.php?Postback=2&Rtype=2&SlaaOpKnap=Sl� op&MemberID=<?   echo $RS[0];?>" id=form2 name=form2> 
<table class="rostat">
<tr>
	<td width="25%"><font color="<?   echo truefalse($rs["Roret"]);?>">Roret</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["TeoretiskStyrmandKursus"]);?>">Teor. styrmandskursus</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["ScullerInstruktoer"]);?>">Scullerinstrukt�r</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Kajak"]);?>">Kajakret</font></td>
</tr>
<tr>
	<td width="25%"><font color="<?   echo truefalse($rs["RoInstruktoer"]);?>">Instrukt�r</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Styrmand"]);?>">Styrmand</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Svava"]);?>">Svavaret</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["KajakInstruktoer"]);?>">Kajakinstrukt�r</font></td>
</tr>
<tr>
	<td width="25%"><font color="<?   echo truefalse($rs["StyrmandInstruktoer"]);?>">Styrmandsinstrukt�r</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Langtur"]);?>">Langtursstyrmand</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Sculler"]);?>">Scullerret</font></td>
</tr>
<tr>
	</td>
	<td width="25%"><font color="<?   echo truefalse($rs["Ormen"]);?>">Gig 8-er styrmand</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Kaproer"]);?>">Kaproer</font></td>
	<td width="25%"><font color="<?   echo truefalse($rs["Motorboat"]);?>">Motorb�dsret</font></td>
	<td colspan=4 align=right>
		<input type="submit" value="Indberet rettelse" name="Rettelse2">
	<td>
</tr>
</table>
</form>


<? 

  return $function_ret;
} 

//------------------------------------------------------ 

function RoerensRettedeTure($RS,$ShowRettelseSpecs)
{
  extract($GLOBALS);



?>

<h3>Rettede ture</h3>
<table class="rostat" width="600">
<tr>
	<th class="tablehead">TurID</th>
	<th class="tablehead">Info om turen</th>
	<th class="tablehead">Indberettet af</th>
	<th class="tablehead">Status</th>
	<th class="tablehead">Beskrivelse</th>
</tr>
<? 
  while(!(($rs==0)))
  {


    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 

    $i=$i+1;

?>
	<td><?     echo $rs["fk_turid"];?></td>
	<td><?     echo $rs["Dest"]."<br>".$rs["B�d"]."<br>".$rs["OprettetDato"];?></td>
	<td>
		<? 
    if (!isset($rs["Indberetter"]))
    {

      print "-";
    }
      else
    {

      print $rs["Indberetter"];
    } 

?>
	</td>
	<td>
		<? 
    if (!isset($rs["Fixed_comment"]))
    {

      print "-";
    }
      else
    {

      print $rs["Fixed_comment"];
    } 

?>
	</td>	
	<td>
		<?     if (($rs["fejlid"])==($ShowRettelseSpecs))
    {
?>
		<a href="rostat.php?rostataction=ShowTrips&ID=<?       echo $rs["Medlemsnr"];?>">[Skjul]</a>
		<?     }
      else
    {
?>
		<a href="rostat.php?rostataction=ShowTrips&ID=<?       echo $rs["Medlemsnr"];?>&ShowRettelseSpecs=<?       echo $rs["FejlID"];?>">[Vis]</a>
		<?     } ?>
	</td>
</tr>
<? 
    if (($rs["fejlid"])==($ShowRettelseSpecs))
    {


      $MyClass="firstrow";
      if (($i%2)==0)
      {
        $MyClass="secondrow";
      } 

?>
		<tr class="<?       echo $Myclass;?>">
		  <td></td><td colspan="4">
		  <table width=100%>
			<tr>
				<td width=100% valign="top" colspan=4><b>
				<b>Beskrivelse af  rettelsen<br>
				<hr noshade color="#000000" size="1">
				</b>
				</td>
			</tr>			
			<tr>
				<td width=17% valign="top"><b>
				Tur slettes<br>
				B�d<br>
				Ud<br>
				Ind<br>
				Destination<br>
				Distance<br>
				Turtype<br>
				</b></td>
				<td width=33% valign="top">
				<? 
      if ($rs["Slettur"]="False")
      {
?>Nej<? 
      }
        else
      {

?>Ja<? 
      } 

?><br>				
				<?       echo $rs["B�d"];?><br>				
				<?       echo $rs["Ud"];?><br>				
				<?       echo $rs["Ind"];?><br>				
				<?       echo $rs["Destination"];?><br>				
				<?       echo $rs["Distance"];?> km<br>				
				<?       echo $rs["Turtype"];?><br>					
				</td>

				<td width=17% valign="top"><b>
				Styrmand<br>
				Roere<br>
				</b></td>
				<td width=33% valign="top">
				<?       echo $rs["TurDeltager0"];?><br>
				<?       echo $rs["TurDeltager1"];?><br>
				<?       echo $rs["TurDeltager2"];?><br>
				<?       echo $rs["TurDeltager3"];?><br>
				<?       echo $rs["TurDeltager4"];?><br>
				<?       echo $rs["TurDeltager5"];?><br>
				<?       echo $rs["TurDeltager6"];?><br>
				<?       if ($rs["Turdeltager7"]!="")
      {
?>
				<?         echo $rs["TurDeltager7"];?><br>
				<?         echo $rs["TurDeltager8"];?><br>
				<?       } ?>
				</td>
			</tr>
			<tr>
				<td width=17% valign="top"><b>
				<b>�rsag</b>
				</td>
				<td width=83% valign="top" colspan=3>
				<?       echo $rs["�rsag til rettelsen"];?><br>
				</td>
			</tr>
		  </table>
		  </td>
		</tr>
		<? 
    } 



    $rs=mysql_fetch_array($rs_query);

  } 
?>
</table>
<? 

  return $function_ret;
} 

//------------------------------------------------------ 

function TrueFalse($Inputdate)
{
  extract($GLOBALS);


  if (!isset($inputdate))
  {

    $function_ret="#CFCFCF";
  }
    else
  {

    $function_ret="Black";
  } 


  return $function_ret;
} 

//------------------------------------------------------ 

function Turoversigt($RS)
{
  extract($GLOBALS);


  print "<h3>Turoversigt</h3>";

  print "<table class=\"rostat\" width=\"600\"><tr>";


  for ($FieldNr=0; $FieldNr<=4; $FieldNr=$FieldNr+1)  {
// FIXME    print "<th class=\"tablehead\">".($FieldNr)->$name."</th>";
    print "<th class=\"tablehead\">".($FieldNr).$name."</th>";

  }


  print "</tr>";
  while(!(($rs==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {


      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


     // FIXME print "<td><a href=\"rostat.php?rostataction=TripSpecs&ID=".(0)->$value."\" >".(0)->$value."</a></td>";
    print "<td><a href=\"rostat.php?rostataction=TripSpecs&ID=".(0).$value."\" >".(0).$value."</a></td>";
    //print "<td><a href=\"rostat.php?rostataction=BoatSpecs&ID=".(1)->$value."\" >".(1)->$value."</a></td>";
    print "<td><a href=\"rostat.php?rostataction=BoatSpecs&ID=".(1).$value."\" >".(1).$value."</a></td>";
    for ($FieldNr=2; $FieldNr<=4; $FieldNr=$FieldNr+1)
    {
      // FIXME print "<td>".($FieldNr)->$value."</td>";
      print "<td>".($FieldNr).$value."</td>";

    }


    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 
  print "</table>";

  
  return $function_ret;
} 

//------------------------------------------------------ 

function BaadensTuroversigt($RS)
{
  extract($GLOBALS);


// FIXME  print "<h3>Turoversigt for ".(6)->$value." (".(5)->$value.")</h3>";
  print "<h3>Turoversigt for ".(6).$value." (".(5).$value.")</h3>";

  print "<table class=\"rostat\" width=\"600\">";

  for ($FieldNr=0; $FieldNr<=4; $FieldNr=$FieldNr+1)
  {
   // FIXME print "<th class=\"tablehead\">".($FieldNr)->$name."</th>";
 print "<th class=\"tablehead\">".($FieldNr).$name."</th>";

  }


  print "</tr>";
  while(!(($rs==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    // FIXME print "<td><a href=\"rostat.php?rostataction=TripSpecs&ID=".(0)->$value."\" >".(0)->$value."</a></td>";
    print "<td><a href=\"rostat.php?rostataction=TripSpecs&ID=".(0).$value."\" >".(0).$value."</a></td>";
    // FIXMEprint "<td><a href=\"rostat.php?rostataction=ShowTrips&ID=".(7)->$value."\" >".(1)->$value."</a></td>";
    print "<td><a href=\"rostat.php?rostataction=ShowTrips&ID=".(7).$value."\" >".(1).$value."</a></td>";
    for ($FieldNr=2; $FieldNr<=4; $FieldNr=$FieldNr+1)
    {
      // FIXME print "<td>".($FieldNr)->$value."</td>";
     print "<td>".($FieldNr).$value."</td>";

    }


    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 
  print "</table>";

  

  return $function_ret;
} 

//------------------------------------------------------ 

function Turspecifikation($RS)
{
  extract($GLOBALS);


  print "<h2>Turoversigt</h2>";

  // FIXME print "<p>".(8)->$value." nr. ".(0)->$Value." til ".substr((2)->$value,0,(strpos((2).$value," (",1) ? strpos((2).$value," (",1)+1 : 0))." i ".(1)->$value." den ".(3)->$value." (".(4)->$value." km)</p>";
  print "<p>".(8).$value." nr. ".(0).$Value." til ".substr((2).$value,0,(strpos((2).$value," (",1) ? strpos((2).$value," (",1)+1 : 0))." i ".(1).$value." den ".(3).$value." (".(4).$value." km)</p>";

?>
<form method="POST" action="rettelser.php?Postback=1&Rtype=1&SlaaOpKnap=Sl� op&TurID=<?   echo $RS["TurID"];?>"> 
<input type="submit" value="Indberet rettelser til turen" name="Rettelse">
</form>
<? 

  print "<p>Tur start: ".("Ud")."<br>";
  print "Forventet ind: ".("forvind")."<br>";
  print "Tur slut: ".("ind")."<br><br>";

  print "<table class=\"rostat\" width=\"600\"><tr>";

  for ($FieldNr=5; $FieldNr<=6; $FieldNr=$FieldNr+1)
  {
    // FIXMEprint "<th class=\"tablehead\">".($FieldNr)->$name."</th>";
    print "<th class=\"tablehead\">".($FieldNr).$name."</th>";

  }


  $Bemaerk=("Kommentar");

  print "</tr>";
  while(!(($rs==0))) {
    $i=$i+1;
    if (($i%2)==0) {
      print "<tr class=\"firstrow\">";
    } else {
      print "<tr class=\"secondrow\">";
    } 

    // FIXME$MemberID=(5)->$value;
    $MemberID=(5).$value;
    // FIXME print "<td><a href=\"rostat.php?rostataction=ShowTrips&ID=".$MemberID."\">".(5)->$value."</a></td>";
    // FIXME print "<td><a href=\"rostat.php?rostataction=ShowTrips&ID=".$MemberID."\">".(6)->$value.(7)->$value."</a></td>";

    print "<td><a href=\"rostat.php?rostataction=ShowTrips&ID=".$MemberID."\">".(5).$value."</a></td>";
    print "<td><a href=\"rostat.php?rostataction=ShowTrips&ID=".$MemberID."\">".(6).$value.(7).$value."</a></td>";

    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 
  print "</table>";

  print "<p><br><strong>Bem�rkning: </strong>".$Bemaerk."<br></p>";
  print "<FORM><INPUT TYPE=\"button\" VALUE=\"Tilbage\" onClick=\"history.go(-1)\"></FORM>";
  return $function_ret;
} 

//------------------------------------------------------ 

function Turtypesummary($RS) {
  extract($GLOBALS);
  print "<h3>Oversigt fordelt p� turtyper</h3>";
  print "<table class=\"rostat\" width=\"600\">";
  for ($FieldNr=0; $FieldNr<=3; $FieldNr=$FieldNr+1) {
    // FIXME print "<th class=\"tablehead\">".($FieldNr)->$name."</td>";
    print "<th class=\"tablehead\">".($FieldNr).$name."</td>";

  }

  $AntalTure=0;
  print "</tr>";
  while(!(($rs==0))) {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    $Extra="";
    for ($FieldNr=0; $FieldNr<=3; $FieldNr=$FieldNr+1)
    {
      if ($FieldNr==2)
      {
        $Extra=" km";
      } 
      // FIXMEprint "<td>".($FieldNr)->$value.$Extra."</td>";
      print "<td>".($FieldNr).$value.$Extra."</td>";

    }


    // FIXME$AntalTure=$AntalTure+(1)->$value;
    $AntalTure=$AntalTure+(1).$value;
    // FIXME $Samletlaengde=$Samletlaengde+(2)->$value;
    $Samletlaengde=$Samletlaengde+(2).$value;
    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 

  print "<td class=\"fremh\">I alt</td>";
  print "<td class=\"fremh\">".$antalture."</td>";
  print "<td class=\"fremh\">".$samletlaengde." km</td>";
  print "<td class=\"fremh\">".(intval($samletlaengde/$antalture*10)/10)." km</td>";

  print "</table>";

  

  return $function_ret;
} 

//------------------------------------------------------ 

function Skadesoversigt($RS)
{
  extract($GLOBALS);


  print "<P>&nbsp;</P>";

  print "<br><h2>Seneste skader</h2>";

  print "<table class=\"rostat\" width=\"600\"><tr>";

  for ($FieldNr=1; $FieldNr<=4; $FieldNr=$FieldNr+1)
  {
    // FIXME print "<th class=\"tablehead\">".($FieldNr)->$name."</th>";
    print "<th class=\"tablehead\">".($FieldNr).$name."</th>";

  }


  $Skade=1; //Der vises max. 10 skader
  print "</tr>";
  while(!(($rs==0) || $Skade==10))
  {

    $i=$i+1;
    if (($i%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    for ($FieldNr=1; $FieldNr<=4; $FieldNr=$FieldNr+1)
    {
      // FIXME$fValue=($FieldNr)->$value;
      $fValue=($FieldNr).$value;
      if ($FieldNr==4 && strlen($fValue)>1)
      {
        $fValue="Ja";
      } 
      print "<td>".$Fvalue."</td>";

    }


    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

    $Skade=$Skade+1;
  } 
  print "</table>";

  

  return $function_ret;
} 

//------------------------------------------------------ END

// List The Recordset
function ListRS($RS)
{
  extract($GLOBALS);


  print "<table class=\"rostat\" width=\"750\"><tr>";
  foreach ($rs as $f) // FIXME
  {
    print "<th class=\"tablehead\">".$f->name."</th>";
  }

  print "</tr>";
  while(!(($rs==0)))
  {

    $i=$i+1;
    if (($i%2)==0)
    {


      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 


    foreach ($rs as $f) // FIXME
    {
      print "<td>".$f->Value."</td>";
    }

    print "</tr>";
    $rs=mysql_fetch_array($rs_query);

  } 
  print "</table>";
  

  return $function_ret;
} 
?><? 
// Her kommer s� de DSR specifikke rutiner
// Regner med databasen er �ben.
//----------------------------------------------------
function LockBoat($boatid)
{
  extract($GLOBALS);

LockRemoveInactive();
  $rs_query=mysql_query(("select * from l�steb�de where BoatID=".$boatid),$db);  
$rs=mysql_fetch_array($rs_query);
  if (($rs==0))  {
  $navn=$mysqli->real_escape_string($_SESSION["Navn"]);

  $db->query("Insert into L�steb�de (boatid,KlientNavn) values ($boatid,$navn)",$db); // FIXME
    $function_ret=true;
  } else {
    $function_ret=false;
    print "<img border=\"0\" src=\"images/icon_laast.gif\" width=\"16\" height=\"17\">  B�den er ved at blive udskrevet.";
  } 

  $rs=null;
  return $function_ret;
} 
?><? 
function LockRemoveInactive()
{
  extract($GLOBALS);

// Response . write("Delete from 
// l�steb�de where locktimeout now or KlientNavn=  session(Navn)  )

  if (isset($_SESSION["Navn"])) {
    $navn=mysqli_real_escape_string($_SESSION["Navn"]);
  } else {
    $navn="";
  }
  $db->query("Delete FROM L�steB�de WHERE locktimeout < now() OR KlientNavn=$navn");  
  //NEL close?  return $function_ret;
} 

function GetMedlemNameID($ID)
{
  extract($GLOBALS);

  $rs=$rs_query=mysql_query(("Select  [Fornavn] & \" \" & [Efternavn] as Navn from Medlem Where MedlemID=".$ID),$db);  
$rs=mysql_fetch_array($rs_query);
;  if (!($rs==0))
  {

    $res=$Rs["Navn"];
  }
    else
  {

    $res="Ikke fundet";
  } 

  $function_ret=$Res;
  
  $rs=null;

  return $function_ret;
} 

function GetMedlemNameNR($MedlemsNr)
{
  extract($GLOBALS);

  $rs=$rs_query=mysql_query(("Select  [Fornavn] & \" \" & [Efternavn] as Navn from Medlem Where MedlemsNr=".$Nr),$db);  
$rs=mysql_fetch_array($rs_query);
;  if (!($rs==0))
  {

    $res=$Rs["Navn"];
  }
    else
  {

    $res="Ikke fundet";
  } 

  $function_ret=$Res;
  
  $rs=null;

  return $function_ret;
} 

function GetBoatNameID($ID)
{
  extract($GLOBALS);

  if ($ID>0)
  {

    $rs=$rs_query=mysql_query(("Select Navn from B�d Where B�dID=".$ID),$db);    
$rs=mysql_fetch_array($rs_query);
;    if (!($rs==0))
    {

      $res=$Rs["Navn"];
    }
      else
    {

      $res="Ikke fundet";
    } 

    $function_ret=$Res;
    
    $rs=null;

  } 

  return $function_ret;
} 

function GetSkadeBeskrivelse($SkadeNr)
{
  extract($GLOBALS);


  $rs=$rs_query=mysql_query(("Select Beskrivelse from Skade Where SkadeID=".$SkadeNr),$db);  
$rs=mysql_fetch_array($rs_query);
;  if (!($rs==0))
  {

    $res=$Rs["Beskrivelse"];
  }
    else
  {

    $res="Ikke fundet";
  } 

  $function_ret=$Res;
  
  $rs=null;


  return $function_ret;
} 

function SkadeString($SkadeNr)
{
  extract($GLOBALS);

  switch ($SkadeNr)
  {
    case 1:
      $function_ret="Let skadet";
      break;
    case 2:
      $function_ret="Middel skadet";
      break;
    case 3:
      $function_ret="Sv�rt skadet";
      break;
    default:
      $function_ret="??";
      break;
  } 
  return $function_ret;
} 

function Change2DBBool($InBool)
{
  extract($GLOBALS);


  $function_ret=0;
  if ($inbool=="ON" || $inbool=="on")
  {
    $function_ret=-1;
  } 

  return $function_ret;
} 

function Change2ASPBool($InBool)
{
  extract($GLOBALS);


  $function_ret="Checked";
  if (!isset($inbool))
  {
    $function_ret="";
  } 

  return $function_ret;
} 

function RemovePing($Instring) {
  extract($GLOBALS);
  for ($c1=1; $c1<=strlen($instring); $c1=$c1+1) {
    if (substr($instring,$c1-1,1) != "%27") {
      $function_ret=$RemovePing+substr($instring,$c1-1,1);
    } 
  }
  return $function_ret;
} 

function WriteHit($AffPage,$AffItem="") {
  extract($GLOBALS);
  //  $tid="#".time()."#";
  // $db->query("INSERT INTO Hitcounter ([Side], [Item], [Timestamp]) VALUES ($Affpage,$AffItem,$tid);");
} 

?></head>
