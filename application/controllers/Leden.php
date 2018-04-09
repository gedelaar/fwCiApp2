<?php

/* CONTROLLER
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of leden
 *
 * @author Gerard
 */
class leden extends CI_Controller {

    //put your code here

    function getall() {
        $data['query'] = $this->leden_model->leden_getall();
        $this->load->view('leden_getall', $data);
    }

    function nobardienst($id) {
        $this->UpdateBardienst($id, 9);
    }

    function neverbardienst($id) {
        $this->UpdateBardienst($id, 8);
    }

    function yesbardienst($id) {
        $this->UpdateBardienst($id, 2);
        echo "Uw reactie is verwerkt. Bedankt hiervoor.";
    }

    private function UpdateBardienst($id, $code) {
        if ($id < 5) {
            $this->leden_model->leden_yesbar($this->CreateNewId($id), $code);
            $this->feedback_mail();
        } else {
            $this->leden_model->leden_nobar($this->CreateNewId($id), $code);
            $this->feedback_mail();
            $this->mail_all();
        }
    }

    private function CreateNewId($id) {
        return(substr($id, 1, substr($id, 0, 1)));
    }

    function vul_diensten() {
        $this->load->model('functions');
        $data['query'] = $this->leden_model->vul_bar_init();
        $data['query'] = $this->leden_model->vul_chauffeur_init();
        $data['query'] = $this->leden_model->vul_opendiensten_in();
        echo "De diensten zijn ingevuld.";
    }

    //toevoegen van een lid dat geen bardienst hoeft te draaien
    function bar_uitzondering_lid($id) {
        $this->leden_model->vul_uitzondering_lid_in($id);
    }

    //toevoegen van een team cq poule dat geen bardienst draait.
    function bar_uitzondering_team($poule) {
        $this->leden_model->vul_uitzondering_team_in($poule);
    }

    function mail_all() {
        $this->leden_model->mail_selection();
        echo "Uw reactie is verwerkt. Bedankt hiervoor.";
        echo "De mail is verzonden.";
    }

    function feedback_mail() {
        $this->leden_model->mail_selection_feedback();
    }

    function herinner_bar_mail() {
        $this->leden_model->mail_selection_herinnering();
        echo "Uw reactie is verwerkt. Bedankt hiervoor.";
        //echo $this->email->print_debugger();
    }

    function test_mail() {
        $this->load->model('leden_model');
        $this->load->library('email');
        $this->email->from('forwodians.basketbal@gmail.com', 'FW');
        $this->email->to('<test@edelaar.nl>, <23872873de21f2@ikbenspamvrij.nl>');
        // $this->email->to('test2@edelaar.nl');
        // $this->email->to('test3@edelaar.nl');
        $this->email->reply_to('ge@edelaar.nl', 'bbfw');
//        $this->email->cc('another@another-example.com');
        //$this->email->bcc('bcc-fw@edelaar.nl');
        $this->email->subject('testmail');
        $this->email->message('testmail');
        $this->email->attach('/home/sites/forwodians.nl/web/temp/mini_spelregels.pdf');
        $this->email->attach('/home/sites/forwodians.nl/web/temp/minispelregels_samenvatting.pdf');
        echo "<pre>";
        print_r($this->email);
        echo "</pre>";

        $this->email->send();
        echo "<br>===<br>";
        echo $this->email->print_debugger();
    }

    function email_aanpassing($lidnr, $new_email) {
        $this->load->model('leden_model');
        $this->leden_model->email_aanpassing_uitvoeren($lidnr, $new_email);
    }

    function rxml_wedstrijd() {
        $this->load->model('functions');
        $this->load->model('mod_wedstrijd');
        $this->mod_wedstrijd->put_xml_to_sql("fw");
        $this->mod_wedstrijd->put_xml_to_sql("nw");
        $this->mod_wedstrijd->del_wedstrijdrecords();
        echo "Wedstrijd gegevens zijn ingelezen";

        //$this->load->view('show_XML');
    }

    function read_officials() {
        $this->load->library('csvreader');
        //$filePath = '../temp/officials.csv';
        //$data['csvData'] = $this->csvreader->parse_file($filePath);
        //$this->load->view('csv_view', $data);
        $this->load->model('mod_wedstrijd');
        //$this->rxml_wedstrijd(); => is niet zo nodig
        $this->mod_wedstrijd->csv_to_sql();
        //$this->mail_officials();
        echo "<br>Scheidsrechters en tafelaars zijn ingelezen";
        echo "<br>Nog wel mailen  www.forwodians.nl/fwBar/index.php/leden/mail_officials";
    }

    function mail_officials() {
        $this->load->model('leden_model');
        $this->leden_model->mail_official_selection();
        echo "<br>De officials zijn gemailed.";
        //echo $this->email->print_debugger();
        //echo "De mail is verzonden.";
    }

    function mail_officials_herinnering() {
        $this->load->model('leden_model');
        $this->leden_model->mail_official_selection_herinnering();
        echo "<br>De officials zijn gemailed (reminder).";
        //echo $this->email->print_debugger();
        //echo "De mail is verzonden.";
    }

    /* function test(){
      $this->load->model('functions');
      //$this->load->model('filewriter');
      $this->load->model('mod_wedstrijd');
      $this->mod_wedstrijd->del_wedstrijdrecords();

      } */

    function read_leden() {

        $this->load->model('leden_model');
        $this->leden_model->read_leden_excel();
        echo "<br>klaar, leden ingelezen";
    }

    function make_calendar() {
        $this->load->model('leden_model');
        $leden = $this->leden_model->leden_getall();
        //var_dump($leden);
        foreach ($leden as $lid) {
            //var_dump($lid);
            $this->leden_model->create_agenda($lid->Lidnr);
        }
        echo "<br>klaar, agenda gereed";
    }

}

?>
