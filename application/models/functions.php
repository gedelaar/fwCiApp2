<?php


/* MODEL
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of leden_model
 *
 * @author Gerard
 */
class functions extends CI_Model {
    //put your code here
    function get_seizoen()
    {
        $today = date("dmY");
       // echo $today;
        $thisYear = date("Y");
	//	echo $thisYear;
        if(date("3112".$thisYear) < $today) {
            //dan zit je in het tweede deel van het seizoen
            $seizoen = ($thisYear-1) . '-' . $thisYear;
        }
        else
        {
            $seizoen = $thisYear . '-' . ($thisYear+1);
        }
        return ($seizoen);
    }

    function get_startdatum()
    {
        $today = date("dmY");
        echo "today ". $today;
        
        $thisYear = date("Y");
        if(date("3112".$thisYear) > $today) {
            //dan zit je in het tweede deel van het seizoen
            $startjaar = $thisYear;
        }
        else
        {
            $startjaar = $thisYear-1;
        }
        $startdatum = date($startjaar."0101");
        echo "<br>" . $startdatum;
        return ($startdatum);
    }
    
}

?>
