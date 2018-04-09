<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of referee
 *
 * @author gerard
 */
class referee extends CI_Controller {

    function init_ref_planned() {
        $this->load->model('mod_wedstrijd');
        $this->load->model('ref_model');
        $data = array();
        $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
        $data['init'] = 1;
        $this->ref_model->read_already_plannend_once($data);
        return;
    }

    function select_match() {
        $data = array();
        $data['init'] = 0;
        $ref_arr = array();
        $this->load->model('mod_wedstrijd');
        $this->load->model('ref_model');
        $this->load->model('team_model');
        $data['wed_ref_sel'] = $this->mod_wedstrijd->get_wedstrijd_range($data, 40);
        //var_dump($data['wed_ref_sel']);die;
        //$ref_arr=$this->ref_model->get_ref_by_poule_code($data);
        foreach ($data['wed_ref_sel'] as $row) {
            if ($row->accode == "VHTSC") {
                if ($row->tijd <> "00:00") {
                    $data['wedstrijd_poule'] = $row->poule;
                    $data['wedstrijd_code'] = $row->code;
                    $data['team'] = $this->team_model->get_team_from_poule($data);
                    if ($this->ref_model->match_allready_planned($data)) {
                        echo "<br> al gepland!";
                        //break;
                    } else {
                        $this->select_refs($row->poule, $row->code);
                        //die;
                    }
                }
            }
        }
        return;
    }

    /*    function select_refs($poule, $code) {
      //echo "poule = ".$poule;
      //echo "code = ".$code;die;
      $this->load->model('ref_model');
      $data['run_id'] = $this->ref_model->set_ref_run_id();

      $this->load->model('mod_wedstrijd');
      $this->load->model('team_model');
      //$data['wedstrijd_poule'] = 'WM183B';
      $data['wedstrijd_poule'] = $poule;
      //$data['wedstrijd_code'] = 'BA';
      $data['wedstrijd_code'] = $code;
      $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
      $data['team'] = $this->team_model->get_team_from_poule($data);
      //$data['wedstrijd_datum'] = $this->mod_wedstrijd->get_wedstrijd_datum($data);
      //$data['wedstrijden_vandaag'] = $this->mod_wedstrijd->get_wedstrijden_vandaag($data);
      //var_dump($data);
      //die;

      $nivo = $data['team'][0]->nivo;
      $nivo_arr = str_split($nivo);
      //var_dump($nivo_arr);
      //die;
      //lijstj    e met refs samenstellen
      for ($i = 1; $i <= strlen($nivo); $i++) {
      $data['role'] = "T";
      if (ctype_alpha($nivo_arr[$i - 1])) {
      $data['role'] = "R";
      }
      $data['nivo'] = $nivo_arr[$i - 1];
      $data['role_pos'] = $i;
      $data['refs'] = $this->select_ref($poule, $code, $data['role'], $data['nivo'], $data['role_pos']);
      //$data['refs'] = $this->ref_model->ref_getall($data);
      // hier moet het nivo ook ergens bij en dan in de mod het nivo meegeven en pos daaruit verwijderen!!
      //$data['refs'] = $this->ref_model->sel_ref($data, "spelers");
      }
      //die;
      //var_dump($data['refs']);
      return;
      }
     */

