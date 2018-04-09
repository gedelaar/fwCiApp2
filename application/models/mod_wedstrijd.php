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
class mod_wedstrijd extends CI_Model {

    public $table;

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->table = "wedstrijden";
        $this->table2 = "wedstrijden_verplaatst";
        $this->output->enable_profiler(TRUE);
    }

    /*
      te starten met http://www.forwodians.nl/fwBar/index.php/leden/put_xml_to_sql
     */

    function put_xml_to_sql($mode) {
        //put your code here
        $wFile = "/tmp/mutaties.txt";

        //$qry='http://west.basketball.nl/db/xml/wedstrijd.pl'; 
        $qry = 'http://db.basketball.nl/db/xml/wedstrijd.pl';
        if ($mode === "fw") {
            $qry .= '?clb_ID=96';  //is forwodians
        }
        if ($mode === "nw") {
            $qry .= '?clb_ID=65';  //is noordwijk		
            $qry .= '&&plg_ID=626'; //is M4 1
        }
        $qry .= '&alleen_club=1'; //enkel eigen club info
        //$qry .= '&org_ID=2'; //nbb west, verwijderd omdat er ook rayon gespeeld wordt.
        $qry .= '&sortering=datum';
        //$qry .= '&&szn_Naam='.$this->functions->get_seizoen(); //doe maar van dit seizoen.
        echo "<br>inleesadres = " . $qry . "<br>";

        $xml = simplexml_load_file($qry);
        //print $xml->saveXML();
        //die;
        $startdatum = $this->functions->get_startdatum();
        echo "<br>startdatum = " . $startdatum;

        // Here we'll put a loop to include each item's title and description
        foreach ($xml->competitie as $single) {
            foreach ($single->wedstrijd as $poule) {
                // print_r($poule);
                //$source = $poule->wedstrijd->datum;
                $source = $poule->datum;
                $poule_date = new DateTime($source);
                echo "<br>" . $poule_date->format('d-m-Y'); // 31-07-2012
                echo "<br>" . $poule_date->format('Ymd'); // 31-07-2012

                if ($poule_date->format('Ymd') > $startdatum) {
                    /*                echo "<br>".$poule->wedstrijd->datum->getName();
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
                      echo "<br>"; */

                    //$sql = "select * from wedstrijden ";
                    //$sql .= "where poule='".$poule->nummer."' and code='".substr($poule->wedstrijd->nummer,-2)."'";
                    //$this->db->_reset_select();
                    $this->db->select('*');
                    $this->db->from($this->table);
                    $this->db->where('poule', strval($single->nummer));
					//echo "<br>".$poule->nummer;
                    //$hlp_code = substr($poule->nummer, -2);
					$arr_code = explode("-", $poule->nummer);
					$hlp_code = $arr_code[1];
					//echo "<br>".$hlp_code;die;
                    $this->db->where("BINARY code =  '" . $hlp_code . "'", null, false);
                    $query = $this->db->get();
					
                    //debug_last_query();
					//echo "<br>";
                    //debug_query_result($query);
                    //die;
                    //        echo $query;
                    $wedstrijd_verplaatst = false;
                    $blnmut = false;
                    $barmut = false;
                    //$code="";
                    if ($query->num_rows() > 0) {
                        $blnmut = true; //dan updaten we alles behalve de verwijderde wedstrijden.
                        //dus het record bestaat, bepaal of er een verschil is in datum tijd
                        if ($query->row('datum') <> strval($poule->datum)) {
                            $wedstrijd_verplaatst = true;
                            echo "<br>datumxx " . $query->row('datum') . " == " . strval($poule->datum);
                            $blnmut = true;
                            //			$code = "datum";
                            //$this->filewriter->write($wFile,"datum wzg");
                            $barmut = true;
                        }
                        if ($query->row('tijd') <> strval($poule->tijd)) {
                            echo "<br>tijdxx " . $query->row('tijd') . " == " . strval($poule->tijd);
                            $wedstrijd_verplaatst = true;
                            $blnmut = true;
                            $barmut = true;
                            echo "<br>row-tijd = " . $query->row('tijd');
                            echo "<br>poule-tijd = " . $poule->tijd;
                            //			$code .= "tijd";
                        }
                        if ($query->row('thuis') <> strval($poule->thuisploeg->club . " " . $poule->thuisploeg->teamafkorting)) {
                            $blnmut = true;
                            //			$code .= "thuis";
                        }
                        if ($query->row('uit') <> strval($poule->uitploeg->club . " " . $poule->uitploeg->teamafkorting)) {
                            $blnmut = true;
                            //			$code .= "uit";
                        }
                        if ($query->row('accommodatie') <> strval($poule->locatie->naam)) {
                            //			echo "qry accommodatie = " .$query->row('accommodatie') . " versus " . strval($poule->wedstrijd->locatie->naam);
                            $blnmut = true;
                            //			$code .= "acc";
                        }
                        $uitslag = $poule->uitslag->scorethuis . "-" . $poule->uitslag->scoreuit;
                        if (($uitslag) == '0-0') {
                            $uitslag = "";
                        }
                        if ($query->row('uitslag') <> $uitslag) {
                            $blnmut = true;
                        }
                        // gelijk maar refs eraan koppelen
                    }
                    //	echo "datum = ". $query->row('datum');
                    //		echo "<br>code = ".$code;
                    //die;
                    if ($wedstrijd_verplaatst) {
                        echo "wedstrijd verplaatst";
                        echo "<br>poule = " . $poule->nummer;
                        echo "<br>code = " . substr($poule->nummer, -2);
                        $this->load->helper('date');
                        $datestring = "%Y-%m-%d";

                        //echo "<br> datestring = " . $datestring;
                        //echo mdate($datestring);


                        //$hlp_code = substr($poule->nummer, -2);
						$arr_code = explode("-", $poule->nummer);
						$hlp_code = $arr_code[1];
                        $data3 = array('datum' => strval($query->row('datum')),
                            'tijd' => strval($query->row('tijd')),
                            'poule' => strval($single->nummer),
                            'thuis' => $poule->thuisploeg->club . " " . $poule->thuisploeg->teamafkorting,
                            'uit' => $poule->uitploeg->club . " " . $poule->uitploeg->teamafkorting,
                            'uitslag' => '',
                            'accommodatie' => strval($query->row('accommodatie')),
                            'adres' => '',
                            'postcode' => '',
                            'plaats' => '',
                            'veld' => '',
                            'accode' => '',
                            'mutdatum' => mdate($datestring),
                            'code' => $hlp_code);
						print_r($data3);
                        $this->db->insert($this->table2, $data3);
                    }
                    if ($query->num_rows() === 0 or $blnmut === true) {
                        //voeg nieuw record toe
                        $this->load->helper('date');
                        $datestring = "%Y-%m-%d";
                        //echo mdate($datestring);
                        //echo "<br> datestring = " . $datestring;
                        //die;
                        //   $this->load->helper('timestamp');	
                        $uitslag = $poule->scorethuis . "-" . $poule->scoreuit;
                        //var_dump($poule);
                        //echo "<br>uitslag = " . $uitslag . "<br>";
                        //$hlp_code = substr($poule->nummer, -2);
						$arr_code = explode("-", $poule->nummer);
						$hlp_code = $arr_code[1];
						
                        $data2 = array('datum' => strval($poule->datum),
                            'tijd' => strval($poule->tijd),
                            'poule' => strval($single->nummer),
                            'thuis' => $poule->thuisploeg->club . " " . $poule->thuisploeg->teamafkorting,
                            'uit' => $poule->uitploeg->club . " " . $poule->uitploeg->teamafkorting,
                            'uitslag' => $uitslag,
                            'accommodatie' => strval($poule->locatie->naam),
                            'adres' => strval($poule->locatie->adres),
                            'postcode' => strval($poule->locatie->postcode),
                            'plaats' => strval($poule->locatie->plaats),
                            'veld' => '',
                            'accode' => strval($poule->locatie->attributes()->iss),
                            'mutdatum' => mdate($datestring),
                            'code' => $hlp_code);
                        if ($blnmut === true) {
                            echo "update";
                            $this->db->where('poule', strval($single->nummer));
                            //$hlp_code = substr($poule->nummer, -2);
							$arr_code = explode("-", $poule->nummer);
							$hlp_code = $arr_code[1];
                            $this->db->where('code', $hlp_code);
                            $this->db->update($this->table, $data2);
							debug_last_query();
                            if ($wedstrijd_verplaatst) {
                                $this->db->insert($this->table2, $data2);
                            }
                            //$this->db->flush_cache();
                            //$this->db->reset();
                            // tmp hack barmut to false;
                            //$barmut = FALSE;
                            //echo "barmut = " . $barmut;
                            //die;

                            if ($barmut === TRUE) {
                                echo "update van bar<br>";
                                echo "<br>";
                                //$this->db->where('poule', strval($single->nummer));
                                //$this->db->where('code', substr($poule->nummer,-2));
                                //$this->db->where('dienst <',8);
                                //$data3 = array( 'dienst' => 0);
                                //$this->db->update('bardienst', $data3); 
                                //$this->db->_reset_select();
                            }
                        } else {
                            echo "insert";
                            $this->db->insert($this->table, $data2);
                        }
                        //debug_last_query();
                        // die;
                    }
                }
            }
        }
        return;
    }

    /*
      data moet in het bestand officials.csv staan. Incl header met onderstaande namen
      te starten met "http://www.forwodians.nl/fwBar/index.php/leden/read_officials"
      - aanpassing : verwijderen bestand (of renamen) na verwerking
     */

    function csv_to_sql() {
        //put your code here
        $this->load->model('ref_model');
        $fileDir = $this->config->item('fileDir');
        //$filePath = '../temp/officials.csv';
        //$filePath = 'c:\temp\officials.csv';
        $filePath = $fileDir . 'officials.csv';
        //echo $fileDir;die;
        $csvData = $this->csvreader->parse_file($filePath);
        //var_dump($csvData);
        foreach ($csvData as $field) {
            //print_r($field);
            //echo $field['Poule'];
            //die;
            $data2 = array('poule' => $field['Poule'],
                'naam01' => $field['Naam01'],
                'naam02' => $field['Naam02'],
                'naam03' => $field['Naam03'],
                'naam04' => $field['Naam04'],
                'naam05' => $field['Naam05'],
                'naam06' => $field['Naam06'],
                'naam07' => $field['Naam07'],
                'naam08' => $field['Naam08'],
                'official01' => $field['Official 01'],
                'official02' => $field['Official 02'],
                'official03' => $field['Official 03'],
                'official04' => $field['Official 04'],
                'official05' => $field['Official 05'],
                'official06' => $field['Official 06'],
                'official07' => $field['Official 07'],
                'official08' => $field['Official 08'],
                'veld' => $field['Veld'],
                'orgzn01' => $field['Org Zn01'],
                'orgzn02' => $field['Org Zn02'],
                'orgzn03' => $field['Org Zn03'],
                'orgzn04' => $field['Org Zn04'],
                'code' => $field['Code']);
            //echo "update";
            //var_dump($data2);
            //echo "<br>";
            //die;

            $this->db->where('poule', $field['Poule']);
            $this->db->where('code', $field['Code']);
            $this->db->update($this->table, $data2);
            //debug_last_query();
            $this->ref_model->ref_update_plannedtable($data2);
            //die;
        }
        return;
    }

    function del_wedstrijdrecords() {
        $this->load->helper('date');
        $datestring = "%Y-%m-%d";

        //echo "<br> datestring = " . $datestring;
        //echo mdate($datestring);

        $this->db->where('mutdatum <>', mdate($datestring));
        $this->db->delete('wedstrijden');

        //debug_last_query();
        return;
    }

    function get_wedstrijd($data) {
        $this->db->select("* , str_to_date(datum, '%d-%m-%Y' ) as dd", FALSE);
        if (isset($data['wedstrijd_poule'])) {
            $this->db->where('poule', $data['wedstrijd_poule']);
        }
        if (isset($data['wedstrijd_code'])) {
            $this->db->where('code', $data['wedstrijd_code']);
        }
        if (isset($data['wedstrijd_datum'])) {
            $this->db->where('datum', $data['wedstrijd_datum']);
        }
        $this->db->order_by('dd');
        $this->db->order_by('tijd');
        $query = $this->db->get('wedstrijden');
        //SELECT * FROM `wedstrijden` where STR_TO_DATE(datum,'%d-%m-%Y') between curdate() and curdate()+10
        //print_r($this->db->last_query());
        //var_dump($query->result());die;
        return $query->result();
    }

    function obj_get_wedstrijd($data) {
        if (isset($data['wedstrijd_poule'])) {
            $this->db->where('poule', $data['wedstrijd_poule']);
        }
        if (isset($data['wedstrijd_code'])) {
            $this->db->where('code', $data['wedstrijd_code']);
        }
        if (isset($data['wedstrijd_datum'])) {
            $this->db->where('datum', $data['wedstrijd_datum']);
        }
        $query = $this->db->get('wedstrijden');
        //print_r($this->db->last_query());
        return $query;
    }

    /*
     * poule + code is criteria
     * wedstrijd datum return
     */

    function get_wedstrijd_datum($data) {
        $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
        $row = $data['wedstrijd'][0]->datum;
        return $row;
    }

    function get_wedstrijd_datum_poule($data, $mode) {
        $data['wedstrijd_poule'] = $data['ref_poule'];
        //echo "<br>".$data['ref_poule'];
        unset($data['wedstrijd_code']);
        $query = $this->mod_wedstrijd->obj_get_wedstrijd($data);
        //var_dump($query->result());
        //mode 1 is ??
        //mode 2 is ??
        if ($mode === 1) {
            if ($query->num_rows() > 0) {
                return true;
            } else {
                //echo "<br>mod_wedstijd = false";
                return false;
            }
        }
        if ($mode === 2) {
            foreach ($query->result() as $row) {
                if ($row->accode === 'VHTSC') {
                    $thuis = true;
                } else {
                    $thuis = false;
                }
                //echo "<br>";
                //print_r($row->accode);
                return $thuis;
            }
        }
        //$row = $data['wedstrijd'][0]->poule;
        //return $row;
    }

    function get_wedstrijden_vandaag($data) {
// en de thuis wedstrijden...
        $this->load->helper('date');
        $datestring = $data['wedstrijd_datum'];

        //echo "<br> datestring = " . $data['wedstrijd_datum'];
        //echo mdate($datestring);
        $this->db->select('*');
        $this->db->where('datum =', mdate($datestring));
        $this->db->where('accode =', "VHTSC");
        $this->db->join('referee_planned as rp', 'rp.poule = w.poule and rp.code= w.code', 'left');
        $this->db->select('w.poule');
        $this->db->select('w.code');
        $query = $this->db->get('wedstrijden as w');
        //debug_last_query();
        // echo $this->db->last_query();
        //var_dump ($query->result());die;
        return ($query->result());
    }

    function get_coaching_match($data) {
        
    }

    function get_wedstrijd_range($data, $range) {
        $this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") > curdate() and STR_TO_DATE(datum,"%d-%m-%Y") <= DATE_ADD(curdate(),INTERVAL ' . $range . ' DAY)');
        $this->db->order_by('datum');
        $this->db->order_by('tijd', "desc");
        //$this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") between curdate() and curdate() + '. $range);
        //$this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") between curdate()-9 and curdate()');
        $query = $this->db->get('wedstrijden');
        //SELECT * FROM `wedstrijden` where STR_TO_DATE(datum,'%d-%m-%Y') between curdate() and curdate()+10
        //echo "<pre>";
        //print_r($this->db->last_query());
        //var_dump($query->result());
        //echo "</pre>";
        //die;
        return $query->result();
    }

    function get_wedstrijd_range_asc($data, $range) {
        $this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") > curdate() and STR_TO_DATE(datum,"%d-%m-%Y") <= DATE_ADD(curdate(),INTERVAL ' . $range . ' DAY)');
        $this->db->order_by('dd', "asc");
        $this->db->order_by('tijd', "asc");
        $this->db->select('STR_TO_DATE(datum,"%d-%m-%Y") as dd', false);
        $this->db->select('datum,tijd,poule,code,thuis,uit,naam01,naam02,naam03,naam04,naam05,accode');
        //$this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") between curdate() and curdate() + '. $range);
        //$this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") between curdate()-9 and curdate()');
        $query = $this->db->get('wedstrijden');
        //SELECT * FROM `wedstrijden` where STR_TO_DATE(datum,'%d-%m-%Y') between curdate() and curdate()+10
        //echo "<pre>";
        //print_r($this->db->last_query());
        //var_dump($query->result());
        //echo "</pre>";
        //die;
        return $query->result();
    }

    function get_wedstrijd_range_with_ref_asc($data, $range) {
        $this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") > curdate() and STR_TO_DATE(datum,"%d-%m-%Y") <= DATE_ADD(curdate(),INTERVAL ' . $range . ' DAY)');
        $this->db->where('accode = "VHTSC"');
        $this->db->where('(rp.status < 6 or isnull(rp.status))');
        $this->db->order_by('dd', "asc");
        $this->db->order_by('tijd', "asc");
        $this->db->order_by('poule', "asc");
        $this->db->order_by('run_id_sq', "asc");
        $this->db->select('STR_TO_DATE(datum,"%d-%m-%Y") as dd', false);
        $this->db->select('datum,tijd,wd.poule,wd.code,thuis,uit,naam01,naam02,naam03,naam04,naam05,ref_id,Voornaam,Tussenvoegsel,l.Naam,accode,run_id,run_id_sq');
        //$this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") between curdate() and curdate() + '. $range);
        //$this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") between curdate()-9 and curdate()');
        $this->db->join('referee_planned as rp', 'rp.poule=wd.poule and rp.code=wd.code', 'left');
        $this->db->join('leden as l', 'rp.ref_id=l.lidnr', 'left');
        $this->db->join('teams as t', 't.poulid=wd.poule', 'left');
        $this->db->join('teamindeling as ti', 'ti.lidnummer=l.lidnr', 'left');
        $this->db->select('ti.team as ref_team');
        $this->db->select('t.code as team, nivo');
        $this->db->join('referee as r', 'r.lidnr=l.lidnr and rp.role=r.categorie', 'left');
        $this->db->select('r.awgr, r.categorie');
        if (isset($data['wedstrijd_datum'])) {
            $this->db->where('datum', $data["wedstrijd_datum"]);
        }
        $query = $this->db->get('wedstrijden as wd');
        //SELECT * FROM `wedstrijden` where STR_TO_DATE(datum,'%d-%m-%Y') between curdate() and curdate()+10
        //echo "<pre>";
        //echo "<br>";print_r($this->db->last_query());die;
        //var_dump($query->result());
        //echo "</pre>";
        //die;
        return $query->result();
    }

    function get_wedstrijd_range_with_team($data, $range) {
        $this->db->where('STR_TO_DATE(datum,"%d-%m-%Y") > curdate() and STR_TO_DATE(datum,"%d-%m-%Y") <= DATE_ADD(curdate(),INTERVAL ' . $range . ' DAY)');
        $this->db->where('accode = "VHTSC"');
        $this->db->order_by('dd', "asc");
        $this->db->order_by('tijd', "asc");
        $this->db->order_by('poule', "asc");
        $this->db->select('STR_TO_DATE(datum,"%d-%m-%Y") as dd', false);
        $this->db->select('datum,tijd,wd.poule,wd.code,thuis,uit,naam01,naam02,naam03,naam04,naam05,accode');
        $this->db->join('teams as t', 't.poulid=wd.poule', 'left');
        $this->db->select('t.code as team, nivo');
        if (isset($data['wedstrijd_datum'])) {
            $this->db->where('datum', $data["wedstrijd_datum"]);
        }
        $query = $this->db->get('wedstrijden as wd');
        return $query->result();
    }

    function create_wedstrijd_ovz($data) {
        $this->load->helper('download');
        $this->load->model('team_model');
        $range = 100;
        //echo "hier";
        $data['wed'] = $this->get_wedstrijd_range_asc($data, $range);
        //var_dump($data['wed']);
        $outfile = 'wed_ovz.csv';
        $file = "datum;tijd;poule;thuis;uit;scheidsrechters;tafelaars\n";
        foreach ($data['wed'] as $row) {
            if ($hlp_datum <> $row->datum) {
                $file.="\n";
            }
            //$poule = $this->team_model->get_poulid_from_team_lidnr($row['LIDNR']);
            //$file.=$row['LIDNR'] . ";" . $row['CATEGORIE'] . ";" . $row['ZOEKNAAM'] . ";" . $row['AWGR'] . ";" . $row['ref_telling'] . ";" . $poule . "\n";
//			echo "<pre>";
//			var_dump ($row);
//			echo "</pre>";
            $file.=$row->datum . ";" . $row->tijd . ";" . $row->poule . "-" . $row->code . ";" . $row->thuis . ";" . $row->uit . ";" . $row->naam01 . " - " . $row->naam02 . ";" . $row->naam03 . " - " . $row->naam04 . " - " . $row->naam05 . "\n";
            $hlp_datum = $row->datum;
        }
        //var_dump( $file);
        //			die;
        force_download($outfile, $file);
        return;
    }

}

?>
