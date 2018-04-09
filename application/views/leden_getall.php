<?php
//VIEW

echo "<br>";
foreach($query as $rij) {
    foreach($rij as $field => $value){
    //print $rij->code;
    //print $rij->Lidnr;
        
    print "<b>". htmlentities($field) . "</b>= " . htmlentities($value) . " " ;
    
    }
    print "<BR>";
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