    function select_refs($poule, $code, $role = 0) {
		//error_reporting(-1);
        //echo "poule = ".$poule;
        //echo "code = ".$code;//die;
        $this->load->model('ref_model');
        $data['run_id'] = $this->ref_model->set_ref_run_id();
		//print_r ($data['run_id']);

        $this->load->model('mod_wedstrijd');
        $this->load->model('team_model');
        //$data['wedstrijd_poule'] = 'WM183B';
        $data['wedstrijd_poule'] = $poule;
        //$data['wedstrijd_code'] = 'BA';
        $data['wedstrijd_code'] = $code;
        $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
        $data['team'] = $this->team_model->get_team_from_poule($data);
        //$data['wedstrijd_datum'] = $this->mod_wedstrijd->get_wedstrijd_datum($data);
        //$data['wedstrijden_vandaag'] = $this->mod_wedstrijd->get_wedstrijden_vandaag($data);
        //var_dump($data['team']);
        //die;
        if (!isset($data['team'][0])) {
            echo "<br><hr><br>team met poule niet gevonden => " . $poule;
            die;
        }

        $nivo = $data['team'][0]->nivo;
        $nivo_arr = str_split($nivo);
        //var_dump($nivo_arr);
        //die;
        //lijstje met refs samenstellen
        for ($i = 1; $i <= strlen($nivo); $i++) {
            $data['role'] = "T";
            if (ctype_alpha($nivo_arr[$i - 1])) {
                $data['role'] = "R";
            }
            $data['nivo'] = $nivo_arr[$i - 1];
            $data['role_pos'] = $i;
            if ($role === $data['role'] or $role === 0) {
                //echo "<br>".$poule." - ". $code." - ". $data['role']." - ". $data['nivo']." - ". $data['role_pos']." - ".$role;
                $data['refs'] = $this->select_ref($poule, $code, $data['role'], $data['nivo'], $data['role_pos']);
            }
            //$data['refs'] = $this->ref_model->ref_getall($data);
            // hier moet het nivo ook ergens bij en dan in de mod het nivo meegeven en pos daaruit verwijderen!!
            //$data['refs'] = $this->ref_model->sel_ref($data, "spelers");
        }
        //die;
		//echo "<br>";
        //var_dump($data['refs']);
		//die;
        return;
    }

    //put your code here
    function select_ref($poule, $code, $role, $nivo, $role_pos) {
        $this->load->model('ref_model');
        $this->load->model('mod_wedstrijd');
        $this->load->model('team_model');
        //$data['wedstrijd_poule'] = 'WM183B';
        $data['wedstrijd_poule'] = $poule;
        //$data['wedstrijd_code'] = 'BA';
        $data['wedstrijd_code'] = $code;
        $data['wedstrijd_role'] = $role;
        $data['wedstrijd_nivo'] = $nivo;
        $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
        $data['team'] = $this->team_model->get_team_from_poule($data);
        $data['wedstrijd_datum'] = $this->mod_wedstrijd->get_wedstrijd_datum($data);
        $data['wedstrijden_vandaag'] = $this->mod_wedstrijd->get_wedstrijden_vandaag($data);
        $data['role_pos'] = $role_pos;

        $nivo = $data['team'][0]->nivo;
        $nivo_arr = str_split($nivo);
        $data['refs'] = $this->ref_model->ref_getall_potential($data);
        $data['refs'] = $this->ref_model->ref_eigen_wedstrijd($data);
        //echo "<br>cnt =". count($data['refs']) ;

        $data['refs'] = $this->ref_model->ref_sort($data, $data['wedstrijd_role']);
        //echo "<br>cnt =". count($data['refs']) ;
		//print_r($data['refs']);
        $data['refs'] = $this->ref_model->remove_own_coach($data);
        //echo "<br>cnt =". count($data['refs']) ;
        $data['refs'] = $this->ref_model->ref_nivo($data);
        //echo "<br>=============== nivo filter";
		      //  echo "<br>cnt =". count($data['refs']) ;
        $data['refs'] = $this->ref_model->ref_bank_wedstrijd($data);
        //echo "<br>=============== eigen wedstrijd filter";
		//        echo "<br>cnt =". count($data['refs']) ;
        $data['refs'] = $this->ref_model->ref_coach_check($data);
        //echo "<br>=============== coach_check";
		//        echo "<br>cnt =". count($data['refs']) ;
        $data['refs'] = $this->ref_model->ref_already_planned($data);
        //echo "<br>=============== ref_already_planned";
		        //echo "<br>cnt =". count($data['refs']) ;
				//echo "<pre>";print_r($data['refs']);echo "</pre>";
        if ($data['wedstrijd_role'] == "R") {
            $data['refs'] = $this->ref_model->ref_experience($data);
          //    echo "<br>=============== ref_exp";
			//          echo "<br>cnt =". count($data['refs']) ;
        }
        $data['refs'] = $this->ref_model->sel_ref($data, "spelers");
        //echo "<br>=============== sel_ref";
		  //      echo "<br>cnt =". count($data['refs']) ;
        return ($data['refs']);
    }

