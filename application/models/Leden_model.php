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
class Leden_model extends CI_Model {

//bar gegevens
    public $code;
    public $poule;
    public $lidnummer;
    public $id;
    public $dienst;
//mail gegevens 
    public $naam_lid;
    public $mail_id;
    public $wedstrijddatum;
    public $wedstrijdtijd;
    public $email_adres;
    public $thuis_ploeg;
    public $uit_ploeg;
    public $antwoord_bardienst;
    protected $table = 'leden';
    protected $barYes = 2;
    protected $barNo = 9;
    protected $barNever = 8;
    public $barMailYes;
    public $barMailNo;
    public $barMailNever;

//put your code here
    function __construct() {
        parent::__construct();
        //$this->load->database();
//        define('DATE_ICAL', 'Ymd\THis');
        $this->output->enable_profiler($this->config->item('profiler'));
    }

    public function setId($int) {
        $this->id = $int;
        return true;
    }

    public function getId() {
        return $this->id;
    }

    function leden_getall() {
        $query = $this->db->get('leden');
        return $query->result();
    }

    public function CheckAntwoord($antw) {
//check all mogelijke antwoorden
//no, yes, never
        if (strtoupper($antw) === "NO") {
            $this->antwoord_bardienst = 9;
            return (TRUE);
        }
        if (strtoupper($antw) === "YES") {
            $this->antwoord_bardienst = 2;
            return (TRUE);
        }
        if (strtoupper($antw) === "NEVER") {
            $this->antwoord_bardienst = 8;
            return (TRUE);
        }
        return (FALSE);
    }

    public function CheckId($id = null) {
        if (isset($this->id)) {
            return true;
        }
        return false;
    }

    /*     * *******************
     * 1 lengte van de lengte van de string
     * 2 lengte van de string
     * 3 de string zelf
     * 4 de code 
     * 5 restant is ballast
     * 
     * ******************* */

    public function DetectIdFromMail($string) {
        if ($this->CheckString($string, 10)) {
            $lenLen = $this->CheckstringLen($string);
            $lenId = $this->CheckstringIdLen($string, $lenLen, $lenLen);
            $this->id = $this->GetIdFromString($string, $lenLen + 1, $lenId);
            $this->antwoord_bardienst = $this->GetAntwoordBardienstFromString($string, $lenId + $lenLen + 1, 1);
            return true;
        }
        return FALSE;
    }

    public function CheckString($string, $len) {
        if (strlen($string) > $len) {
            return TRUE;
        }
        return FALSE;
    }

    public function CheckstringLen($string) {
        return substr($string, 0, 1);
    }

    public function CheckstringIdLen($string, $len) {
        return substr($string, $len, $len);
    }

    public function GetIdFromString($string, $len, $lenId) {
        return substr($string, $len + 1, $lenId);
    }

    public function GetAntwoordBardienstFromString($string, $len, $start) {
        return substr($string, $len, $start);
    }

    public function CreateIdForMailYes() {
        if ($this->CheckId()) {
            $this->barMailYes = $this->CreateIdForMail($this->barYes);
            return TRUE;
        }
        return FALSE;
    }

    public function CreateIdForMailNo() {
        if ($this->CheckId()) {
            $this->barMailNo = $this->CreateIdForMail($this->barNo);
            return TRUE;
        }
        return FALSE;
    }

    public function CreateIdForMailNever() {
        if ($this->CheckId()) {
            $this->barMailNever = $this->CreateIdForMail($this->barNever);
            return TRUE;
        }
        return FALSE;
    }

    private function CreateIdForMail($code) {
        return strlen(strlen($this->id)) . strlen($this->id) . $this->id . $code . now();
    }

    public function UpdateBardienst() {
        if ($this->retrieve_db()) {
            if ($this->antwoord_bardienst == 8) {
                $this->bar_statusupdate($this->antwoord_bardienst, 0, 0, $this->lidnummer);
                $this->add_bardienst(0, $this->poule, $this->code, "");
                return TRUE;
            } else {
                if ($this->dienst <> '9') {
                    $this->bar_statusupdate($this->antwoord_bardienst, $this->poule, $this->code, $this->lidnummer);
                    if ($this->antwoord_bardienst == 9) {
                        $this->add_bardienst(0, $this->poule, $this->code, "");
                    }
                }
                return TRUE;
            }
        }
        return false;
    }

    private function retrieve_db() {
        $this->db->from("bardienst");
        $this->db->join("wedstrijden", "bardienst.poule=wedstrijden.poule and bardienst.code=wedstrijden.code");
        $this->db->where('id', $this->id);
        $query = $this->db->get();
        $this->db->last_query();
        //$this->db->debug_query_result($query);

        if ($query->num_rows() > 0) {
            return ($this->get_bar_from_id());
            return true;
        }
        return FALSE;
    }

    function bar_statusupdate($status, $poule, $code, $lidnummer) {
        //$this->load->helper('date');
        //$this->load->helper('timestamp');
        $data = array('dienst' => $status,
            'poule' => $poule,
            'code' => $code,
            'lidnummer' => $lidnummer,
            'invdatum' => timestamp());
        $this->db->where('id', $this->id);
        $this->db->update('bardienst', $data);
//debug_last_query();
//debug_query_result($query);
//DIE;
    }

    function add_bardienst($status, $poule, $code, $lidnummer) {
//        $this->load->helper('date');
//        $this->load->helper('timestamp');
        $data2 = array('poule' => $poule,
            'code' => $code,
            'lidnummer' => $lidnummer,
            'invdatum' => timestamp(),
            'dienst' => $status);
        $this->db->insert('bardienst', $data2);
    }

    function add_chauffeur($status, $poule, $code, $lidnummer1, $lidnummer2, $lidnummer3) {
        //       $this->load->helper('date');
        //       $this->load->helper('timestamp');
        $data2 = array('poule' => $poule,
            'code' => $code,
            'lidnummer1' => $lidnummer1,
            'lidnummer2' => $lidnummer2,
            'lidnummer3' => $lidnummer3,
            'seizoen' => $this->functions->get_seizoen(),
            'invdatum' => timestamp(),
            'dienst' => $status);
        $this->db->insert('chauffeur', $data2);
    }

    function get_bar_from_id() {
        $this->db->select('*');
        $this->db->from('bardienst');
        $this->db->where('id', $this->id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $this->code = $query->row('code');
            $this->poule = $query->row('poule');
            $this->lidnummer = $query->row('lidnummer');
            $this->dienst = $query->row('dienst');
//            debug_var($this->dienst);
            return true;
        }
        return false;
    }

    function get_bar_from_poule() {
        $this->db->select('*');
        $this->db->from('bardienst');
        $this->db->where('poule', $this->poule);
        $this->db->where('code', $this->code);
        $this->db->where('dienst < ', 8);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $this->code = $query->row('code');
            $this->poule = $query->row('poule');
            $this->lidnummer = $query->row('lidnummer');
            $this->dienst = $query->row('dienst');
            $this->id = $query->row('id');
            return;
        }
        return;
    }

    function update_wedstrijd_code() {
        /*
          $sql = "UPDATE `wedstrijden` SET code = ucase( code ) ";
          $this->db->query($sql);
         */
    }

    function vul_bar_init() {
        $this->update_wedstrijd_code();
        $this->db->select('wedstrijden.poule, wedstrijden.code, datum, tijd');
        $this->db->from('wedstrijden');
//$this->db->join('bardienst',"bardienst.poule=wedstrijden.poule and bardienst.code=wedstrijden.code", "left outer");
//$this->db->where("bardienst.dienst is null");
        $this->db->where("accode", "VHTSC");
//tijd 00:00 uitgesloten voor bardienst
        $this->db->where("tijd <>", "00:00");
        $this->db->group_by('datum, tijd, poule, code');
        $this->db->order_by('veld', 'asc');
//moet nog order by bij komen ivm toeveoging dubbele entries... 18/9/12

        $query = $this->db->get();
//print_r($this->db->last_query());
//debug_query_result($query);
//die;

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->poule = $rij->poule;
                $this->code = $rij->code;
                if ($this->check_bar()) {
                    $this->add_bardienst(0, $rij->poule, $rij->code, "");
                }
            }
        }
    }

    function vul_chauffeur_init() {
        $this->update_wedstrijd_code();
        $this->db->select('wedstrijden.poule, wedstrijden.code, datum, tijd');
        $this->db->from('wedstrijden');
        $this->db->where("accode <>", "VHTSC");
        $this->db->order_by('veld', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->poule = $rij->poule;
                $this->code = $rij->code;
                if ($this->check_chauffeur()) {
                    $this->add_chauffeur(0, $rij->poule, $rij->code, 0, 0, 0);
                }
            }
        }
    }

    function check_bar() {
        $this->db->select('*');
        $this->db->from('bardienst');
        $this->db->where('poule', $this->poule);
        $this->db->where('dienst', 8);
//team uitzondering
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return false;
        }

