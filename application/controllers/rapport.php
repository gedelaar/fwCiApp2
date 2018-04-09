<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rapport extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        // $this->load->model('leden_model');
        // $this->load->model('mdl_excel');
        // load the database and connect to MySQL
        //  $this->load->database();
    }

    public function __construct() {
        // load Controller constructor
        parent::__construct();
        // load the model we will be using
        //$this->load->library('Excel');
        $this->load->model('leden_model');
        $this->load->model('mdl_excel');
        // load the database and connect to MySQL
        $this->load->database();

        //$this->output->enable_profiler(TRUE);            
    }

    function BarOverzicht() {
        $this->load->library("Excel");
        $this->load->model('mdl_excel');
        //$data['query'] = $this->leden_model->get_leden();
        //print_r($data);
        //$this->mdl_excel->Maak_test_excel();
        //$this->mdl_excel->Maak_excel_Overzicht_van_bar($data);
        $this->mdl_excel->create_bar_ovz($data);
    }

    function WedstrijdOverzicht() {
        //$this->load->library("Excel");
        $this->load->model('mod_wedstrijd');
        //$data['query'] = $this->leden_model->get_leden();
        //print_r($data);
        //$this->mdl_excel->Maak_test_excel();
        $this->mod_wedstrijd->create_wedstrijd_ovz($data);
    }

    function BarOverzichtTest() {
        $this->load->library("Excel");
        $this->excel->setActiveSheetIndex(0);
        $data['query'] = $this->leden_model->get_leden();
        //$data = $this->Your_model->findAll();
        $this->mdl_excel->SimpleTest();
        //$this->excel->stream('filename.xls',$data);		
        //print_r($data);
        //S$this->mdl_excel->Maak_excel_Overzicht_van_bar($data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */