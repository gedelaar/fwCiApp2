<?php // Load and parse the XML document 

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//bouw de query op
$qry='http://west.basketball.nl/db/xml/wedstrijd.pl'; 
$qry .= '?clb_ID=96';  //is forwodians
$qry .= '&alleen_club=1'; //enkel eigen club info
//$qry .= '&org_ID=2'; //nbb west, verwijderd omdat er ook rayon gespeeld wordt.
$qry .= '&sortering=datum';
$qry .= '&&szn_Naam='.$this->functions->get_seizoen(); //doe maar van dit seizoen.

$xml = simplexml_load_file($qry);
 
$title =  $xml->seizoen;
echo "<br>".$qry."<br>";
//print_r($xml);
//die;
?>
<html xml:lang="en" lang="en">
<head>
  <title><?php echo $title; ?></title>
</head>
<body>

<h1><?php echo $title; ?></h1>
<?php 
$startdatum = $this->functions->get_startdatum();
echo $startdatum;
?>
<?php

// Here we'll put a loop to include each item's title and description
foreach ($xml->competitie as $poule) {
    $source = $poule->wedstrijd->datum;
    $poule_date = new DateTime($source);
    //echo "<br>" . $poule_date->format('d-m-Y'); // 31-07-2012
    
    if($poule_date->format('Ymd') > $startdatum){
        echo "<br>".$poule->wedstrijd->datum->getName();
        echo "<br>".$poule->wedstrijd->datum;
        echo "<br>".$poule->wedstrijd->tijd;
        echo "<br>".$poule->nummer;
        echo "<br>".$poule->wedstrijd->thuisploeg->club;
        echo "<br>".$poule->wedstrijd->thuisploeg->teamafkorting;
        echo "<br>".$poule->wedstrijd->uitploeg->club;
        echo "<br>".$poule->wedstrijd->uitploeg->teamafkorting;        
        echo "<br>".$poule->wedstrijd->uitslag->scorethuis;
        echo "<br>".$poule->wedstrijd->uitslag->scoreuit;
        echo "<br>".$poule->wedstrijd->locatie->naam;
        echo "<br>".$poule->wedstrijd->locatie->adres;
        echo "<br>".$poule->wedstrijd->locatie->postcode;
        echo "<br>".$poule->wedstrijd->locatie->plaats;
        echo "<br>".$poule->wedstrijd->locatie->attributes()-> iss;
        echo "<br>".substr($poule->wedstrijd->nummer,-2);
        echo "<br>";
        
        $sql = "select * from wedstrijden ";
        $sql .= "where poule=".$poule->nummer." and code=".substr($poule->wedstrijd->nummer,-2);
        echo $sql;
        
    }
}

?>

</body>
</html>