//en nog een keer op aanwezigheid
        $this->db->from('bardienst');
        $this->db->where('poule', $this->poule);
        $this->db->where('code', $this->code);
        $this->db->where("dienst <>", 8);
        $query2 = $this->db->get();
//echo "<br>";
//print_r($this->db->last_query());
        if ($query2->num_rows() > 0) {
            return false;
        }
        return true;
    }

    function check_chauffeur() {
        $this->db->select('*');
        $this->db->from('chauffeur');
        $this->db->where('poule', $this->poule);
        $this->db->where('dienst', 8);
//team uitzondering
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return false;
        }

//en nog een keer op aanwezigheid
        $this->db->from('chauffeur');
        $this->db->where('poule', $this->poule);
        $this->db->where('code', $this->code);
        $this->db->where("dienst <>", 8);
        $query2 = $this->db->get();
        if ($query2->num_rows() > 0) {
            return false;
        }
        return true;
    }

    function vul_opendiensten_in() {
// zoek de open bardiensten
        $this->db->from('bardienst');
        $this->db->where('lidnummer', "");
        $this->db->where('dienst <', 8);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->poule = $rij->poule;
                $this->code = $rij->code;
                $this->zoek_lid_zonder_bardienst();
            }
        }
    }

    function zoek_lid_zonder_bardienst() {
// zoek in de team indeling naar de leden
//***** manipulatie van poule 
//$this->poule = '4243';
//******
        $this->db->select('teamindeling.lidnummer');
        $this->db->from('teamindeling');
        $this->db->join('teams', 'teamindeling.team = teams.code');
        $this->db->where("teams.poulid", $this->poule);
        $query = $this->db->get();
        $this->debug_log("function=zoek_lid_zonder_bardienst");
        $this->debug_last_query();
//debug_query_result($query);
// die;
//        debug_var($this->poule);

        $bln_bardienst = false;
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->lidnummer = $rij->lidnummer;
//echo "1";debug_var($this->lidnummer);
                if ($this->is_uitzondering_voor_bar()) {
                    continue;
                }
//echo "2";debug_var($this->lidnummer);
                if ($this->heeft_al_bardienst()) {
                    continue;
                }
//echo "3";debug_var($this->lidnummer);
                $bln_bardienst = true;
                break;
            }
            if (!$bln_bardienst) {
                $this->minste_bardiensten();
            }
            $hlpLidnummr = $this->lidnummer;
//echo $hlpLidnummr;
//die;
            $this->get_bar_from_poule();
            $this->lidnummer = $hlpLidnummr;
            if ($this->lidnummer <> "") {
                $this->bar_statusupdate(0, $this->poule, $this->code, $this->lidnummer);
            }
        }
    }

    function heeft_al_bardienst() {
// kijk eerst in de bardienst
        $this->db->from('bardienst');
        $this->db->where('lidnummer', $this->lidnummer);
        $this->db->where('poule', $this->poule);
        $this->db->where('dienst <', 8);
        $query = $this->db->get();
//debug_log("function=heeft_al_bardienst");
//debug_last_query();
//debug_query_result($query);        
//die;
        if ($query->num_rows() > 0) {
// gevonden, heeft reeds een bardien
            return true;
        }
// niet gevonden, heeft geen bardienst
        return false;
    }

    function is_uitzondering_voor_bar() {
// kijk eerst in de bardienst
        $this->db->select('*');
        $this->db->from('bardienst');
        $this->db->where('lidnummer', $this->lidnummer);
        $this->db->where('dienst = ', 8);
        $this->db->or_where('(lidnummer = "' . $this->lidnummer . '" and dienst = 9 and poule="' . $this->poule . '" and code="' . $this->code . '")');
        $query = $this->db->get();
//debug_log("function=uitzondering voor bar");
//debug_last_query();
//debug_query_result($query);
//debug_var($this->poule);
//die;

        if ($query->num_rows() > 0) {
// gevonden, is vrijgesteld
//debug_log("uitzondering");
            return true;
        }
// niet gevonden, geen vrijstelling
        return false;
//die;
    }

    function minste_bardiensten() {
// echo "<br>minste bardiensten<br>";
        $this->db->select('count(lidnummer),lidnummer');
        $this->db->from('bardienst');
        $this->db->where('poule', $this->poule);
        $this->db->where('dienst < ', 8);
        $this->db->where('lidnummer <> ', "");
        $this->db->group_by('lidnummer');
        $this->db->order_by('count(lidnummer)', 'asc');
        $query = $this->db->get();
//debug_log("function=minste_bardiensten");
//debug_last_query();
//debug_query_result($query);
        if ($query->num_rows() > 0) {
// gevonden, is vrijgesteld
            foreach ($query->result() as $rij) {
                $this->lidnummer = $rij->lidnummer;
                if (!$this->is_uitzondering_voor_bar()) {
                    return;
                }
            }
        }
        return;
    }

    function mail_selection_herinnering() {
        $this->load->helper('date');
        $this->load->library('email');
        $this->db->select('id, datum, tijd, bardienst.poule, bardienst.code, bardienst.lidnummer, naam, tussenvoegsel, voornaam, email, thuis, uit, accode, dienst ');
        $this->db->from('bardienst');
        $this->db->join('leden', "bardienst.lidnummer = leden.lidnr");
        $this->db->where('dienst <= ', 2);
//$this->db->or_where('dienst = ', 2);		
        $this->db->where('bardienst.lidnummer <> ', "");
//$this->db->where("date_format(str_to_date(datum,'%d-%m-%Y'),'%Y-%m-%d') >= ", "2013-04-10");
//$this->db->where("date_format(str_to_date(datum,'%d-%m-%Y'),'%Y-%m-%d') >= ", "DATE_ADD(NOW(),INTERVAL 7 DAYS )", null);
        $this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  >= ', 'CURDATE()', false);
        $this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  < ', 'DATE_ADD(CURDATE(), INTERVAL 7 DAY)', false);
        $this->db->join('wedstrijden', "bardienst.poule = wedstrijden.poule and bardienst.code = wedstrijden.code");
        $this->db->where('accode', 'VHTSC'); //thuiswedstrijden
        $this->db->order_by('bardienst.poule,bardienst.code', 'asc');
        $query = $this->db->get();
//        debug_last_query();
//        debug_query_result($query);


        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->email->clear();
                $this->mail_id = $rij->id;
                $this->id = $rij->id;
                $this->wedstrijddatum = $rij->datum;
                $this->wedstijdtijd = $rij->tijd;
                $this->poule = $rij->poule;
                $this->code = $rij->code;
                $this->lidnummer = $rij->lidnummer;
                $this->naam_lid = $rij->voornaam . " " . $rij->tussenvoegsel . " " . $rij->naam;
                $this->email_adres = $rij->email;
                $this->thuis_ploeg = $rij->thuis;
                $this->uit_ploeg = $rij->uit;
                $this->dienst = $rij->dienst;

                $this->email->from('barcie@forwodians.nl', 'Barcie Forwodians');
                $this->email->to($this->mail_to());
                $this->email->reply_to('BC-forwodians@edelaar.nl', 'barcie_forwodians');
//        $this->email->cc('another@another-example.com');
                $this->email->bcc('fw_check@edelaar.nl');
                $mail_subject = $this->mail_subject($rij->datum, $rij->tijd);
//       echo $mail_subject;
                $this->email->subject($mail_subject);
                $mail_body = $this->mail_body_herinnering();
//   echo $mail_body;
                $this->email->message($mail_body);
//$this->email->attach('wp-content/plugins/wedstrijd/Handleiding_bardienstv4.pdf');
//echo $mail_body; 
//die;
                $this->email->send();
                $this->bar_statusupdate(1, $rij->poule, $rij->code, $rij->lidnummer);
//die;
            }
        }