    function create_ref_ovz() {
        $this->load->model('ref_model');
        $this->ref_model->create_rep_refs();
    }

    function delete_run($run_id = 0) {
        $this->load->model('ref_model');
        if (isset($run_id)) {
            $this->ref_model->del_ref_run($run_id);
        } else {
            echo "run id is niet ingevuld";
        }
    }

    function delete_run_sq($run_id = 0, $run_id_sq = 0) {
        $this->load->model('ref_model');
        if (isset($run_id)) {
            if (isset($run_id_sq)) {
                $this->ref_model->del_ref_run_sq($run_id, $run_id_sq);
            } else {
                echo "run id sq is niet ingevuld";
            }
        } else {
            echo "run id is niet ingevuld";
        }
    }

    function get_statistics($role) {
        $this->load->model('ref_model');
        $data['wedstrijd_role'] = $role;
        $data['refs'] = $this->ref_model->ref_getall($data);
        $data['refs'] = $this->ref_model->get_statistics($data);
        $this->ref_model->create_rep_stats($data);
    }

    function load_refs($run_id = 0) {
        $this->load->model('ref_model');
        if (isset($run_id)) {
            $this->ref_model->load_ref($run_id);
        } else {
            echo "run id is niet ingevuld";
        }
    }

    function load_refs_poule($poule_id = 0, $code = 0) {
        $this->load->model('ref_model');
        if (isset($poule_id)) {
            $this->ref_model->load_ref_poule($poule_id, $code);
        } else {
            echo "poule id is niet ingevuld";
        }
    }

    function vw_wedstrijden($datum = null) {
        $this->load->model('mod_wedstrijd');

        //$data = array();
        //$data['css'] = $this->config->item('css');
        //$data['base'] = $this->config->item('base_url');
        //while (1) {
        //$data = array(
        //    'user_name' => $this->input->post('u_name'),
        //    'user_email_id' => $this->input->post('u_email')
        //);
        if (isset($datum)) {
            $data['wedstrijd_datum'] = $datum;
        }
        $data['wedstrijden'] = $this->mod_wedstrijd->get_wedstrijd_range_with_ref_asc($data, 100);

        $data['run_id'] = 0;
        $data['run_id_sq'] = 0;
        $data['ref_in_wd'] = 0;
        //var_dump($wd);
        //die;
        //$data['trail_item'] = $this->ref_model->get_audit_trail($arr_run_id[0], $arr_run_id[1], $arr_run_id[2]);
        $this->load->view('wedstrijden_view', $data);
        //var_dump($data['xyz']);
        //}
    }

    function verwerk_vw_wedstrijden($datum = null) {
        $this->load->model('mod_wedstrijd');
        $this->load->model('ref_model');
        $data = array();
//while (1) {
        //print_r($_POST);
        //$data['css'] = $this->config->item('css');
        //$data['base'] = $this->config->item('base_url');

        foreach ($_POST as $key => $post) {
            // echo $post;
            //$data['css'] = $this->config->item('css');
            //$data['base'] = $this->config->item('base_url');
            if ($post === "plan_wedstrijd") {
                $len = strlen($key);
                $poule = substr($key, 0, ($len - 2));
                $code = substr($key, ($len - 2), 2);
                $data['run_id'] = 0;
                $data['run_id_sq'] = 0;
                //echo $code;
                $this->select_refs($poule, $code);
            } if ($post === "plan_scheidsrechter") {
                $len = strlen($key);
                $poule = substr($key, 0, ($len - 2));
                $code = substr($key, ($len - 2), 2);
                $data ['run_id'] = 0;
                $data['run_id_sq'] = 0;
                echo "<br>" . $key;
                $this->select_refs($poule, $code, "R");
            } if ($post === "plan_tafelaar") {
                $len = strlen($key);
                $poule = substr($key, 0, ($len - 2));
                $code = substr($key, ($len - 2), 2);
                $data ['run_id'] = 0;
                $data['run_id_sq'] = 0;
                //echo $code;
                $this->select_refs($poule, $code, "T");
            } if ($post === "del_planning") {
                if ($key <> "") {
                    $run_id = $key;
                    $data['run_id'] = $key;
                    //echo $code;
                    $this->delete_run($run_id);
                }
                //$data['run_id'] = 0;
                //$data['run_id_sq'] = 0;
            }

            if ($post === "bevestig_planning") {
                if ($key <> "") {
                    $run_id = $key;
                    $data['run_id'] = $key;
                    $data['run_id_sq'] = 0;
                    //echo $code;
                    $len = strlen($key);
                    $poule = substr($key, 0, ($len - 3));
                    $code = substr($key, ($len - 2), 2);

                    $this->load_refs_poule($poule, $code);
                }
                //$data['run_id'] = 0;
                //$data['run_id_sq'] = 0;
            }

            if ($post === "audit_trail") {
                $arr_run_id = explode("-", $key);
//var_dump( $arr_run_id);die;
                $data['trail'] = $this->ref_model->get_audit_trail($arr_run_id[0], $arr_run_id[1]);
                $data['trail_item'] = $this->ref_model->get_audit_trail($arr_run_id[0], $arr_run_id[1], $arr_run_id[2]);
                $data['run_id'] = $arr_run_id[0];
                $data['run_id_sq'] = $arr_run_id[1];
//$data['css'] = $this->config->item('css');
//$data['base'] = $this->config->item('base_url');
//var_dump($data['trail']);
                $data['trailhtml'] = $this->load->view('planning_trail_view', $data, true);
            } if ($post === "vervang_pers") {
                $arr_data = explode("-", $key);
                $run_id = $arr_data[0];
                $run_id_sq = $arr_data[1];
                $poule = $arr_data[2];
                $code = $arr_data[3];
                $role = $arr_data[4];
                $role_pos = $arr_data[6];
                $nivo = $arr_data[5][$role_pos - 1];

                $ref_id = $arr_data[7];
//var_dump($arr_data);//die;
                $this->ref_model->del_trail($run_id, $run_id_sq);
                $inp['poule'] = $poule;
                $inp['code'] = $code;
                $inp['ref_id'] = $ref_id;
                $inp['status'] = 6;
                $this->ref_model->check_ref_planned($inp, "upd");
                $data['run_id_sq'] = 0;
//echo "<br>".$poule."-".$code."-". $role."-". $nivo."-". $role_pos;die;
                $data['run_id'] = $this->ref_model->set_ref_run_id();
                $this->select_ref($poule, $code, $role, $nivo, $role_pos);
//$data['trailhtml'] = $this->load->view('planning_trail_view', $data, true);                
            }
        }
        if ($data) {
//$run_id = $data['run_id'];
//$run_id_sq = $data['run_id_sq'];
        }

//$data = array(
//    'user_name' => $this->input->post('u_name'),
//    'user_email_id' => $this->input->post('u_email')
//);
        if (isset($datum)) {
            $data['wedstrijd_datum'] = $datum;
        }
        $data['wedstrijden'] = $this->mod_wedstrijd->get_wedstrijd_range_with_ref_asc($data, 100);
//$data['trail_item'] = $this->ref_model->get_audit_trail($arr_run_id[0], $arr_run_id[1], $arr_run_id[2]);
        $this->load->view('wedstrijden_view', $data);
//var_dump($data['xyz']);
//}
    }

