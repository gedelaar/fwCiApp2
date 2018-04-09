<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ref_model
 *
 * @author gerard
 */
class ref_model extends CI_Model {

//put your code here
    public $code;
    public $debug;

//put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->output->enable_profiler(TRUE);
        $this->debug = 1;
    }

    function ref_getall($data) {
//eerst alle refs ophalen
        $this->db->select('id, r.LIDNR, ZOEKNAAM, AWGR, RATIO, CATEGORIE,Lidsoort');
        $this->db->from('referee as r');
        $this->db->where('CATEGORIE', $data['wedstrijd_role']);
        $this->db->join('leden as l', 'l.lidnr = r.lidnr');
        $query = $this->db->get();
//var_dump($query->result_array());die;
        return $query->result_array();
    }

    function ref_getall_potential($data) {
//eerst alle refs ophalen die een rol spelen
        $this->db->select('id, r.LIDNR, ZOEKNAAM, AWGR, RATIO, CATEGORIE,Lidsoort');
        $this->db->from('referee as r');
        $this->db->where('CATEGORIE', $data['wedstrijd_role']);
        $this->db->where('AWGR <>', 'Z');
        $this->db->where('AWGR <>', '9');
        $this->db->join('leden as l', 'l.lidnr = r.lidnr');
        $query = $this->db->get();
        //echo $this->db->last_query(); die;
        return $query->result_array();
    }

    function ref_exclude_date($data, $lidnr) {
//datum range vergelijken
        $datum = $data['wedstrijd_datum'];
//$this->db->where('excl_date >=', $datum);
//$this->db->where('excl_date_till <', $datum);
        $this->db->where('excl_date <=', "str_to_date('$datum', '%d-%m-%Y')", FALSE);
        $this->db->where('excl_date_till >=', "str_to_date('$datum', '%d-%m-%Y')", FALSE);
        $bln_lid = 0;
        if ($lidnr <> "") {
            $this->db->where('lidnummer', $lidnr);
            $bln_lid = 1;
        }
        $query = $this->db->get('referee_exclude');
//        echo $this->db->last_query();die;
        if ($bln_lid === 1) {
//echo $query->num_rows();die;
            if ($query->num_rows() > 0) {
                $key = $data['refs'][0];
                $this->ref_log($key, "exclude date", $data);
                return true;
            }
            return false;
        } else {
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $ref_arr = array();
                    foreach ($data['refs'] as $key) {
                        if ($key['LIDNR'] <> $row->lidnummer) {
                            $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
//var_dump($ref_arr);die;
                        } else {
                            $this->ref_log($key, "exclude date", $data);
                        }
                    }
                }
            } else {
                $ref_arr = $data['refs'];
            }
            return $ref_arr;
        }
    }

    function ref_exclude_blessure($data, $lidnr) {
        $datum = $data['wedstrijd_datum'];
        $this->db->where('blessure', true);
        $bln_lid = 0;
        if ($lidnr <> "") {
            $this->db->where('lidnummer', $lidnr);
            $bln_lid = 1;
        }
        $query = $this->db->get('referee_exclude');
//echo $this->db->last_query();
        if ($bln_lid === 1) {
//echo $query->num_rows();die;
            if ($query->num_rows() > 0) {
                $key = $data['refs'][0];
//$this->ref_log($key, "geblesseerd", $data);
                return true;
            }
            return false;
        } else {
            $ref_arr = $data['refs'];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
//var_dump($row);
                    $data['refs'] = $ref_arr;
                    $ref_arr = array();
                    foreach ($data['refs'] as $key) {
                        if ($key['LIDNR'] <> $row->lidnummer) {
//echo "<br>push -> ".$key['LIDNR'];
                            $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                        } else {
//$this->ref_log($key, "geblesseerd", $data);
                        }
                    }
                }
            } else {
//echo "hierx";
                $ref_arr = $data['refs'];
            }
//die;
//var_dump($ref_arr);die;
            return $ref_arr;
        }
    }

    function ref_exclude_gemeente($data, $lidnr) {
        $tabel = "leden";
        if ($lidnr <> "") {
            $this->db->where('lidnr', $lidnr);
            $bln_lid = 1;
        }
        $this->db->select('lidnr, woonplaats');
        $query = $this->db->get($tabel);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
//echo "<br>". $row->woonplaats;
//echo "<br>". $this->get_gem_u($row->woonplaats);
                return $this->get_gem_u($row->woonplaats);
            }
        }
        echo "fout !!! kan lid niet vinden";
        die;
    }

    function ref_exclude_repeating_day($data, $lidnr) {
        $datum = $data['wedstrijd_datum'];
        $weekdag = $this->omzetting_weekdagen(date('D', strtotime($datum)));
        $this->db->where('day', $weekdag);
        $dt = $data['wedstrijd'][0];
        $tijd = $dt->tijd;
        $this->db->where('time_from <', $tijd);
        $this->db->where('time_till >', $tijd);
        $bln_lid = 0;

        if ($lidnr <> "") {
            $this->db->where('lidnummer', $lidnr);
            $bln_lid = 1;
        }

        $query = $this->db->get('referee_exclude');
//echo $this->db->last_query();
        if ($bln_lid === 1) {
//echo $query->num_rows();die;
            if ($query->num_rows() > 0) {
                $key = $data['refs'][0];
                $this->ref_log($key, "altijd op " . $weekdag . " " . $tijd . " afwezig", $data);
                return true;
            }
            return false;
        } else {
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $ref_arr = array();
                    foreach ($data['refs'] as $key) {
                        if ($key['LIDNR'] <> $row->lidnummer) {
                            $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                        } else {
                            $this->ref_log($key, "altijd op " . $weekdag . " " . $tijd . " afwezig", $data);
                        }
                    }
                }
            } else {
                $ref_arr = $data['refs'];
            }
            return $ref_arr;
        }
    }

    function omzetting_weekdagen($weekdag) {
        $weekdag = strtoupper($weekdag);
        switch ($weekdag) {
            case "SUN":
                return "zo";
            case "SAT":
                return "za";
            case "MON":
                return "ma";
            case "TUE":
                return "di";
            case "WED":
                return "wo";
            case "THU":
                return "do";
            case "FRI":
                return "vr";
            default:
                return strtolower($weekdag);
        }
    }

    function ref_nivo($data) {
        $row = $data['team'][0];
        $wedstrijd_nivo = $data['wedstrijd_nivo'];
        $ref_arr = array();
        foreach ($data['refs'] as $key) {
            $arr1 = str_split($key['AWGR']);
            if ($arr1[0] <= $wedstrijd_nivo) {
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
                $this->ref_log($key, "nivo", $data);
            }
        }
        return $ref_arr;
    }

    function ref_nivo_p($data, $lidnr) {
        $row = $data['team'][0];
        $wedstrijd_nivo = $data['wedstrijd_nivo'];
        $ref_arr = array();
        foreach ($data['refs'] as $key) {
            $arr1 = str_split($key['AWGR']);
            if ($arr1[0] <= $wedstrijd_nivo) {
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
                $this->ref_log($key, "nivo", $data);
            }
        }
        return $ref_arr;
    }

    function ref_eigen_wedstrijd($data) {
        $this->load->model('team_model');
        $this->load->model('mod_wedstrijd');
        $ref_arr = array();
//bepaal wedstrijddag
        $data['wedstrijd_datum'];
//als uit dan geen ref indeling
//als thuis dan indelen
//als geen wedstrijd dan indelen
        foreach ($data['refs'] as $key) {
//bepaal team van ref
//var_dump($key);die;
            if ($key['Lidsoort'] === "NS") {
                $key['eigen_wedstrijd'] = $this->get_max_nivo($key['LIDNR']);
                $diff = 0;
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
                $key['ref_poule'] = $this->team_model->get_poulid_from_team_lidnr($key['LIDNR']);
                $hlp_str = "eigen";
//bepaal wedstrijd van ref
                if ($key['ref_poule'] === "no_poule") {
                    $key['eigen_wedstrijd'] = $key['ref_poule'];
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                } else {
                    $hlp_str = "eigen";
                    $data['ref_poule'] = $key['ref_poule'];
                    if ($this->mod_wedstrijd->get_wedstrijd_datum_poule($data, 1) === false) {
//geen wedstrijd op ref dag !
                        $key['eigen_wedstrijd'] = "";
//echo "<pre>1=";
//var_dump($key);
//echo "</pre>";

                        $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                    } else {
//wel wedstrijd op ref dag !
//enkel thuiswedstrijden
                        if ($this->mod_wedstrijd->get_wedstrijd_datum_poule($data, 2) === true) {
                            $key['eigen_wedstrijd'] = $key['ref_poule'];
//echo "<pre>2=";
//var_dump($key);
//echo "</pre>";

                            $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                        } else {
                            $uit_wd = $data['wedstrijd'][0];
                            $wd = array();
                            $wd['wedstrijd_poule'] = $key['ref_poule'];
                            $wd['wedstrijd_datum'] = $data['wedstrijd_datum'];
                            $query = $this->mod_wedstrijd->get_wedstrijd($wd);
                            $eigen_uit_wd = $query[0];
                            $diff = $this->calc_time_diff($uit_wd->tijd, $eigen_uit_wd->tijd);
//echo "<pre>3=";
//var_dump($key);
//echo "</pre>";
//echo "<br>".$diff/60;
                            $diff = $diff / 60;
//echo "<br>dif + role = ".$diff ." ".$data['wedstrijd_role']." " . $key['LIDNR'];
                            if ($diff >= 3.5) {
//echo "hier";
                                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                            } else {
                                $hlp_str .= " " . $diff;
                                $this->ref_log($key, "eigen_uit_wedstrijd, ->" . $hlp_str, $data); // uitwedstrijden moeten ook 3,5 voor en 3.5 uur na
                            }
                        }
                    }
                }
            }
        }
        return $ref_arr;
    }

    function ref_bank_wedstrijd($data) {
        $this->load->model('team_model');
        $this->load->model('mod_wedstrijd');
        $ref_arr = array();
//bepaal wedstrijddag
        $data['wedstrijd_datum'];
//als uit dan geen ref indeling
//als thuis dan indelen
//als geen wedstrijd dan indelen
        foreach ($data['refs'] as $key) {
            if ($key['Lidsoort'] === "NS") {
                
            }
//bepaal team van ref
            $key['ref_poule'] = $this->team_model->get_poulid_from_bank_team_lidnr($key['LIDNR']);
            $hlp_str = "bank";
//bepaal wedstrijd van ref
            if ($key['ref_poule'] === "no_poule") {
                $key['bank_wedstrijd'] = $key['ref_poule'];
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
                $hlp_str = "bank";
                $data['ref_poule'] = $key['ref_poule'];
                if ($this->mod_wedstrijd->get_wedstrijd_datum_poule($data, 1) === false) {
//geen wedstrijd op ref dag !
                    $key['bank_wedstrijd'] = "";
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                } else {
//wel wedstrijd op ref dag !
//enkel thuiswedstrijden
                    if ($this->mod_wedstrijd->get_wedstrijd_datum_poule($data, 2) === true) {
                        $key['bank_wedstrijd'] = $key['ref_poule'];
                        $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                    } else {
                        $uit_wd = $data['wedstrijd'][0];
                        $wd = array();
                        $wd['wedstrijd_poule'] = $key['ref_poule'];
                        $wd['wedstrijd_datum'] = $data['wedstrijd_datum'];
                        $query = $this->mod_wedstrijd->get_wedstrijd($wd);
                        $eigen_uit_wd = $query[0];
                        $diff = $this->calc_time_diff($uit_wd->tijd, $eigen_uit_wd->tijd);
                        $diff = $diff / 60;
//echo "<br>".$diff;
                        if ($diff >= 3.5) {
                            $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                        } else {
                            $hlp_str .= " " . $diff;
                            $this->ref_log($key, "eigen_uit_wedstrijd, ->" . $hlp_str, $data);
                        }
                    }
                }
            }
        }
        return $ref_arr;
    }

    /* check of een potentiele ref ook coach is van een team
     * dat moet spelen
     * zelfde controle als van een spelend lid
     */

    function ref_coach_check($data) {
        $this->load->model('team_model');
        $this->load->model('mod_wedstrijd');
        $ref_arr = array();
//bepaal wedstrijddag
        $data['wedstrijd_datum'];
//als uit dan geen ref indeling
//als thuis dan indelen
//als geen wedstrijd dan indelen
        foreach ($data['refs'] as $key) {
//bepaal coach team van ref
            $data['ref_poule'] = $this->team_model->get_poulid_as_coach($key['LIDNR']);
            //var_dump($data['ref_poule']);
            //echo $key['LIDNR'];
//bepaal coach wedstrijd van ref
            if ($this->mod_wedstrijd->get_wedstrijd_datum_poule($data, 1) === false) {
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
//wel wedstrijd op ref dag !
                if ($this->mod_wedstrijd->get_wedstrijd_datum_poule($data, 2) === true) {
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                } else {
                    $this->ref_log($key, "coach wedstrijd", $data);
                }
            }
        }
        return $ref_arr;
    }

    function ref_experience($data) {
        $this->load->model('team_model');
// heeft de ref voldoende ervaring om deze wedstrijd te fluiten
// mag geen team hoger of gelijk aan eigen team zijn
// default = 0 -> alles
// 1 -> wat is het nivo van de wedstrijd
        $ref_arr = array();
        $wd_team = $data['team'][0];
        $hlp_team_nivo = $this->get_systab("T_SQ", $wd_team->CODE);
//ervaringsnummer wedstrijd/team gevonden, nu vergelijken met de refs
//deze moeten groter of gelijk zijn
        foreach ($data['refs'] as $key) {
            $ref_lidnr = $key['LIDNR'];
            if ($this->team_model->get_poulid_from_team_lidnr($ref_lidnr) === "no_poule") {
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
                $ref_team = $this->team_model->get_team_from_poule_($this->team_model->get_poulid_from_team_lidnr($ref_lidnr));
//                echo "<br>".$ref_lidnr;
//                echo "<br>";var_dump($ref_team);
                $ref_team_nivo = $this->get_systab("T_SQ", $ref_team[0]->CODE);
               // echo "<br>ref";var_dump($ref_team_nivo);
                //echo "<br>hlp";var_dump($hlp_team_nivo);
                if ($ref_team_nivo === '1' || $ref_team_nivo < $hlp_team_nivo ) {
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                } else {
                    $this->ref_log($key, "experience", $data);
                }
            }
        }
        return $ref_arr;
    }

    function ref_already_planned($data) {
// loop door de geplande wedstrijden van vandaag
// als ref already planned dan skip
// select alle geplande refs voor vandaag
// join wedstrijd met ref_gepland
        foreach ($data['wedstrijden_vandaag'] as $key) {
            $hlp_id_arr = $this->get_ref_by_poule_code($key);
            foreach ($hlp_id_arr as $key_h) {
                $hlp_id = $key_h['ref_id'];
                if (is_null($key_h['ref_id'])) {
                    $hlp_id = 0;
                }
                $data['refs'] = $this->ref_already_planned_skip($data, $hlp_id);
            }
        }
        $ref_arr = $data['refs'];
        return $ref_arr;
    }

    private function ref_already_planned_skip($data, $hlp_id) {
        $ref_arr = array();
        foreach ($data['refs'] as $key_r) {
            if ($hlp_id <> $key_r['LIDNR']) {
                $ref_arr = $this->c_array_push($ref_arr, $key_r, __FUNCTION__);
            }//klopt nog niet !
            else {
                if (!isset($data['init']) || $data['init'] === 0) {
                    $this->ref_log($key_r, "al gepland of uitgesloten", $data);
                }
            }
        }
        return $ref_arr;
    }

    function ref_sort_statistic() {
        $query = $this->db->get('referee');
        return $query->result();
    }

    function get_ref_by_poule_code($data) {
        $this->db->select('ref_id');
        $this->db->where('poule', $data->poule);
        $this->db->where('code', $data->code);
        $this->db->where('status <', 8);
        $query = $this->db->get('referee_planned');
        if ($query) {
            return $query->result_array();
        } else {
            return;
        }
    }

    function get_ref_by_lidnr($lidnr) {
        $this->db->select('*');
        $this->db->where('ref_id', $lidnr);
        $this->db->join('wedstrijden as wd', 'wd.poule=rp.poule and wd.code=rp.code', 'left');
        $this->db->where('status <', 6);
        $query = $this->db->get('referee_planned as rp');
        if ($query) {
            //echo $this->db->last_query();
            return $query->result_array();
        } else {
            return;
        }
    }

    function get_systab($syscode, $key, $nivo = 0) {
        $table = "systab";
        $hlp_len = strlen($key);
        if ($nivo === 0) {
            $hlp_len = $this->get_systab($syscode . "_KV", $syscode, 1);
        }
        $this->db->select('value');
        $this->db->where('syscode', $syscode);
        $this->db->where('key_value', substr($key, 0, $hlp_len));
        $query = $this->db->get($table);
        $hlp_value = $query->result();
		//print_r($hlp_value);die;
        if (is_null($query->result())) {
            return;
        } else {
            return $hlp_value[0]->value;
        }
    }

    function read_already_plannend_once($data) {
        $data_w = $data['wedstrijd'];
        $this->load->model('mod_wedstrijd');
        foreach ($data_w as $key) {
            $data['role'] = "R";
            $data['wedstrijd_role'] = $data['role'];
            $data['poule'] = $key->poule;
            $data['wedstrijd_poule'] = $data['poule'];
            $data['code'] = $key->code;
            $data['wedstrijd_code'] = $data['code'];
            $data['ref_id'] = $key->official01;
            $this->check_ref_planned($data, "add");
            $data['ref_id'] = $key->official02;
            $this->check_ref_planned($data, "add");
            $data['role'] = "T";
            $data['wedstrijd_role'] = $data['role'];
            $data['ref_id'] = $key->official03;
            $this->check_ref_planned($data, "add");
            $data['ref_id'] = $key->official04;
            $this->check_ref_planned($data, "add");
            $data['ref_id'] = $key->official05;
            $this->check_ref_planned($data, "add");
        }
    }

    function check_ref_planned($inp, $mode) {
//status:
//0: nieuw
//3: gemaild
//5: toegevoegd via csv file
//8: geupdate via csv file
//var_dump($inp); 
        if (isset($inp['ref_id']) && $inp['ref_id'] <> "") {
            if ($mode === "add") {
                $run_id = $this->get_ref_run_id();
                $table = "referee_planned";
//echo $table;die;
                $this->db->select('*');
                $this->db->where('poule', $inp['poule']);
                $this->db->where('code', $inp['code']);
                $query = $this->db->get($table);
                $hlp_status = 0;
                if (isset($inp['status'])) {
                    $hlp_status = $inp['status'];
                }
                $data_i = array(
                    'role' => $inp['role'],
                    'poule' => $inp['poule'],
                    'code' => $inp['code'],
                    'run_id_sq' => $inp['role_pos'],
                    'match_nivo' => $inp['match_nivo'],
                    'run_id' => $run_id,
                    'status' => $hlp_status,
                    'ref_id' => $inp['ref_id']
                );
//var_dump($data_i);die;
                $this->db->insert('referee_planned', $data_i);
//var_dump($data_i);die;
            }
        }
//echo "??";
//die;
        if ($mode === "upd") {
            $table = "referee_planned";
            $data_u = array(
                'status' => 8
            );
            $this->db->where('poule', $inp['poule']);
            $this->db->where('code', $inp['code']);
            $this->db->where('status <', 8);
            if (isset($inp['ref_id'])) {
                $this->db->where('ref_id', $inp['ref_id']);
            }
            if (isset($inp['status'])) {
                $data_u['status'] = $inp['status'];
            }

            $this->db->update($table, $data_u);
            //echo $this->db->last_query();die;
        }
        $table = "referee_planned";
        $this->db->select('ref_id,poule,code,role,voornaam,naam,awgr,');
        $this->db->where('poule', $inp['poule']);
        $this->db->where('code', $inp['code']);
        $this->db->join('leden as l', 'ref_id = lidnr');
        $this->db->join('referee as r', 'r.lidnr = l.lidnr');
        $query = $this->db->get($table);
    }

    function ref_update_plannedtable($data2) {
        $mode = "upd";
        $inp['poule'] = $data2['poule'];
        $inp['code'] = $data2['code'];
        $this->check_ref_planned($inp, $mode);

        $mode = "add";
        $inp['poule'] = $data2['poule'];
        $inp['code'] = $data2['code'];
        $inp['status'] = 5;

        $this->set_ref_run_id();

        for ($i = 1; $i <= 8; $i++) {
            $inp['role'] = "T";
            if ($i <= 2) {
                $inp['role'] = "R";
            }
            $off_name = "official0" . $i;
            $inp['ref_id'] = $data2[$off_name];
            $inp['role_pos'] = $i;
            $inp['match_nivo'] = "";
//var_dump($inp['ref_id']);die;
            if ($inp['ref_id'] <> "") {
                $this->check_ref_planned($inp, $mode);
            }
        }
    }

    function ref_sort($data, $role) {
        $data['refs'] = $this->get_statistics($data);
        $data['refs'] = $this->get_uren_tot_wedstrijd($data);
		
//$ref_arr = array();
        $ref_arr = $this->ref_ontdubbeling($data);
        $ref_arr1 = array();
//$ref_arr2 = array();
//$ref_arr = $data['refs'];
        if ($role === "R") {
            $ref_arr1 = $this->array_multi_sort($ref_arr, 'hours_till_own_match', 'AWGR', 'ref_telling', $order1 = SORT_ASC, $order2 = SORT_DESC, $order3 = SORT_ASC);

        } else {
            //$ref_arr1 = $this->array_multi_sort($ref_arr, 'AWGR', 'ref_telling', 'hours_till_own_match', $order1 = SORT_DESC, $order2 = SORT_ASC, $order3 = SORT_ASC);
            $ref_arr1 = $this->array_multi_sort($ref_arr, 'hours_till_own_match', 'AWGR', 'ref_telling', $order1 = SORT_ASC, $order2 = SORT_DESC, $order3 = SORT_ASC);
        }

        return $ref_arr1;
    }

    function array_multi_sort($array, $on1, $on2, $on3, $order1 = SORT_DESC, $order2 = SORT_ASC, $order3 = SORT_DESC) {
        $arr_tel = 0;
        foreach ($array as $key => $value) {
            $arr_tel++;
            $one_way_fares[$key] = $value[$on2];
            $return_fares[$key] = $value[$on1];
            $way_fares3[$key] = $value[$on3];
        }
        if ($arr_tel > 0) {
            array_multisort($return_fares, $order1, $one_way_fares, $order2, $way_fares3, $order3, $array);
        } else {
            echo "<br> sort array is leeg !";
//die;
        }
        return $array;
    }

    function get_statistics($data) {
//aantal gefloten wedstrijden voor de selectie aan refs
        $table = "referee_planned";
        $ref_arr = array();
        foreach ($data['refs'] as $key) {
            $ref_lidnr = $key['LIDNR'];
            $this->db->select('count( ref_id) as ref_telling');
            $this->db->where('ref_id', $ref_lidnr);
            $this->db->where('role', $data['wedstrijd_role']);
            $this->db->where('status <', 8);
            $this->db->group_by('ref_id');
            $this->db->order_by('ref_id');
            $query = $this->db->get($table);
            $key['ref_telling'] = 0;
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $key['ref_telling'] = $row->ref_telling * $this->get_factor($ref_lidnr, "STS");
//echo "<br>".$key['ref_telling'];die;
            }
            $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
        }
        return $ref_arr;
    }

    function get_uren_tot_wedstrijd($data) {
//select de wedstrijd van potentiele ref 
//sorteer deze op time-diff
// zit probleem in dat data wordt gedupliceerd....!!!
// ook bankzitters moeten mee doen
        $diffs = 0;
        $ref_arr = array();
        $wedstrijden = $data['wedstrijden_vandaag'];
        foreach ($data['refs'] as $key) {
            if (!isset($key['eigen_wedstrijd'])) {
                $key['eigen_wedstrijd'] = "";
            }
            if ($key['eigen_wedstrijd'] === "") {
                $key['hours_till_own_match'] = 99;
                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
            } else {
                $bln_wd_found = 0;
                $bln_bank_found = 0;
                foreach ($wedstrijden as $key_w) {

                    if (($key_w->poule === $key['eigen_wedstrijd']) and ( $key['Lidsoort'] <> "NS")) {
                        if ($bln_wd_found === 0) {
                            $dt = $data['wedstrijd'][0];
                            $tijd = $dt->tijd;
                            $diffs = $this->calc_time_diff($key_w->tijd, $tijd);
                            $key['hours_till_own_match'] = $diffs / 60;
//echo $key['hours_till_own_match'];
                            $bln_wd_found = 1;
                            if ($diffs <> 0) {
                                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                            } else {
                                $this->ref_log($key, "wedstrijd gelijk moment", $data);
//array_push($ref_arr, $key);
                            }
                        }
                    }
                    if (!isset($key['bank_wedstrijd'])) {
                        $key['bank_wedstrijd'] = "";
                    }
                    if ($key_w->poule === $key['bank_wedstrijd']) {
                        if ($bln_bank_found === 0) {
                            $dt = $data['wedstrijd'][0];
                            $tijd = $dt->tijd;
                            $diffs = $this->calc_time_diff($key_w->tijd, $tijd);
                            $key['hours_till_own_match'] = $diffs / 60;
                            $bln_wd_found = 1;
                            if ($diffs <> 0) {
                                $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                            } else {
                                $this->ref_log($key, "bank wedstrijd gelijk moment", $data);
//array_push($ref_arr, $key);
                            }
                        }
                    }
                }
                if ($bln_wd_found === 0) {
                    $ref_lidnr = $key['LIDNR'];
                    $key['hours_till_own_match'] = 999 * $this->get_factor($ref_lidnr, "HRS");
                    ;
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                }
            }
        }
        return $ref_arr;
    }

    function calc_time_diff($tijd1, $tijd2) {
        $this->load->helper('date');
        $dummy_date = '2000-01-01 ';
        $start_date = new DateTime($dummy_date . $tijd1);
        $since_start = $start_date->diff(new DateTime($dummy_date . $tijd2));

        return $since_start->h * 60 + $since_start->i;
    }

    function remove_own_coach($data) {
//eigen coach kan geen ref zijn.
        $this->load->model('team_model');
        $ref_arr = array();
        $coaches_arr = $this->team_model->get_coaches($data);
        if (count($coaches_arr) > 0) {
            $i = count($coaches_arr);
            foreach ($data['refs'] as $key) {
                $match = false;
                for ($x = 0; $x <= $i - 1; $x++) {
                    $arr_c = $coaches_arr[$x];
                    if ($arr_c['LIDNR'] === $key['LIDNR']) {
                        $match = true;
                    }
                }
                if ($match === true) {
                    $this->ref_log($key, "eigen coach", $data);
                } else {
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                }
            }
        } else {
            $ref_arr = $data['refs'];
        }
        return $ref_arr;
    }

    function sel_ref($data) {
        $ref_arr = array();
        $data['run_id'] = $this->get_ref_run_id();
        $bln_sel = 0;
        foreach ($data['refs'] as $key) {
//var_dump($data);
//die;
            if ($bln_sel === 0) {
                $bln_excl = 0;
                if ($this->ref_exclude_date($data, $key['LIDNR'])) {
//echo "<br>****ref_exclude_date....";
//echo "<pre>";print_r($data);echo "</pre>";
//die;
                    $this->ref_log($key, "ref_exclude_date " . $key['hours_till_own_match'], $data);
                    $bln_excl = 1;
                }
                if ($this->ref_exclude_repeating_day($data, $key['LIDNR'])) {
//echo "<br>****ref_exclude_repeating_day....";
                    $this->ref_log($key, "ref_exclude_repeating_day " . $key['hours_till_own_match'], $data);
                    $bln_excl = 1;
                }
                if ($this->ref_exclude_gemeente($data, $key['LIDNR'])) {
//echo "<br>****ref_exclude gemeente....";
                    $this->ref_log($key, "ref_exclude gemeente " . $key['hours_till_own_match'], $data);
                    $bln_excl = 1;
                }
                if ($data['wedstrijd_role'] === "R") {
                    if ($this->ref_exclude_blessure($data, $key['LIDNR'])) {
//echo "<br>****ja komt toch voor....";
                        $this->ref_log($key, "ref_exclude blessure " . $key['hours_till_own_match'], $data);
                        $bln_excl = 1;
                    }
                }
                /* if ($this->ref_nivo_p($data, $key['LIDNR'])) {
                  //echo "<br>****ja komt toch voor....";
                  $bln_excl = 1;
                  } */
                if ($data['wedstrijd_poule'] <> $key['bank_wedstrijd'] and $data['wedstrijd_poule'] <> $key['eigen_wedstrijd'] and $bln_excl === 0) {
                    $bln_sel = 1;
                    $ref_arr = $this->c_array_push($ref_arr, $key, __FUNCTION__);
                    $this->ref_log($key, "match select " . $key['hours_till_own_match'], $data);
                } else {
                    if ($bln_excl === 0) {
                        $this->ref_log($key, "match non select dup_key " . $key['hours_till_own_match'], $data);
                    }
                }
            } else {
                $this->ref_log($key, "match non select " . $key['hours_till_own_match'], $data);
            }
        }
 //       echo "<br>laatste stap ==============";
  //      var_dump($ref_arr[0]);
        $ref_arr[0]['ref_id'] = $ref_arr[0]['LIDNR'];
        $ref_arr[0]['role'] = $data['wedstrijd_role'];
        $ref_arr[0]['poule'] = $data['wedstrijd_poule'];
        $ref_arr[0]['code'] = $data['wedstrijd_code'];
        $ref_arr[0]['role_pos'] = $data['role_pos'];
        $ref_arr[0]['match_nivo'] = $data['wedstrijd_nivo'];
        $this->check_ref_planned($ref_arr[0], "add");
        return;
    }

    function search_array_dup($lidnr, $data) {
        $i = 0;
        foreach ($data as $key) {
            if ($key['LIDNR'] === $lidnr) {
                $i = $i + 1;
            }
        }
        if ($i >= 2) {
            return true;
        }
        return false;
    }

    function match_allready_planned($data) {
        $this->db->select('ref_id, role');
        $this->db->where('poule', $data['wedstrijd_poule']);
        $this->db->where('code', $data['wedstrijd_code']);
        $this->db->where('status <= 6');
        $query = $this->db->get('referee_planned');
        $count_t = 0;
        $count_r = 0;
        $len_nivo = strlen($data['team'][0]->nivo);
        if ($query->num_rows === $len_nivo) {
            return true;
        } else {
            $nivo_arr = str_split($data['team'][0]->nivo);
            for ($i = 1; $i <= $len_nivo; $i++) {
                if (ctype_alpha($nivo_arr[$i - 1])) {
                    $count_r++; //die;
                } else {
                    $count_t++; //die;
                }
                return false;
            }
        }
    }

    function ref_log($strTekst, $strReden, $data) {
        if ($this->debug and ( isset($strTekst["AWGR"]))) {
            if (!isset($strTekst['ref_telling'])) {
                $strTekst['ref_telling'] = "";
            }
            if (!isset($strTekst['hours_till_own_match'])) {
                $strTekst['hours_till_own_match'] = "";
            }
            if (!isset($strTekst['eigen_wedstrijd'])) {
                $strTekst['eigen_wedstrijd'] = "";
            }
            /*
              echo "<br>****" . $strReden . " => ";
              echo "______(AWGR)=>" . $strTekst["AWGR"];
              echo "_____(RATIO)=>" . $strTekst["RATIO"];
              echo "_____(CAT)  =>" . $strTekst["CATEGORIE"];
              echo "_____(STATS)=>" . $strTekst['ref_telling'];
              echo "_____(UREN ew)=>" . $strTekst['hours_till_own_match'];
              echo "_____(eigen_wedstrijd)=>" . $strTekst['eigen_wedstrijd'];
              echo "___" . $strTekst["LIDNR"];
              echo " " . $strTekst["ZOEKNAAM"];
             */
        }
        if (isset($data['wedstrijd'])) {
            $hlp_wedstrijd = $data['wedstrijd'][0];
            $hlp_team = $data['team'][0];
            //var_dump($data);die;
            $data_db = array(
                'run_id' => $this->get_ref_run_id(),
                'run_id_sq' => $data['role_pos'],
                'poule' => $data['wedstrijd_poule'],
                'code' => $data['wedstrijd_code'],
                'role' => $data['wedstrijd_role'],
                'nivo' => $data['wedstrijd_nivo'],
                'wedstrijd' => $hlp_wedstrijd->datum . ";" . $hlp_wedstrijd->tijd . ";" . $hlp_wedstrijd->thuis,
                'team' => $hlp_team->CODE . ";" . $hlp_team->nivo,
                'reden' => $strReden . ";" . $strTekst["LIDNR"] . ";" . $strTekst["ZOEKNAAM"] . ";" . $strTekst["AWGR"] . ";" . $strTekst['ref_telling'] . ";" . $strTekst['hours_till_own_match'] . ";" . $strTekst['eigen_wedstrijd'],
                'ref_id' => $strTekst["LIDNR"]
            );
            $this->db->insert('referee_audittrail', $data_db);
        }
        return;
    }

    function set_ref_run_id() {
        $table = "systab";
        $syscode = "run_id";
        $this->db->select('value');
        $this->db->where('syscode', $syscode);
        $query = $this->db->get($table);
        $hlp_value = $query->result();
        if (is_null($query->result())) {
            $data_db = array(
                'syscode' => $syscode,
                'key_value' => "",
                'value' => '1'
            );
            $this->db->insert($table, $data_db);
            return(1);
        } else {
            $data_db = array(
                'syscode' => $syscode,
                'key_value' => "",
                'value' => $hlp_value[0]->value + 1
            );
            $this->db->where('syscode', $syscode);
            $this->db->update($table, $data_db);
            return $hlp_value[0]->value + 1;
        }
    }

    function get_ref_run_id() {
        $table = "systab";
        $syscode = "run_id";
        $this->db->select('value');
        $this->db->where('syscode', $syscode);
        $query = $this->db->get($table);
        $hlp_value = $query->result();
        return $hlp_value[0]->value;
    }

    function get_factor($lidnr, $code) {
        $table = "systab";
//code = STS or HRS
        $syscode = $code . "FACTOR";
        $key_value = $lidnr;
        $this->db->select('value');
        $this->db->where('syscode', $syscode);
        $this->db->where('key_value', $key_value);
        $query = $this->db->get($table);
        if ($query->num_rows === 1) {
            $hlp_var = $query->result();
            return $hlp_var[0]->value;
        }
        return 1;
    }

    function get_max_nivo($lidnr) {
        $table = "systab as s";
        $syscode = "MAXNIVO";
        $key_value = $lidnr;
        $this->db->select('poulid');
        $this->db->join('teams as t', 's.value = t.code');
        $this->db->where('syscode', $syscode);
        $this->db->where('key_value', $key_value);
        $query = $this->db->get($table);
        if ($query->num_rows === 1) {
            $hlp_var = $query->result();
            return $hlp_var[0]->poulid;
        }
        return "no_poule";
    }

    function get_gem_u($plaats) {
        $table = "systab";
        $syscode = "GEM";
        $key_value = "U";
        $this->db->select('value');
        $this->db->where('syscode', $syscode);
        $this->db->where('key_value', $key_value);
        $this->db->where('value', $plaats);
        $query = $this->db->get($table);
        if ($query->num_rows() === 1) {
            return false;
        }
//echo "true";
        return true;
    }

    function add_referee($liddata) {
        $table = "referee";
        $this->load->model('leden_model');
        $lid_obj_a = $this->leden_model->get_lid($liddata['lidnr']);
        $lid_obj = $lid_obj_a[0];
        $data_i = array(
            'lidnr' => $lid_obj->Lidnr,
            'zoeknaam' => $lid_obj->Voornaam . " " . $lid_obj->Tussenvoegsel . " " . $lid_obj->Naam,
            'orgid' => "2136",
            'AWGR' => "Z",
            'ratio' => "0",
            'categorie' => 'R'
        );
        $this->db->insert($table, $data_i);
        $data_i = array(
            'lidnr' => $lid_obj->Lidnr,
            'zoeknaam' => $lid_obj->Voornaam . " " . $lid_obj->Tussenvoegsel . " " . $lid_obj->Naam,
            'orgid' => "2136",
            'AWGR' => "9",
            'ratio' => "0",
            'categorie' => 'T'
        );
        $this->db->insert($table, $data_i);
        return true;
    }

    function del_referee($lid_nr) {
        $table = "referee";
        $this->db->delete($table, array('lidnr' => $lid_nr));
        return true;
    }

    function create_rep_refs() {
        $this->load->helper('download');
        $this->db->select('ref_id,w.poule as w_poule,w.code as w_code,role,voornaam,l.naam,awgr');
        $this->db->select('w.datum as w_datum, w.tijd as w_tijd');
        $this->db->select('tw.datum as tw_datum, tw.tijd as tw_tijd, tw.accode as tw_accode, tw.thuis as tw_thuis, tw.plaats as tw_plaats');
        $this->db->select('t.code as t_code, oms, run_id');
        $this->db->where('status <', 5);
        $this->db->from('referee_planned as rp');
        $this->db->join('leden as l', 'ref_id = lidnr', 'left');
        $this->db->join('referee as r', 'r.lidnr = rp.ref_id and rp.role=r.categorie', 'left');
        $this->db->join('wedstrijden as w', 'w.poule=rp.poule and w.code=rp.code', 'left');
        $this->db->join('teamindeling as ti', 'ti.lidnummer = rp.ref_id', 'left');
        $this->db->join('teams as t', 'ti.team = t.code', 'left');
        $this->db->join('wedstrijden as tw', 'tw.poule=t.poulid and tw.datum=w.datum', 'left');
        $this->db->order_by("run_id", "asc");
        $this->db->order_by("role", "asc");
        $this->db->order_by("rp.id", "asc");
        $query = $this->db->get();
//echo $this->db->last_query();die;
        $outfile = 'referee.csv';
        $file = "run_id;ref_id;poule;code;role;stats;voornaam;naam;awgr;datum;tijd;code;oms;wd_datum;wd_tijd;wd_accode;wd_thuis;wd_plaats\n";
        foreach ($query->result() as $row) {
            $file .= $row->run_id . ";";
            $file .= $row->ref_id . ";";
            $file .= $row->w_poule . ";";
            $file .= $row->w_code . ";";
            $file .= $row->role . ";";
            $stat = $this->get_statistics_per_ref($row->ref_id, $row->role);
            $file .= $stat . ";";
            $file .= $row->voornaam . ";";
            $file .= $row->naam . ";";
            $file .= $row->awgr . ";";
            $file .= $row->w_datum . ";";
            $file .= $row->w_tijd . ";";
            $file .= $row->t_code . ";";
            $file .= $row->oms . ";";
            $file .= $row->tw_datum . ";";
            $file .= $row->tw_tijd . ";";
            $file .= $row->tw_accode . ";";
            $file .= $row->tw_thuis . ";";
            $file .= $row->tw_plaats . "\n";
        }
        force_download($outfile, $file);
        return;
    }

    function get_statistics_per_ref($lidnr, $role) {
        $table = "referee_planned";
        $this->db->select('count( ref_id) as ref_telling');
        $this->db->where('ref_id', $lidnr);
        $this->db->where('role', $role);
        $this->db->where('status <', 8);
        $this->db->group_by('ref_id');
        $this->db->order_by('ref_id');
        $query = $this->db->get($table);
        $key['ref_telling'] = 0;
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $key['ref_telling'] = $row->ref_telling;
        }
        return $key['ref_telling'];
    }

    function del_ref_run($run_id) {
        if ($this->check_ref_run($run_id)) {
            $table = "referee_planned";
            $this->db->delete($table, array('run_id =' => $run_id));
            $table = "referee_audittrail";
            $this->db->delete($table, array('run_id =' => $run_id));
            echo "<br>RUN id verwijderd";
            return true;
        } else {
            echo "<br>RUN id bestaat niet";
        }
    }

    function del_ref_run_sq($run_id, $run_id_sq) {
        if ($this->check_ref_run($run_id)) {
            $table = "referee_planned";
            $this->db->delete($table, array('run_id =' => $run_id, 'run_id_sq =' => $run_id_sq));
            $table = "referee_audittrail";
            $this->db->delete($table, array('run_id =' => $run_id, 'run_id_sq =' => $run_id_sq));
            echo "<br>RUN_sq id verwijderd";
            return true;
        } else {
            echo "<br>RUN id bestaat niet";
        }
    }

    function del_trail($run_id, $run_id_sq) {
        if ($this->check_ref_run($run_id)) {
            $table = "referee_audittrail";
            $this->db->delete($table, array('run_id =' => $run_id, 'run_id_sq =' => $run_id_sq));

            return true;
        } else {
            
        }
    }

    function check_ref_run($run_id) {
        $table = "referee_planned";
        $this->db->where('run_id', $run_id);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    function create_rep_stats($data) {
        $this->load->helper('download');
        $this->load->model('team_model');
        $outfile = 'ref_stats.csv';
        $file = "ref_id;role;zoeknaam;awgr;stats;poule\n";
        foreach ($data['refs'] as $row) {
            $poule = $this->team_model->get_poulid_from_team_lidnr($row['LIDNR']);
            $file .= $row['LIDNR'] . ";" . $row['CATEGORIE'] . ";" . $row['ZOEKNAAM'] . ";" . $row['AWGR'] . ";" . $row['ref_telling'] . ";" . $poule . "\n";
        }
        force_download($outfile, $file);
        return;
    }

    function ref_ontdubbeling($data) {
//$ref_arr = array();
//$ref_arr1 = array();
        $ref_arr2 = array();
        $ref_arr = $data['refs'];

        $ref_arr1 = $this->array_multi_sort($ref_arr, 'LIDNR', 'hours_till_own_match', 'ref_telling', $order1 = SORT_DESC, $order2 = SORT_DESC, $order3 = SORT_ASC);

        $lidnr = "";
        foreach ($ref_arr1 as $key) {
            if ($key['LIDNR'] <> $lidnr) {
                $ref_arr2 = $this->c_array_push($ref_arr2, $key, __FUNCTION__);
                $lidnr = $key['LIDNR'];
            }
        }
		//echo "<pre>";
		//print_r($ref_arr2);
		//echo "</pre>";

        return $ref_arr2;
    }

    function c_array_push($ref_arr, $key, $bron) {
//		if($key['LIDNR']==="213600420"){
//			echo "<br>sophie ".$bron."<br>";
//			}
        array_push($ref_arr, $key);
        return $ref_arr;
    }

// het invullen van de refs in de wedstrijden  
    function load_ref($run_id) {
        $this->load->model('mod_wedstrijd');
        $this->load->model('leden_model');
        $this->load->model('team_model');
        $table = "wedstrijden";
        if ($this->check_ref_run($run_id)) {
            $data['run'] = $this->get_refs_from_run($run_id);
//$run = $data['run'];
//echo $run[0]->poule;
            $data['team'] = $this->team_model->get_team_from_poule_($data['run'][0]->poule);
            //var_dump($data['run']);
            $team = $data['team'][0];
//var_dump($team);
            $len = strlen($team->nivo);
            echo $len;
//echo $run_id;
//var_dump($data['run']);die;
            if (strlen($team->nivo) > 3) {
                $i = 1;
            } else {
                $i = 3;
            }
            foreach ($data['run'] as $run) {
                $data['lid'] = $this->leden_model->get_lid($run->ref_id);
                $data['poule'] = $run->poule;
//echo $data['wedstrijd_poule'];
                $data['code'] = $run->code;
                $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
                $wd = $data['wedstrijd'];
                $this->db->where('poule', $data['poule']);
                $this->db->where('code', $data['code']);

                $lid = $data['lid'];
//print_r( $this->leden_model->get_lid($run->ref_id));
                //var_dump($lid[0]);
                echo $run->role;
                echo $i;
                echo "<pre>";
                var_dump($data['run']);
                echo "</pre>";
                echo "<br>";
                //$i = $run->run_id_sq;

                if ((($run->role === "R")and ( $i <= 2)) or ( ($run->role === "T")and ( $i > 2))) {
                    $data2 = array(
                        'official0' . $i => $run->ref_id,
                        'naam0' . $i => $lid[0]->Voornaam . " " . $lid[0]->Tussenvoegsel . " " . $lid[0]->Naam
                    );

                    $this->db->update($table, $data2);
                } else {
                    echo "fout in toewijzing GESTOPT";
                    echo "<br>poule: " . $run->poule . " " . $run->code;
                    ;
                    echo "<br>lid: " . $run->ref_id . " " . $lid[0]->Voornaam . " " . $lid[0]->Tussenvoegsel . " " . $lid[0]->Naam;
                    exit(1);
                }
                debug_last_query(); //die;
//$this->ref_update_plannedtable($data2);
                $i = $i + 1;
            }
            return true;
        } else {
            echo "<br>RUN id bestaat niet";
        }
    }

// het invullen van de refs in de wedstrijden  
    function load_ref_poule($poule_id, $code) {
        $this->load->model('mod_wedstrijd');
        $this->load->model('leden_model');
        $this->load->model('team_model');
        $table = "wedstrijden";
        //if ($this->check_ref_run($run_id)) {
        $data['run'] = $this->get_refs_from_poule($poule_id, $code);
//$run = $data['run'];
//echo $run[0]->poule;
        $data['team'] = $this->team_model->get_team_from_poule_($poule_id);
        //var_dump($data['run']);
        $team = $data['team'][0];
        //var_dump($team);
        $len = strlen($team->nivo);
        //echo $len;
        //echo $run_id;
        //var_dump($data['run']);
        //die;
        if (strlen($team->nivo) > 3) {
            $i = 1;
        } else {
            $i = 3;
        }


        //var_dump($data['run']);
        //die;
        foreach ($data['run'] as $run) {
            $data['lid'] = $this->leden_model->get_lid($run->ref_id);
            $data['poule'] = $run->poule;
//echo $data['wedstrijd_poule'];
            $data['code'] = $run->code;
            $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
            $wd = $data['wedstrijd'];
            $this->db->where('poule', $data['poule']);
            $this->db->where('code', $data['code']);

            $lid = $data['lid'];
//print_r( $this->leden_model->get_lid($run->ref_id));
            //  var_dump($lid);
            //  echo $run->role;
            //  echo $i;
            //  echo "<pre>";
            //  var_dump($data['run']);
            // echo "</pre>";
            // echo "<br>";
            //$i = $run->run_id_sq;
            // echo "hier";
            if ((($run->role === "R")and ( $i <= 2)) or ( ($run->role === "T")and ( $i > 2))) {
                $data2 = array(
                    'official0' . $i => $run->ref_id,
                    'naam0' . $i => $lid[0]->Voornaam . " " . $lid[0]->Tussenvoegsel . " " . $lid[0]->Naam
                );

                $this->db->update($table, $data2);
            } else {
                echo "fout in toewijzing GESTOPT";
                echo "<br>poule: " . $run->poule . " " . $run->code;
                ;
                echo "<br>lid: " . $run->ref_id . " " . $lid[0]->Voornaam . " " . $lid[0]->Tussenvoegsel . " " . $lid[0]->Naam;
                exit(1);
            }
            //debug_last_query(); //die;
//$this->ref_update_plannedtable($data2);
            $i = $i + 1;
        }
        return true;
        //} else {
        //  echo "<br>RUN id bestaat niet";
        //}
    }

    function get_refs_from_run($run_id) {
        $table = "referee_planned";
        $this->db->where('run_id', $run_id);
//$this->db->where('role', $role);
        $this->db->order_by('role', 'asc');
        $query = $this->db->get('referee_planned');
//var_dump($query->result());
        return $query->result();
    }

    function get_refs_from_poule($poule_id, $code) {
        $table = "referee_planned";
        $this->db->where('poule', $poule_id);
        $this->db->where('code', $code);
        $this->db->where('status <', 6);
//$this->db->where('role', $role);
        $this->db->order_by('role', 'asc');
        $this->db->order_by('run_id_sq', 'asc');
        $query = $this->db->get('referee_planned');
		//echo $this->db->last_query();
        //var_dump($query->result());
        return $query->result();
    }

    function get_ref_with_poule_from_poule($poule_id, $code) {
        $table = "referee_planned";
        $this->db->where('rp.poule', $poule_id);
        $this->db->where('rp.code', $code);

        $this->db->where('status <', 6);
//$this->db->where('role', $role);
        $this->db->order_by('role', 'asc');
        $this->db->order_by('run_id_sq', 'asc');
        $this->db->join('teamindeling as ti', 'ti.lidnummer=rp.ref_id');
        $this->db->join('teams as t', 'ti.team=t.CODE');
        $query = $this->db->get('referee_planned as rp');
        //var_dump($query->result());
        return $query->result();
    }

    function get_audit_trail($run_id, $run_id_sq, $ref_id = 0) {
        $table = "referee_audittrail";
        $this->db->select('reden');
        $this->db->where('run_id', $run_id);
        $this->db->where('run_id_sq', $run_id_sq);
        if ($ref_id <> 0) {
            $this->db->where('ref_id', $ref_id);
        }
//$this->db->where('role', $role);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($table);
        //debug_last_query();
        //var_dump($query->result());
        return $query->result();
    }

    function get_audit_trail_all($run_id, $run_id_sq, $ref_id = 0) {
        $table = "referee_audittrail";
        $this->db->select('*');
        $this->db->where('run_id', $run_id);
        $this->db->where('run_id_sq', $run_id_sq);
        if ($ref_id <> 0) {
            $this->db->where('ref_id', $ref_id);
        }
//$this->db->where('role', $role);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($table);
        //debug_last_query();
        //var_dump($query->result());
        return $query->result();
    }

    function update_ref_table($run_id, $ref_id_old, $ref_id_new) {
        $this->load->model('leden_model');
		//echo $run_id."run-";
		//echo $ref_id_old."old-";
		//echo $ref_id_new."new-";
        if ($ref_id_new <> "") {
            if ($this->leden_model->bestaat_lid($ref_id_new)) {
                $this->db->where('run_id', $run_id);
                $this->db->where('ref_id', $ref_id_old);
                $this->db->set('ref_id', $ref_id_new);
                $this->db->set('status', '4');
                $this->db->update('referee_planned');
            } else {
                echo "lidnummer bestaat niet";
            }
        }
		return;
    }

}