//print_r($this->db->last_query());
//die;
//debug_query_result($query);
        return;
    }

    function get_team_from_lid() {

        $this->db->select('lidnummer,team');
        $this->db->from('teamindeling');
        $this->db->where("teamindeling.lidnummer", $this->lidnummer);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
// gevonden, is vrijgesteld
            foreach ($query->result() as $rij) {
//echo "<br>get_team_from_lid " .$rij->team;
                return($rij->team);
            }
        }
        return("ZZZZ");
    }

    function bep_senior_lid() {
        if (substr($this->get_team_from_lid(), 1, 1) == "S") {
//echo "<br>bep_senior_lid = oke";
            return(true);
        }
        return(false);
    }

    function mail_selection() {
        $this->load->library('email');
        $this->load->helper('date');
        $this->db->select('id, datum, tijd, bardienst.poule, bardienst.code, bardienst.lidnummer, naam, tussenvoegsel, voornaam, email, thuis, uit, accode ');
        $this->db->from('bardienst');
        $this->db->join('leden', "bardienst.lidnummer = leden.lidnr");
        $this->db->where('dienst = ', 0);
        $this->db->where('bardienst.lidnummer <> ', "");
        $this->db->join('wedstrijden', "bardienst.poule = wedstrijden.poule and bardienst.code = wedstrijden.code");
        $this->db->where('accode', 'VHTSC'); //thuiswedstrijden
//$this->db->where("str_to_date('datum', '%d-%m-%Y')>= ", DATE_FORMAT(date(now()),'%d-%m-%Y'));	
        $this->db->order_by('bardienst.poule,bardienst.code', 'asc');
        $query = $this->db->get();
//debug_last_query();
//debug_query_result($query);
//die; 

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->email->clear();
                $this->mail_id = $rij->id;
                $this->id = $rij->id;
                $this->wedstrijddatum = $rij->datum;
                $this->wedstijdtijd = $rij->tijd;
                $this->poule = $rij->poule;
                $this->code = $rij->code;
                $this->lidnummer = $rij->lidnummer;
                $this->naam_lid = $rij->voornaam . " " . $rij->tussenvoegsel . " " . $rij->naam;
                $this->email_adres = $rij->email;
                $this->thuis_ploeg = $rij->thuis;
                $this->uit_ploeg = $rij->uit;

                $this->email->from('barcie@forwodians.nl', 'Barcie Forwodians');
                $this->email->to($this->mail_to());
                $this->email->reply_to('BC-forwodians@edelaar.nl', 'barcie_forwodians');
//        $this->email->cc('another@another-example.com');
                $this->email->bcc('fw_check@edelaar.nl');

                if ($this->bep_senior_lid()) {
                    $mail_body = $this->mail_body_senior();
                    $mail_subject = $this->mail_subject_senior($rij->datum, $rij->tijd);
                } else {
                    $mail_body = $this->mail_body();
                    $mail_subject = $this->mail_subject($rij->datum, $rij->tijd);
                }
                $this->email->subject($mail_subject);
                $this->email->message($mail_body);
//echo $mail_subject;                	
//echo $mail_body;                	                	
//$this->email->attach('wp-content/plugins/wedstrijd/Handleiding_bardienstv4.pdf');
//echo "<pre>";
//print_r($this->mail_to());//die;
//echo "</pre>";
//print_r($this->email);
//die;
                $this->email->send();
//echo $this->email->print_debugger();
                $this->bar_statusupdate(1, $rij->poule, $rij->code, $rij->lidnummer);
//die;
            }
        }
//print_r($this->db->last_query());
//die;
//debug_query_result($query);
        return;
    }

    function mail_body() {
//private strBody;
        $this->load->helper('date');
        $this->load->helper('url');
        $strBody = '<br>Wij, de barcommissie, doen erg veel moeite om tijdens de ';
        $strBody .= "thuiswedstrijden van uw zoon/dochter de bardiensten in te ";
        $strBody .= "roosteren. Per telefoon ouders benaderen kost veel tijd. ";
        $strBody .= "Daarom doen we dit via de mail. Aangezien uw kind tijdens ";
        $strBody .= "de wedstrijd graag toeschouwers heeft, is het juist tijdens ";
        $strBody .= "deze wedstrijd handig als er een bardienst wordt gedraaid ";
        $strBody .= "door 1 van de ouders. Deze barinkomsten drukken de kosten ";
        $strBody .= "van de club. Wij hebben een bardienstrooster gemaakt en wij ";
        $strBody .= "gaan ervan uit dat alle diensten ook kunnen worden gedraaid. ";
        $strBody .= "Wij hebben U voor de bardienst ingedeeld voor onderstaande wedstrijd.<br>";
        $strBody .= "<br>" . $this->thuis_ploeg . " tegen " . $this->uit_ploeg;
        $strBody .= "<br>aanvang wedstrijd is " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2) . " op " . $this->wedstrijddatum;
        $strBody .= "<br><br>";
        $strBody .= anchor("leden/yesbardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "bevestiging deze bardienst", "bevestiging");
        $strBody .= "<br><br>";
        $strBody .= anchor("leden/nobardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "afmelding deze bardienst", "afmelding");
        $strBody .= $this->geweigerde_bardiensten();
        $strBody .= "<br><br>";
        $strBody .= "<br>Mocht u van mening zijn dat u geen bardienst hoeft te draaien omdat u andere activiteiten voor de vereniging verricht,";
        $strBody .= " dan klikt u op de link hieronder en zult u dit deel van het seizoen niet verder worden ingedeeld voor bardiensten.<br>";
        $strBody .= anchor("leden/neverbardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "vrijstelling van bardienst", "afmelding");
        $strBody .= "<br><br>";
        $strBody .= anchor("http://www.forwodians.nl/fwBar/Handleiding_bardienst_2013-2014v1.pdf", 'handleiding bardienst');
        $strBody .= "<br>";

        $strBody .= "voor andere vragen of opmerkingen, graag even een reply op dit mailadres!";
//$strBody.=  "poule = " . $this->poule."<br><hr><br>";                
        return $strBody;
    }

    function mail_body_senior() {
//private strBody;
        $this->load->helper('date');
        $this->load->helper('url');
        $strBody = '<br>Met het bestuur en de TC hebben wij als barcommissie  ';
        $strBody .= "afgesproken dat de seniorenteams de bar invulling zelf ";
        $strBody .= "regelen.  ";
        $strBody .= "De barcommissie vraagt een teamlid om de bar invulling te ";
        $strBody .= "regelen. Natuurlijk kan je dit zelf tijdens de wedstrijd niet ";
        $strBody .= "doen. Vraag daarom of je partner of ouders of vrienden, die  ";
        $strBody .= "toch aanwezig zijn, om dit voor je in te vullen. ";
        $strBody .= "<br><br>Doe dit aub voor je club. Want ook de tegenstanders en scheidsrechters ";
        $strBody .= "hebben vast trek in minimaal een kopje koffie. ";
        $strBody .= "Wij hebben je voor de bardienst ingedeeld voor onderstaande wedstrijd.<br>";
        $strBody .= "<br>" . $this->thuis_ploeg . " tegen " . $this->uit_ploeg;
        $strBody .= "<br>aanvang wedstrijd is " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2) . " op " . $this->wedstrijddatum;
        $strBody .= "<br><br>";
        $strBody .= anchor("leden/yesbardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "bevestiging deze bardienst", "bevestiging");
        $strBody .= "<br><br>";
        $strBody .= anchor("leden/nobardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "afmelding deze bardienst", "afmelding");
        $strBody .= $this->geweigerde_bardiensten();
        $strBody .= "<br><br>";
        $strBody .= "<br>Mocht je van mening zijn dat je geen bardienst hoeft te draaien omdat je andere activiteiten voor de vereniging verricht,";
        $strBody .= " dan klikt je op de link hieronder en zult je dit deel van het seizoen niet verder worden ingedeeld voor bardiensten.<br>";
        $strBody .= anchor("leden/neverbardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "vrijstelling van bardienst", "afmelding");
        $strBody .= "<br><br>";
        $strBody .= anchor("http://www.forwodians.nl/fwBar/Handleiding_bardienst_2013-2014v1.pdf", 'handleiding bardienst');
        $strBody .= "<br>";
        $agenda_ref = $this->get_href_ical_fw($this->wedstrijddatum, $this->wedstijdtijd, "De Schans", "Bardienst " . $this->thuis_ploeg, "agenda");
        $strBody .= "voor in uw eigen " . $agenda_ref . " ";
        $strBody .= "<br>";
        $strBody .= "voor andere vragen of opmerkingen, graag even een reply op dit mailadres!";
//$strBody.=  "poule = " . $this->poule."<br><hr><br>";                
        return $strBody;
    }

    function mail_body_herinnering() {
//private strBody;
        $this->load->helper('date');
        $this->load->helper('url');
        $strBody = '<br>Hierbij herinneren wij u eraan dat u in de komende periode ';
        $strBody .= "bent ingeroosterd voor de bardienst. Hopelijk heeft u dit reeds ";
        $strBody .= "in uw agenda genoteerd, anders graag alsnog even doen svp. ";
        $strBody .= "Het betreft de onderstaande wedstrijd: ";
        $strBody .= "<br>" . $this->thuis_ploeg . " tegen " . $this->uit_ploeg;
        $strBody .= "<br>aanvang wedstrijd is " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2) . " op " . $this->wedstrijddatum;
        $strBody .= "<br><br>";
        $strBody .= "<br><br>";
        if ($this->dienst == 1) {
            $strBody .= anchor("leden/yesbardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "graag nog even deze bardienst bevestiging", "bevestiging");
        }
        $strBody .= "<br><br>";
        $strBody .= anchor("leden/nobardienst/" . strlen($this->mail_id) . $this->mail_id . NOW(), "afmelding deze bardienst", "afmelding");
        $strBody .= $this->geweigerde_bardiensten();
        $strBody .= "<br><br>";
        $strBody .= "<br><br>";
        $agenda_ref = $this->get_href_ical_fw($this->wedstrijddatum, $this->wedstijdtijd, "De Schans", "Bardienst " . $this->thuis_ploeg, "agenda");
        $strBody .= "voor in uw eigen " . $agenda_ref . " ";
        $strBody .= "<br>voor vragen of opmerkingen, graag even een reply op dit mailadres!";
//$strBody.=  "poule = " . $this->poule."<br><hr><br>";                
        return $strBody;
    }

    function get_href_ical_fw($datum, $tijd, $locatie, $oms, $reftxt) {
        $hUrl = "<a href='http://www.forwodians.nl/fwBar/iCall.php?dt=" . $datum . "&td=" . $tijd . "&lc=" . str_replace(" ", "+", $locatie) . "&tp=" . str_replace(" ", "+", $oms) . "'>" . $reftxt . "</a>";
//echo $hUrl;
//die; 
        return $hUrl;
    }

    function mail_selection_feedback() {
        $this->load->library('email');
        $this->load->helper('date');
        $this->db->select('bardienst.dienst, id, wedstrijden.datum, tijd, bardienst.poule, bardienst.code, bardienst.lidnummer, naam, tussenvoegsel, voornaam, email, thuis, uit, accode ');
        $this->db->from('bardienst');
        $this->db->join('leden', "bardienst.lidnummer = leden.lidnr");
        $this->db->where('bardienst.dienst <', 3);
        $this->db->where('bardienst.lidnummer <> ', "");
        $this->db->join('wedstrijden', "bardienst.poule = wedstrijden.poule and bardienst.code = wedstrijden.code");
        $this->db->where('accode', 'VHTSC'); //thuiswedstrijden
        $this->db->where("STR_TO_DATE(datum,'%d-%m-%Y' ) <= DATE_FORMAT(date(now()),'%Y-%m-%d')", Null);
        $this->db->order_by('bardienst.poule,bardienst.code', 'asc');
        $query = $this->db->get();
//debug_last_query();
//debug_query_result($query);
//       die; 
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rij) {
                $this->email->clear();
                $this->mail_id = $rij->id;
                $this->id = $rij->id;
                $this->wedstrijddatum = $rij->datum;
                $this->wedstijdtijd = $rij->tijd;
                $this->poule = $rij->poule;
                $this->code = $rij->code;
                $this->lidnummer = $rij->lidnummer;
                $this->naam_lid = $rij->voornaam . " " . $rij->tussenvoegsel . " " . $rij->naam;
                $this->email_adres = $rij->email;
                $this->thuis_ploeg = $rij->thuis;
                $this->uit_ploeg = $rij->uit;

                $this->email->from('barcie@forwodians.nl', 'Barcie Forwodians');
                $this->email->to($this->mail_to());
//$this->email->to("test@edelaar.nl");
                $this->email->reply_to('BC-forwodians@edelaar.nl', 'barcie_forwodians');
                $this->email->bcc('fw_check@edelaar.nl');
                $mail_subject = $this->mail_subject_feedback($rij->datum, $rij->tijd);
                $this->email->subject($mail_subject);
                $mail_body = $this->mail_body_feedback();
                $this->email->message($mail_body);
                $this->email->send();
                $this->bar_statusupdate(3, $rij->poule, $rij->code, $rij->lidnummer);
            }
        }
        return;
    }

    function mail_body_feedback() {
//private strBody;
        $this->load->helper('date');
        $this->load->helper('url');
        $strBody = '<br>Namens de vereniging bedankt dat u een bardienst heeft willen draaien. ';
        $strBody .= "Aangezien we altijd op zoek zijn om zaken te verbeteren willen we u vragen ";
        $strBody .= "om dit " . anchor("https://docs.google.com/forms/d/1326j2fzKaD6phHxmwEnv2aQEn9pWhjrHnd3733zaLPI/viewform", 'vragenformulier') . " in te vullen. ";
        $strBody .= "<br><br>";
        $strBody .= "Het betrof de bardienst van de onderstaande wedstrijd: ";
        $strBody .= "<br>" . $this->thuis_ploeg . " tegen " . $this->uit_ploeg;
        $strBody .= "<br>aanvang wedstrijd was " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2) . " op " . $this->wedstrijddatum;
        $strBody .= "<br><br>";
        $strBody .= "<br><br>";
        $strBody .= "voor vragen of opmerkingen, graag even een reply op dit mailadres!";
//$strBody.=  "poule = " . $this->poule."<br><hr><br>";                
        return $strBody;
    }

    function geweigerde_bardiensten() {
        $this->db->select("*");
        $this->db->from('bardienst');
        $this->db->join('leden', 'leden.lidnr=bardienst.lidnummer');
        $this->db->where('poule', $this->poule);
        $this->db->where('code', $this->code);
        $this->db->where('dienst', 9);
        $this->db->order_by('invdatum', 'desc');
        $query = $this->db->get();
//debug_last_query();
//debug_query_result($query);

        $strOuder = "";
        if ($query->num_rows() > 0) {
            $strOuder = "<br>Onderstaande ouders hebben deze dienst eerder geweigerd";
// gevonden, is vrijgesteld
            foreach ($query->result() as $rij) {
                $strOuder .= "<br>Ouders van " . $rij->Voornaam . " " . $rij->Tussenvoegsel . " " . $rij->Naam . " op " . $rij->invdatum;
            }
        }
        return $strOuder;
    }

    function mail_subject($wddatum, $wdtijd) {
        return "Ouders/verzorgers " . $this->naam_lid . ":" . $wddatum . " " . $wdtijd;
    }

    function mail_subject_senior($wddatum, $wdtijd) {
        return "Bardienst verzoek voor " . $this->naam_lid . " dd " . $wddatum . " " . $wdtijd;
    }

    function mail_subject_feedback($wddatum, $wdtijd) {
        return "Feedback gevraagd bardienst:" . $wddatum . " " . $wdtijd;
    }

    function mail_to() {
        $arr = array();
        $arr = $this->add_to_arr($arr, $this->email_adres);
        return $arr;
    }

    function mail_bijlage() {
        echo anchor("http://www.forwodians.nl/temp/BIB_P12-13.pdf", 'handleiding bardienst');
        echo "<br>";
        return;
    }

    function vul_uitzondering_lid_in($id) {
        $data = array('poule' => 0,
            'code' => 0,
            'lidnummer' => $id,
            'dienst' => '8');
        $this->db->insert('bardienst', $data);
    }

    function vul_uitzondering_team_in($poule) {
        $data = array('poule' => $poule,
            'code' => 0,
            'lidnummer' => "",
            'dienst' => '8');
        $this->db->insert('bardienst', $data);
    }

    function get_leden() {
        $this->load->helper('date');
        $this->db->select('id, datum, tijd, bardienst.poule, bardienst.code, bardienst.lidnummer, naam, tussenvoegsel, voornaam, email, thuis, uit, telnr, accode ');
        $this->db->from('bardienst');
        $this->db->join('leden', "bardienst.lidnummer = leden.lidnr");
        $this->db->where('dienst > ', 0);
        $this->db->where('dienst < ', 8);
//$this->db->where("str_to_date('datum', '%d-%m-%Y')>= ", DATE_FORMAT(date(now()),'%d-%m-%Y'));	
        $this->db->where('bardienst.lidnummer <> ', "");
        $this->db->join('wedstrijden', "bardienst.poule = wedstrijden.poule and bardienst.code = wedstrijden.code");
        $this->db->where('accode', 'VHTSC'); //thuiswedstrijden
        $this->db->order_by('datum,tijd,bardienst.poule,bardienst.code', 'asc');
        $query = $this->db->get();
//debug_last_query();
//debug_query_result($query);
//$this->db->_compile_select(); 
//print_r($this->db->last_query());
//die;
        return $query->result();
    }

    function mail_official_selection() {
        $this->load->library('email');
        $this->load->helper('date');

        $sql = 'e.naam as enm, e.tussenvoegsel as etv, e.voornaam as evn, e.email as eml,';
        $sql .= 'f.naam as fnm, f.tussenvoegsel as ftv, f.voornaam as fvn, f.email as fml,';
        $sql .= 'g.naam as gnm, g.tussenvoegsel as gtv, g.voornaam as gvn, g.email as gml,';
        $sql .= 'h.naam as hnm, h.tussenvoegsel as htv, h.voornaam as hvn, h.email as hml,';
        $sql .= 'i.naam as inm, i.tussenvoegsel as itv, i.voornaam as ivn, i.email as iml,';
        $sql .= 'j.naam as jnm, j.tussenvoegsel as jtv, j.voornaam as jvn, j.email as jml';
        $this->db->select('y.naam as ynm, y.tussenvoegsel as ytv, y.voornaam as yvn, y.email as yml');
        $this->db->select('z.naam as znm, z.tussenvoegsel as ztv, z.voornaam as zvn, z.email as zml');
        $this->db->select('STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum', false);
        $this->db->select('datum, tijd, thuis, uit');
        $this->db->select($sql);
        $this->db->from('wedstrijden as a');
        $this->db->join('leden as e', "e.lidnr = a.official03", "left");
        $this->db->join('leden as f', "f.lidnr = a.official04", "left");
        $this->db->join('leden as g', "g.lidnr = a.official05", "left");
        $this->db->join('leden as h', "h.lidnr = a.official06", "left");
        $this->db->join('leden as i', "i.lidnr = a.official07", "left");
        $this->db->join('leden as j', "j.lidnr = a.official08", "left");
        $this->db->join('leden as y', "y.lidnr = a.official01", "left");
        $this->db->join('leden as z', "z.lidnr = a.official02", "left");
//$this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  >= ', 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)', false);
        $this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  >= ', 'CURDATE()', false);
        $this->db->where('naam03 <> ', "");
        $this->db->where('accode', 'VHTSC');
//$sql=$sql . ' where STR_TO_DATE(datum, "%d-%m-%Y")  >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
        $this->db->order_by('dt_datum, tijd', 'asc');
//echo $sql;
//die;

        $query = $this->db->get();
        debug_last_query();
        debug_query_result($query);
//die; 

        if ($query->num_rows() > 0) {
            $this->email->attach('/home/sites/forwodians.nl/web/temp/Digitaal wedstrijd formulier.pdf');
//$this->email->attach('/home/sites/forwodians.nl/web/temp/minispelregels_samenvatting.pdf');
            foreach ($query->result() as $rij) {
                $this->email->clear();
                $this->wedstrijddatum = $rij->datum;
                $this->wedstijdtijd = $rij->tijd;
                $this->naam_tafelaar1 = $rij->evn . " " . $rij->etv . " " . $rij->enm;
                $this->naam_tafelaar2 = $rij->fvn . " " . $rij->ftv . " " . $rij->fnm;
                $this->naam_tafelaar3 = $rij->gvn . " " . $rij->gtv . " " . $rij->gnm;
                $this->naam_tafelaar4 = $rij->hvn . " " . $rij->htv . " " . $rij->hnm;
                $this->naam_tafelaar5 = $rij->ivn . " " . $rij->itv . " " . $rij->inm;
                $this->naam_tafelaar6 = $rij->jvn . " " . $rij->jtv . " " . $rij->jnm;
                $this->naam_scheidsrechter1 = $rij->yvn . " " . $rij->ytv . " " . $rij->ynm;
                $this->naam_scheidsrechter2 = $rij->zvn . " " . $rij->ztv . " " . $rij->znm;
                $this->namentafelaars = trim($this->naam_tafelaar1) . $this->check_length(trim($this->naam_tafelaar2), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar2) . $this->check_length(trim($this->naam_tafelaar3), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar3) . $this->check_length(trim($this->naam_tafelaar4), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar4) . $this->check_length(trim($this->naam_tafelaar5), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar5) . $this->check_length(trim($this->naam_tafelaar6), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar6);
                $this->namenscheidsrechters = trim($this->naam_scheidsrechter1) . $this->check_length(trim($this->naam_scheidsrechter2), 0, ", ");
                $this->namenscheidsrechters .= trim($this->naam_scheidsrechter2);

//$this->namentafelaars = trim($this->naam_tafelaar1.$this->naam_tafelaar2.$this->naam_tafelaar3.$this->naam_tafelaar4.$this->naam_tafelaar5.$this->naam_tafelaar6);
                $list = array();
                if (!is_null($rij->eml)) {
                    $list = $this->add_to_arr($list, $rij->eml);
                }
                if (!is_null($rij->fml)) {
                    $list = $this->add_to_arr($list, $rij->fml);
                }
                if (!is_null($rij->gml)) {
                    $list = $this->add_to_arr($list, $rij->gml);
                }
                if (!is_null($rij->hml)) {
                    $list = $this->add_to_arr($list, $rij->hml);
                }
                if (!is_null($rij->iml)) {
                    $list = $this->add_to_arr($list, $rij->iml);
                }
                if (!is_null($rij->jml)) {
                    $list = $this->add_to_arr($list, $rij->jml);
                }
                if (!is_null($rij->yml)) {
                    $list = $this->add_to_arr($list, $rij->yml);
                }
                if (!is_null($rij->zml)) {
                    $list = $this->add_to_arr($list, $rij->zml);
                }

//$list = array($rij->eml, $rij->fml, $rij->gml, $rij->hml, $rij->iml, $rij->jml, $rij->yml, $rij->zml);
//$list = array("test@edelaar.nl");
//echo "ook";
                echo "<pre>";
                print_r($list); //die;
                echo "</pre>";
                $this->email->to($list);
                $this->thuis_ploeg = $rij->thuis;
                $this->uit_ploeg = $rij->uit;

                $this->email->from('forwodians.basketbal@gmail.com', 'wedstrijdCie Forwodians');
                $this->email->reply_to('forwodians.basketbal@gmail.com');
//        $this->email->cc('another@another-example.com');
                $bcc_list = array('fw_check@edelaar.nl');
                $this->email->bcc($bcc_list);
//echo "hier";
//$subj = imap_mime_header_decode($this->mail_subject_tafelaars());
//var_dump($subj);
                $this->email->subject($this->mail_subject_tafelaars());
//$this->email->subject($subj);
                $mail_body = $this->mail_body_tafelaars();
//   echo $mail_body;
                $this->email->message($mail_body);
                print_r($this->email);
//die;
                $this->email->send();
                echo $this->email->print_debugger();
                echo "<br><hr><br>";
//die;
            }
        }
//print_r($this->db->last_query());
//die;
//debug_query_result($query);
        return;
    }

    function add_to_arr($arr, $element) {
//echo "hier";
        $hlp_arr = explode(",", $element);
        foreach ($hlp_arr as $key => $value) {
            $email = trim($value);
            if (!filter_var(($email), FILTER_VALIDATE_EMAIL)) {
                echo "fout adres" . $email;
            }

            array_push($arr, $email);
        }
        return($arr);
    }

    function mail_official_selection_herinnering() {
        $this->load->library('email');
        $this->load->helper('date');

        $sql = 'e.naam as enm, e.tussenvoegsel as etv, e.voornaam as evn, e.email as eml,';
        $sql .= 'f.naam as fnm, f.tussenvoegsel as ftv, f.voornaam as fvn, f.email as fml,';
        $sql .= 'g.naam as gnm, g.tussenvoegsel as gtv, g.voornaam as gvn, g.email as gml,';
        $sql .= 'h.naam as hnm, h.tussenvoegsel as htv, h.voornaam as hvn, h.email as hml,';
        $sql .= 'i.naam as inm, i.tussenvoegsel as itv, i.voornaam as ivn, i.email as iml,';
        $sql .= 'j.naam as jnm, j.tussenvoegsel as jtv, j.voornaam as jvn, j.email as jml';
        $this->db->select('y.naam as ynm, y.tussenvoegsel as ytv, y.voornaam as yvn, y.email as yml');
        $this->db->select('z.naam as znm, z.tussenvoegsel as ztv, z.voornaam as zvn, z.email as zml');
        $this->db->select('STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum', false);
        $this->db->select('datum, tijd, thuis, uit');
        $this->db->select($sql);
        $this->db->from('wedstrijden as a');
        $this->db->join('leden as e', "e.lidnr = a.official03", "left");
        $this->db->join('leden as f', "f.lidnr = a.official04", "left");
        $this->db->join('leden as g', "g.lidnr = a.official05", "left");
        $this->db->join('leden as h', "h.lidnr = a.official06", "left");
        $this->db->join('leden as i', "i.lidnr = a.official07", "left");
        $this->db->join('leden as j', "j.lidnr = a.official08", "left");
        $this->db->join('leden as y', "y.lidnr = a.official01", "left");
        $this->db->join('leden as z', "z.lidnr = a.official02", "left");
//$this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  >= ', 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)', false);
        $this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  >= ', 'CURDATE()', false);
        $this->db->where('STR_TO_DATE(datum, "%d-%m-%Y")  < ', 'DATE_ADD(CURDATE(), INTERVAL 6 DAY)', false);
        $this->db->where('naam03 <> ', "");
        $this->db->where('accode', 'VHTSC');
//$sql=$sql . ' where STR_TO_DATE(datum, "%d-%m-%Y")  >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
        $this->db->order_by('dt_datum, tijd', 'asc');
        echo $sql;
//die;

        $query = $this->db->get();
        debug_last_query();
        debug_query_result($query);
//die; 

        if ($query->num_rows() > 0) {
            $this->email->attach('/home/sites/forwodians.nl/web/temp/Digitaal wedstrijd formulier.pdf');
//$this->email->attach('/home/sites/forwodians.nl/web/temp/minispelregels_samenvatting.pdf');				
            foreach ($query->result() as $rij) {
                $this->email->clear();
                $this->wedstrijddatum = $rij->datum;
                $this->wedstijdtijd = $rij->tijd;
                $this->naam_tafelaar1 = $rij->evn . " " . $rij->etv . " " . $rij->enm;
                $this->naam_tafelaar2 = $rij->fvn . " " . $rij->ftv . " " . $rij->fnm;
                $this->naam_tafelaar3 = $rij->gvn . " " . $rij->gtv . " " . $rij->gnm;
                $this->naam_tafelaar4 = $rij->hvn . " " . $rij->htv . " " . $rij->hnm;
                $this->naam_tafelaar5 = $rij->ivn . " " . $rij->itv . " " . $rij->inm;
                $this->naam_tafelaar6 = $rij->jvn . " " . $rij->jtv . " " . $rij->jnm;
                $this->naam_scheidsrechter1 = $rij->yvn . " " . $rij->ytv . " " . $rij->ynm;
                $this->naam_scheidsrechter2 = $rij->zvn . " " . $rij->ztv . " " . $rij->znm;
                $this->namentafelaars = trim($this->naam_tafelaar1) . $this->check_length(trim($this->naam_tafelaar2), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar2) . $this->check_length(trim($this->naam_tafelaar3), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar3) . $this->check_length(trim($this->naam_tafelaar4), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar4) . $this->check_length(trim($this->naam_tafelaar5), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar5) . $this->check_length(trim($this->naam_tafelaar6), 0, ", ");
                $this->namentafelaars .= trim($this->naam_tafelaar6);
                $this->namenscheidsrechters = trim($this->naam_scheidsrechter1) . $this->check_length(trim($this->naam_scheidsrechter2), 0, ", ");
                $this->namenscheidsrechters .= trim($this->naam_scheidsrechter2);

//$this->namentafelaars = trim($this->naam_tafelaar1.$this->naam_tafelaar2.$this->naam_tafelaar3.$this->naam_tafelaar4.$this->naam_tafelaar5.$this->naam_tafelaar6);
//                $list = array($rij->eml, $rij->fml, $rij->gml, $rij->hml, $rij->iml, $rij->jml, $rij->yml, $rij->zml);
                $list = array();
                if (!is_null($rij->eml)) {
                    $list = $this->add_to_arr($list, $rij->eml);
                }
                if (!is_null($rij->fml)) {
                    $list = $this->add_to_arr($list, $rij->fml);
                }
                if (!is_null($rij->gml)) {
                    $list = $this->add_to_arr($list, $rij->gml);
                }
                if (!is_null($rij->hml)) {
                    $list = $this->add_to_arr($list, $rij->hml);
                }
                if (!is_null($rij->iml)) {
                    $list = $this->add_to_arr($list, $rij->iml);
                }
                if (!is_null($rij->jml)) {
                    $list = $this->add_to_arr($list, $rij->jml);
                }
                if (!is_null($rij->yml)) {
                    $list = $this->add_to_arr($list, $rij->yml);
                }
                if (!is_null($rij->zml)) {
                    $list = $this->add_to_arr($list, $rij->zml);
                }

//$list = array("test@edelaar.nl");
                $this->email->to($list);
                $this->thuis_ploeg = $rij->thuis;
                $this->uit_ploeg = $rij->uit;

                $this->email->from('wedstrijdcie@forwodians.nl', 'wedstrijdCie Forwodians');
                $this->email->reply_to('wedstrijdcie_forwodians');
//        $this->email->cc('another@another-example.com');
                $bcc_list = array('fw_check@edelaar.nl');
                $this->email->bcc($bcc_list);
                $this->email->subject($this->mail_subject_tafelaars());
                $mail_body = $this->mail_body_tafelaars();
//   echo $mail_body;
                $this->email->message($mail_body);
//$this->email->attach('wp-content/plugins/wedstrijd/Handleiding_bardienstv4.pdf');
//print_r($this->email);
//die;
                $this->email->send();
            }
        }
//print_r($this->db->last_query());
//die;
//debug_query_result($query);
        return;
    }

    function mail_body_tafelaars() {
//private strBody;
        $this->load->helper('date');
        $this->load->helper('url');

        $strBody = '<br>Beste ' . $this->namenscheidsrechters . $this->check_length($this->namenscheidsrechters, 1, " en ") . $this->namentafelaars;
        $strBody .= "<br><br>";
        $strBody .= "Jullie zijn ingedeeld om de wedstrijd van " . $this->thuis_ploeg . " tegen " . $this->uit_ploeg;
        $strBody .= " te fluiten en te tafelen!  ";
        $strBody .= "<br>Aanvang wedstrijd is " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2) . " op " . $this->wedstrijddatum;
        $strBody .= "<br><br>";
        $strBody .= "<br>De scheidsrechters zijn: " . $this->namenscheidsrechters;
        $strBody .= "<br>De tafelaars zijn: " . $this->namentafelaars;
        $strBody .= "<br><br>";
        $strBody .= "Kijk a.u.b. op de website voor eventuel wijzigingen ";
        $strBody .= "<br>Kom a.u.b. op tijd (half uur van te voren), als je niet kunt zorg dan zelf voor vervanging";
        $strBody .= "<br><br>Vriendelijke groet";
        $strBody .= "<br><br>de wedstrijdcommissie";
        $strBody .= "<br><br>Vragen of opmerkingen richten aan de TC svp";
//$strBody.=  "nb. hier is al aandacht aan geschonken via de mail gisterenavond, de website en facebook. Dus wordt het nu bekend verondersteld."; 
//$strBody.=  "de procedure is nu aangepast en naar verwachting krijgen jullie nu voortaan sneller bericht."; 
//$strBody.=  "poule = " . $this->poule."<br><hr><br>";   
//echo "<br>".$strBody;
        return $strBody;
    }

    function mail_subject_tafelaars() {
//return "Herinnering fluiten - tafelen " . $this->namenscheidsrechters . " en " . $this->namentafelaars . " dd " . $this->wedstrijddatum . " " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2);
        return "Herinnering fluiten - tafelen dd " . $this->wedstrijddatum . " " . substr($this->wedstijdtijd, 0, 2) . ":" . substr($this->wedstijdtijd, 3, 2);
    }

    function check_length($strString, $minlength, $rtnvalue) {
        if (strlen($strString) > $minlength) {
//echo "<BR>".$strString. " " . strlen($strString);
            return $rtnvalue;
        }
        return(" ");
    }

    function read_leden_excel() {
        $this->load->library('Excel');
        $objReader = new PHPExcel_Reader_Excel5();
        $objReader->setReadDataOnly(true);
//echo '/home/sites/forwodians.nl/web/temp/Leden.xls' ;die;
//$objPHPExcel = $objReader->load( dirname(__FILE__) . '/temp/Leden.xls' );
        $objPHPExcel = $objReader->load('/home/sites/forwodians.nl/web/temp/Leden.xls');
//$objPHPExcel = $objReader->load('c:/temp/Leden.xls');
//var_dump($objPHPExcel);die;
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
        $array_data = array();
        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            if (1 == $row->getRowIndex())
                continue; //skip first row
            $rowIndex = $row->getRowIndex();
            $array_data[$rowIndex] = array('A' => '', 'B' => '', 'C' => '', 'D' => '');

            foreach ($cellIterator as $cell) {
//var_dump($cell);
                if ('A' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('B' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('C' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('D' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('E' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('F' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('G' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('H' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('I' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('J' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), 'DD-MM-YYYY');
                } else if ('K' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('L' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('M' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('N' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('O' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('P' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('Q' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                } else if ('R' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), 'DD-MM-YYYY');
                } else if ('S' == $cell->getColumn()) {
                    $array_data[$rowIndex][$cell->getColumn()] = $this->null_test($cell->getCalculatedValue());
                }
            }
//var_dump([$rowIndex]);
//var_dump($array_data[$rowIndex]);
            $this->add_lid($array_data[$rowIndex]);
//add or update ledenlijst + referee bestand 
//verwijder de niet meer gebruikte leden
//lijst de verschillen uit !!
        }
        $dd = date('Y-m-d');
//echo "<br>1" . $dd;
        $this->delete_leden_niet_gemuteerd($dd);
//var_dump($array_data);
    }

    function null_test($waarde) {
//echo "<br>".$waarde;
        if (!isset($waarde)) {
            if (is_null($waarde)) {
                return "";
            }
            return "";
        }
        return $waarde;
    }

    function add_lid($data) {
        $this->load->helper('date');
// var_dump($data);
        $table = "leden";
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('lidnr', $data["A"]);
        $query = $this->db->get();
//var_dump($query);
//die;
        $hlp_new = 1;
        if ($query->num_rows > 0) {
//update
            $this->db->where('lidnr', $data["A"]);
            $this->db->delete($table);
            $hlp_new = 0;
        }
//add
        $data_i = array(
            'lidnr' => $data["A"],
            'voornaam' => $data["B"],
            'Naam' => $data["C"],
            'tussenvoegsel' => $data["D"],
            'voorletters' => $data["E"],
            'straat' => $data["F"],
            'huisnr' => $data["G"],
            'postcode' => $data["H"],
            'woonplaats' => $data["I"],
            'gebdat' => $data["J"],
            'geslacht' => $data["K"],
            'telnr' => $data["L"],
            'email' => str_replace(";", ", ", $data["M"]),
            'lidsoort' => $data["N"],
            'extraoms' => $data["O"],
            'lidveld1' => $data["P"],
            'lidveld2' => $data["Q"],
            'datmut' => $data["R"],
            'mobiel' => $data["S"]
        );
        $this->db->insert($table, $data_i);
        if ($hlp_new == 1) {
            $this->load->model('ref_model');
            echo "<br>add ref";
            $this->ref_model->add_referee($data_i);
        }
    }

    function get_lid($lidnr) {
        $this->db->flush_cache();
        $table = "leden";
        $this->db->select('*');
        $this->db->where('lidnr', $lidnr);
        $this->db->from($table);
        $query = $this->db->get();
//print_r($this->db->last_query());
//print_r($query->result());
        return $query->result();
    }

    function delete_leden_niet_gemuteerd($datum) {
        $this->load->helper('date');
        $this->load->model('team_model');
//selecteer alle leden die niet gemuteerd zijn en die niet in teams voorkomen
        $table = 'leden';
        $this->db->select('lidnr');
        $this->db->where('tbl_upd_datum <', $datum);
        $this->db->from($table);
        $query = $this->db->get();
//print_r($this->db->last_query());
        foreach ($query->result() as $row) {
//uitzonderingen
//var_dump($row);
            if ($this->team_model->get_poulid_from_team_lidnr($row->lidnr) == "no_poule") {
                $this->delete_lid($row->lidnr);
                echo "<br>" . $row->lidnr . " verwijderd uit leden!";
            } else {
                echo "<br>" . $row->lidnr . " komt niet voor in een team!";
            }
        }
    }

    function delete_lid($lidnr) {
        $table = 'leden';
        $this->db->delete($table, array('lidnr' => $lidnr));
    }

    function create_agenda($lidnr) {
// the iCal date format. Note the Z on the end indicates a UTC timestamp.
//define('DATE_ICAL', 'Ymd\THis');
// max line length is 75 chars. New line is \\n

        $output = "BEGIN:VCALENDAR\r\n";
        $output .= "METHOD:PUBLISH\r\n";
        $output .= "VERSION:2.0\r\n";
        $output .= "PRODID:-//Forwodians leden kalender\r\n";
        $output .= "X-WR-CALNAME:Basketbal kalender (Forwodians)\r\n";
        $output .= "X-WR-TIMEZONE:Europe/Amsterdam\r\n";
//$lidnr = "213600420";
        $output .= $this->create_wedstrijd_events($lidnr);
        $output .= $this->create_bank_events($lidnr);
        $output .= $this->create_bardienst_events($lidnr);
        $output .= $this->create_chauffeur_events($lidnr, 1);
        $output .= $this->create_chauffeur_events($lidnr, 2);
        $output .= $this->create_chauffeur_events($lidnr, 3);
        $output .= $this->create_scheidsrechter_events($lidnr, 1);
        $output .= $this->create_scheidsrechter_events($lidnr, 2);
        $output .= $this->create_tafelaar_events($lidnr, 3);
        $output .= $this->create_tafelaar_events($lidnr, 4);
        $output .= $this->create_tafelaar_events($lidnr, 5);
// close calendar
        $output .= "END:VCALENDAR";

//$dir = "C:/temp/ics/";
//$file = $dir.$lidnr.'.ics';
        $file = '../temp/' . $lidnr . '.ics';
        file_put_contents($file, $output);
//echo $output;
    }

    function create_wedstrijd_events($lidnr) {
//UID start met WD !! moet uniek zijn voor poule en code
        $this->db->select('*');
        $this->db->from("wedstrijden as w");
        $this->db->join("teams as t", "w.poule=t.poulid");
        $this->db->join("teamindeling as ti", "ti.team=t.code");
        $this->db->join("leden as l", "l.lidnr = ti.lidnummer");
        $this->db->where('l.lidnr', $lidnr);
        $this->db->where('w.tijd <>', "00:00");
        $query = $this->db->get();

        $output = "";
        foreach ($query->result() as $wedstrijd):
            $wd = new stdClass();
            $wd->summery = "wedstrijd " . $wedstrijd->poule . $wedstrijd->code . "=>" . $wedstrijd->thuis . "-" . $wedstrijd->uit;
            $wd->start_datum = $wedstrijd->datum . $wedstrijd->tijd;
            $tijd2 = new DateTime($wedstrijd->datum . $wedstrijd->tijd);
            $tijd2->add(new DateInterval('PT2H'));
            $wd->end_datum = $wedstrijd->datum . $tijd2->format('H:i:s');
            $wd->mut_datum = $wedstrijd->mutdatum;
            if ($wedstrijd->accode === "VHTSC") {
                $wd->location = $wedstrijd->accommodatie;
            } else {
                $wd->location = $wedstrijd->accommodatie . ", " . $wedstrijd->adres . ", " . $wedstrijd->postcode . ", " . $wedstrijd->plaats;
            }
            $wd->uid = "WD" . $wedstrijd->poule . $wedstrijd->code . $wedstrijd->SEIZOEN;
            $wd->status = "CONFIRMED";
            $wd->categorie = "basketbal,wedstrijd";
            $lid_obj = $this->get_lid($lidnr);
            $lid = $lid_obj[0];
            $wd->description = "agenda van " . $lid->Voornaam . " " . $lid->Tussenvoegsel . " " . $lid->Naam . "  " . $wd->summery . "  " . $wd->location . "\n";
            $output .= $this->create_event($wd);
        endforeach;
        return $output;
    }

    function create_bank_events($lidnr) {
//UID start met WD !! moet uniek zijn voor poule en code
        $this->db->select('*');
        $this->db->from("wedstrijden as w");
        $this->db->join("teams as t", "w.poule=t.poulid");
        $this->db->join("teamindeling as ti", "ti.bank_team=t.code");
        $this->db->join("leden as l", "l.lidnr = ti.lidnummer");
        $this->db->where('l.lidnr', $lidnr);
        $this->db->where('w.tijd <>', "00:00");
        $query = $this->db->get();

        $output = "";
        foreach ($query->result() as $wedstrijd):
            $wd = new stdClass();
            $wd->summery = "wedstrijd " . $wedstrijd->poule . $wedstrijd->code . "=>" . $wedstrijd->thuis . "-" . $wedstrijd->uit;
            $wd->start_datum = $wedstrijd->datum . $wedstrijd->tijd;
            $tijd2 = new DateTime($wedstrijd->datum . $wedstrijd->tijd);
            $tijd2->add(new DateInterval('PT2H'));
            $wd->end_datum = $wedstrijd->datum . $tijd2->format('H:i:s');
            $wd->mut_datum = $wedstrijd->mutdatum;
            if ($wedstrijd->accode === "VHTSC") {
                $wd->location = $wedstrijd->accommodatie;
            } else {
                $wd->location = $wedstrijd->accommodatie . ", " . $wedstrijd->adres . ", " . $wedstrijd->postcode . ", " . $wedstrijd->plaats;
            }
            $wd->uid = "WD" . $wedstrijd->poule . $wedstrijd->code . $wedstrijd->SEIZOEN;
            $wd->status = "CONFIRMED";
            $wd->categorie = "basketbal,wedstrijd";
            $lid_obj = $this->get_lid($lidnr);
            $lid = $lid_obj[0];
            $wd->description = "agenda van " . $lid->Voornaam . " " . $lid->Tussenvoegsel . " " . $lid->Naam . "  " . $wd->summery . "  " . $wd->location . "\n";
            $output .= $this->create_event($wd);
        endforeach;
        return $output;
    }

    function create_bardienst_events($lidnr) {
//UID start met BR !! moet uniek zijn voor poule en code
//SELECT * FROM `wedstrijden` as w
//join teams as t on w.poule=t.poulid
//join teamindeling as ti on BINARY  ti.team=t.code
//join leden as l on l.lidnr = ti.lidnummer
//join bardienst as b on l.lidnr=b.lidnummer and w.poule=b.poule and BINARY  w.code=b.code
//where l.lidnr="213600420"

        $this->db->select('*');
        $this->db->from("wedstrijden as w");
        $this->db->join("teams as t", "w.poule=t.poulid");
        $this->db->join("teamindeling as ti", "ti.team=t.code");
        $this->db->join("leden as l", "l.lidnr = ti.lidnummer");
        $this->db->join("bardienst as b", "l.lidnr=b.lidnummer and w.poule=b.poule and BINARY  w.code=b.code");
        $this->db->where('l.lidnr', $lidnr);
        $this->db->where('w.tijd <>', "00:00");
        $query = $this->db->get();
//echo $this->db->last_query();
        $output = "";
        foreach ($query->result() as $wedstrijd):
            $wd = new stdClass();
            $wd->summery = "bardienst: " . $wedstrijd->poule . $wedstrijd->code . "=>" . $wedstrijd->thuis . "-" . $wedstrijd->uit;
            $wd->start_datum = $wedstrijd->datum . $wedstrijd->tijd;
            $tijd2 = new DateTime($wedstrijd->datum . $wedstrijd->tijd);
            $tijd2->add(new DateInterval('PT2H'));
            $wd->end_datum = $wedstrijd->datum . $tijd2->format('H:i:s');
            $wd->mut_datum = $wedstrijd->mutdatum;
            if ($wedstrijd->accode === "VHTSC") {
                $wd->location = $wedstrijd->accommodatie;
            } else {
                $wd->location = $wedstrijd->accommodatie . ", " . $wedstrijd->adres . ", " . $wedstrijd->postcode . ", " . $wedstrijd->plaats;
            }
            $wd->uid = "BR" . $wedstrijd->poule . $wedstrijd->code . $wedstrijd->SEIZOEN;
            $wd->status = "CONFIRMED";
            $wd->categorie = "basketbal,bardienst";
            $lid_obj = $this->get_lid($lidnr);
            $lid = $lid_obj[0];
            $wd->description = "agenda van " . $lid->Voornaam . " " . $lid->Tussenvoegsel . " " . $lid->Naam . " ";
            $wd->description .= $wd->summery . "  ";
            $wd->description .= $wd->location . "  ";
            $output .= $this->create_event($wd);
        endforeach;
        return $output;
    }

    function create_chauffeur_events($lidnr, $i) {
        $this->db->select('*');
        $this->db->from("wedstrijden as w");
        $this->db->join("teams as t", "w.poule=t.poulid");
        $this->db->join("teamindeling as ti", "ti.team=t.code");
        $this->db->join("leden as l", "l.lidnr = ti.lidnummer");
        if ($i == 1) {
            $this->db->join("chauffeur as b", "l.lidnr=b.lidnummer1 and w.poule=b.poule and BINARY  w.code=b.code");
        }
        if ($i == 2) {
            $this->db->join("chauffeur as b", "l.lidnr=b.lidnummer2 and w.poule=b.poule and BINARY  w.code=b.code");
        }
        if ($i == 3) {
            $this->db->join("chauffeur as b", "l.lidnr=b.lidnummer3 and w.poule=b.poule and BINARY  w.code=b.code");
        }
        $this->db->where('l.lidnr', $lidnr);
        $this->db->where('w.tijd <>', "00:00");
        $query = $this->db->get();
        echo $this->db->last_query();
        $output = "";
        foreach ($query->result() as $wedstrijd):
            $wd = new stdClass();
            $wd->summery = "chauffeur: " . $wedstrijd->poule . $wedstrijd->code . "=>" . $wedstrijd->thuis . "-" . $wedstrijd->uit;
            $wd->start_datum = $wedstrijd->datum . $wedstrijd->tijd;
            $tijd2 = new DateTime($wedstrijd->datum . $wedstrijd->tijd);
            $tijd2->add(new DateInterval('PT2H'));
            $wd->end_datum = $wedstrijd->datum . $tijd2->format('H:i:s');
            $wd->mut_datum = $wedstrijd->mutdatum;
            if ($wedstrijd->accode === "VHTSC") {
                $wd->location = $wedstrijd->accommodatie;
            } else {
                $wd->location = $wedstrijd->accommodatie . ", " . $wedstrijd->adres . ", " . $wedstrijd->postcode . ", " . $wedstrijd->plaats;
            }
            $wd->uid = "CH" . $wedstrijd->poule . $wedstrijd->code . $wedstrijd->SEIZOEN;
            $wd->status = "CONFIRMED";
            $wd->categorie = "basketbal,chauffeur";
            $lid_obj = $this->get_lid($lidnr);
            $lid = $lid_obj[0];
            $wd->description = "agenda van " . $lid->Voornaam . " " . $lid->Tussenvoegsel . " " . $lid->Naam . "  ";
            $wd->description .= $wd->summery . "  ";
            $wd->description .= $wd->location . "  ";
            $output .= $this->create_event($wd);
        endforeach;
        return $output;
    }

    function create_scheidsrechter_events($lidnr, $i) {
//UID start met WD !! moet uniek zijn voor poule en code
        $this->db->select('*');
        $this->db->from("wedstrijden as w");
        $this->db->join("teams as t", "w.poule=t.poulid");
        $this->db->join("teamindeling as ti", "ti.team=t.code");
        $this->db->join("leden as l", "l.lidnr = ti.lidnummer");
        $this->db->where('w.official0' . $i, $lidnr);
        $this->db->where('w.tijd <>', "00:00");
        $query = $this->db->get();

        $output = "";
        foreach ($query->result() as $wedstrijd):
            $wd = new stdClass();
            $wd->summery = "scheidsrechter " . $wedstrijd->poule . $wedstrijd->code . "=>" . $wedstrijd->thuis . "-" . $wedstrijd->uit;
            $wd->start_datum = $wedstrijd->datum . $wedstrijd->tijd;
            $tijd2 = new DateTime($wedstrijd->datum . $wedstrijd->tijd);
            $tijd2->add(new DateInterval('PT2H'));
            $wd->end_datum = $wedstrijd->datum . $tijd2->format('H:i:s');
            $wd->mut_datum = $wedstrijd->mutdatum;
            if ($wedstrijd->accode === "VHTSC") {
                $wd->location = $wedstrijd->accommodatie;
            } else {
                $wd->location = $wedstrijd->accommodatie . ", " . $wedstrijd->adres . ", " . $wedstrijd->postcode . ", " . $wedstrijd->plaats;
            }
            $wd->uid = "RF" . $wedstrijd->poule . $wedstrijd->code . $wedstrijd->SEIZOEN;
            $wd->status = "CONFIRMED";
            $wd->categorie = "basketbal,scheidsrechter";
            $lid_obj = $this->get_lid($lidnr);
            $lid = $lid_obj[0];
            $wd->description = "agenda van " . $lid->Voornaam . " " . $lid->Tussenvoegsel . " " . $lid->Naam . "  " . $wd->summery . "  " . $wd->location . "\n";
            $output .= $this->create_event($wd);
        endforeach;
        return $output;
    }

    function create_tafelaar_events($lidnr, $i) {
        $this->db->select('*');
        $this->db->from("wedstrijden as w");
        $this->db->join("teams as t", "w.poule=t.poulid");
        $this->db->join("teamindeling as ti", "ti.team=t.code");
        $this->db->join("leden as l", "l.lidnr = ti.lidnummer");
        $this->db->where('w.official0' . $i, $lidnr);
        $this->db->where('w.tijd <>', "00:00");
        $query = $this->db->get();

        $output = "";
        foreach ($query->result() as $wedstrijd):
            $wd = new stdClass();
            $wd->summery = "tafelaar " . $wedstrijd->poule . $wedstrijd->code . "=>" . $wedstrijd->thuis . "-" . $wedstrijd->uit;
            $wd->start_datum = $wedstrijd->datum . $wedstrijd->tijd;
            $tijd2 = new DateTime($wedstrijd->datum . $wedstrijd->tijd);
            $tijd2->add(new DateInterval('PT2H'));
            $wd->end_datum = $wedstrijd->datum . $tijd2->format('H:i:s');
            $wd->mut_datum = $wedstrijd->mutdatum;
            if ($wedstrijd->accode === "VHTSC") {
                $wd->location = $wedstrijd->accommodatie;
            } else {
                $wd->location = $wedstrijd->accommodatie . ", " . $wedstrijd->adres . ", " . $wedstrijd->postcode . ", " . $wedstrijd->plaats;
            }
            $wd->uid = "TF" . $wedstrijd->poule . $wedstrijd->code . $wedstrijd->SEIZOEN;
            $wd->status = "CONFIRMED";
            $wd->categorie = "basketbal,tafelaar";
            $lid_obj = $this->get_lid($lidnr);
            $lid = $lid_obj[0];
            $wd->description = "agenda van " . $lid->Voornaam . " " . $lid->Tussenvoegsel . " " . $lid->Naam . "  " . $wd->summery . "  " . $wd->location . "\n";
            $output .= $this->create_event($wd);
        endforeach;
        return $output;
    }

    function create_event($event) {
        $output = "BEGIN:VEVENT\r\n";
        $output .= "SUMMARY:" . $event->summery . "\r\n";
        $output .= "UID:" . $event->uid . "\r\n";
        $output .= "STATUS:" . strtoupper($event->status) . "\r\n";
        $output .= "DTSTART:" . date(DATE_ICAL, strtotime($event->start_datum)) . "\r\n";
        $output .= "DTEND:" . date(DATE_ICAL, strtotime($event->end_datum)) . "\r\n";
        $output .= "LAST-MODIFIED:" . date(DATE_ICAL, strtotime($event->mut_datum)) . "\r\n";
        $output .= "LOCATION:" . $event->location . "\r\n";
        $output .= "CATEGORIES:" . $event->categorie . "\r\n";
        $output .= "DESCRIPTION:" . $event->description . "\r\n";
        $output .= "END:VEVENT\n";
        return $output;
    }

    function bestaat_lid($lidnr) {
        $this->db->flush_cache();
        $table = "leden";
        $this->db->select('lidnr');
        $this->db->where('lidnr', $lidnr);
        $this->db->from($table);
        $query = $this->db->get();
//print_r($this->db->last_query());
//print_r($query->result());
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

}

?>