    function test_view() {
        //$data['css'] = $this->config->item('css');
        //$data['base'] = $this->config->item('base_url');
	
        $this->load->model('mod_wedstrijd');
        $this->load->model('ref_model');
        $this->load->model('team_model');
        $data = array();
        $hlp_run_id_sq = 0;
        $hlp_run_id = 0;
        $data['wedstrijden'] = $this->mod_wedstrijd->get_wedstrijd_range_with_team($data, 1000);
        $content = array();
        $row = array("title" => "35", "author" => "37", "date" => "43", "body_text" => "tekst");
        $template['title'] = $row['title'];
        $template['wedstrijden'] = $this->load->view('wedstrijden_vw', $data, true);
        $template['wedstrijd_datum'] = $this->load->view('wedstrijden_datum_vw', $data, true);
        foreach ($_POST as $key => $post) {

            if ($post === "plan_wedstrijd") {
                $len = strlen($key);
//                $poule = substr($key, 0, ($len - 2));
//                $code = substr($key, ($len - 2), 2);
                $arr = explode("-", $key);
                $poule = $arr[0];
                $code = $arr[1];

                $data['run_id'] = 0;
                $data['run_id_sq'] = 0;
                //echo "<br>" . $code;
                //echo "<br>" . $poule;
                $this->select_refs($poule, $code);
            }
            if ($post === "plan_scheidsrechter") {
                $len = strlen($key);
                //$poule = substr($key, 0, ($len - 2));
                //$code = substr($key, ($len - 2), 2);
                $arr = explode("-", $key);
                $poule = $arr[0];
                $code = $arr[1];
				
				//echo "key = " . $key;
				//echo "code = " . $code;
                $data['run_id'] = 0;
                $data['run_id_sq'] = 0;
                $this->select_refs($poule, $code, "R");
                $data['refs'] = $this->ref_model->get_refs_from_poule($poule, $code);
				//echo "<pre>";print_r($data['refs']);echo "</pre>";die; 
                $template['wd_official'] = $this->get_official_data($data['refs']);
            }
            if ($post === "plan_tafelaar") {
                $len = strlen($key);
                //$poule = substr($key, 0, ($len - 2));
                //$code = substr($key, ($len - 2), 2);
                $arr = explode("-", $key);
                $poule = $arr[0];
                $code = $arr[1];
				
                $data ['run_id'] = 0;
                $data['run_id_sq'] = 0;
                $this->select_refs($poule, $code, "T");
                $data['refs'] = $this->ref_model->get_refs_from_poule($poule, $code);
                $template['wd_official'] = $this->get_official_data($data['refs']);
            }
            if ($post === "vervang") {
//                print_r($_POST);
                $arr_data = explode("-", $key);
                $run_id = $arr_data[0];
                $run_id_sq = $arr_data[1];
                $poule = $arr_data[2];
                $code = $arr_data[3];
                $role = $arr_data[4];
                $role_pos = $arr_data[6];
                //$nivo = $arr_data[5][$role_pos - 1];
                $nivo = $arr_data[5];
                var_dump($nivo);
                $ref_id = $arr_data[7];
                $this->ref_model->del_trail($run_id, $run_id_sq);
                $inp['poule'] = $poule;
                $inp['code'] = $code;
                $inp['ref_id'] = $ref_id;
                $inp['status'] = 6;
                $this->ref_model->check_ref_planned($inp, "upd");
                $data['run_id_sq'] = 0;
                $data['run_id'] = $this->ref_model->set_ref_run_id();
                $this->select_ref($poule, $code, $role, $nivo, $role_pos);
                $key = $poule . $code;
            }

            if ($post === "update") {
                $arr = explode("-", $key);
				//var_dump($arr);
                $new_ref = $_POST['new_lidnr'];
                $old_ref = $arr[0];
				echo "<br>arr=".$arr[0]." ".$old_ref;
                $run_id = $arr[1];
                $poule = $arr[2];
                $code = $arr[3];
                $this->ref_model->update_ref_table($run_id, $old_ref, $new_ref);
                $key = $poule . $code;
            }

            if ($post === "officials" or $post === "plan_scheidsrechter" or $post === "plan_wedstrijd" or $post === "plan_tafelaar" or $post === "vervang" or $post === "update") {
                $len = strlen($key);
                //$poule = substr($key, 0, ($len - 2));
                //$code = substr($key, ($len - 2), 2);

				if($post === "officials") {
					$hlp_key = explode("-",$key);
					$poule = $hlp_key[0];
					$code = $hlp_key[1];
				}
                $data ['run_id'] = 0;
                $data['run_id_sq'] = 0;
                $data['refs'] = $this->ref_model->get_ref_with_poule_from_poule($poule, $code);
                $template['wd_official'] = $this->get_official_data($data['refs']);
            }

            if ($post === "wd_run_id") {
                $hlp_run_id = $key;
            }

            if ($post === "wd_run_id_sq") {
                $hlp_run_id_sq = $key;
            }

            if ($post === "plan") {
                if ($key <> "") {
                    $run_id = $key;
                    $len = strlen($key);
                    //$poule = substr($key, 0, ($len - 3));
                    //$code = substr($key, ($len - 2), 2);
                    $arr = explode("-", $key);
                    $poule = $arr[0];
                    $code = $arr[1];
                    $wd_datum = $arr[2];
                    $wd_tijd = $arr[3];
                    $this->load_refs_poule($poule, $code);
                    $key = $wd_datum;
                }
            }

            if ($post === "wd_ref") {
                $data['ref_lid'] = $this->ref_model->get_ref_by_lidnr($key);
                $template['ref_lid'] = $this->load->view('wedstrijd_official_historie', $data, true);
                $hlp_ref = $key;
            }
            if ($hlp_run_id <> "" and $hlp_run_id_sq <> "" and $hlp_ref <> "") {
                $data['audit_trail'] = $this->ref_model->get_audit_trail($hlp_run_id, $hlp_run_id_sq);
                $template['audit_trail'] = $this->load->view('wedstrijd_official_audit_trail', $data, true);
                $data['ref'][0] = $hlp_ref;
            }
            if ($post === "wd_datum") {
                $data['wedstrijd_datum_selected'] = $key;
                $template['wedstrijd_datum_tijd'] = $this->load->view('wedstrijden_datum_tijd_vw', $data, true);
                $template['wedstrijden'] = $this->load->view('wedstrijden_vw', $data, true);
            }
            if ($post === "wd_datum_tijd") {
                $data['wedstrijd_datum_tijd_selected'] = $key;
                $template['wedstrijden'] = $this->load->view('wedstrijden_vw', $data, true);
            }
            if ($post === "verwijder") {
                if ($key <> "") {
                    $run_id = $key;
                    $data['run_id'] = $key;
                    $this->delete_run($run_id);
                }
            }
        }

        $article['title'] = $row['title'];
        $article['author'] = $row['author'];
        $article['date'] = $row['date'];
        $article['body'] = $row['body_text'];
        $this->load->view('main_ref_vw', $template);
    }

    function get_official_data($data) {
        $this->load->model('ref_model');
        if ($data):foreach ($data as $key => $value):
                $data['lid'] = $this->get_official_name($value->ref_id);
                $data['sq'] = $key + 1;
                $data['ref'][0] = $value;
                $data['wedstrijd_poule'] = $value->poule;
                $data['wedstrijd_code'] = $value->code;
                $data['wedstrijd'] = $this->mod_wedstrijd->get_wedstrijd($data);
                $data['stats'][0] = $this->ref_model->get_statistics_per_ref($value->ref_id, $value->role);
                $template['wedstrijd_official_' . $data['sq']] = $this->load->view('wedstrijd_official_vw', $data, true);
            endforeach;
        else:
            $template = "";
        endif;
        return $template;
    }

    function parse_audit($data) {
        $hlp_array = array();
        foreach ($data as $dat) {
            $hlp_data = explode(";", $dat->reden);
            array_push($hlp_array, $hlp_data);
        }
        return $hlp_array;
    }

    function get_official_name($lidnr) {
        $this->load->model('leden_model');
        $lid_data = $this->leden_model->get_lid($lidnr);
        return $lid_data;
    }

}
