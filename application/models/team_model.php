<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of team_model
 *
 * @author gerard
 */
class team_model extends CI_Model {

    //put your code here
    //public $code;

    function get_team_from_poule_($wedstrijd_poule) {
        $this->db->where('poulid', $wedstrijd_poule);
        $query = $this->db->get('teams');
		
        return $query->result();
    }

    function get_team_from_poule($data) {
        $query = $this->get_team_from_poule_($data['wedstrijd_poule']);
		//echo $this->db->last_query();
        return $query;
    }

    /*   function get_poulid_from_team_lidnrs($lidnummer) {
      //SELECT poulid FROM `teamindeling` as ti join teams as t on ti.team = t.code where ti.lidnummer = 1
      $this->db->select('poulid');
      $this->db->from('teamindeling');
      $this->db->join('teams', 'teamindeling.team = teams.code or teamindeling.bank_team = teams.code');
      $this->db->where('lidnummer',$lidnummer);
      $query = $this->db->get();
      $data = $query->result();
      //echo "<br>";
      //print_r($this->db->last_query());
      //echo "<br>";
      if($query->num_rows() > 0){
      //echo "<br>".$query->num_rows();
      //print_r($data);
      //die;
      //return $data[0]->poulid;}
      return $data;}
      else {
      $data[0]='no_poule';
      return $data;
      }
      } */

    function get_poulid_from_team_lidnr($lidnummer) {
        //SELECT poulid FROM `teamindeling` as ti join teams as t on ti.team = t.code where ti.lidnummer = 1
        $this->db->select('poulid');
        $this->db->from('teamindeling');
        $this->db->join('teams', 'teamindeling.team = teams.code');
        $this->db->where('lidnummer', $lidnummer);
        $query = $this->db->get();
        $data = $query->result();
        //echo "<br>eigen wedstrijd";
        //print_r($this->db->last_query());
        //echo "<br>";
        if ($query->num_rows() > 0) {
            //echo "<br>".$query->num_rows();
            //print_r($data[0]->poulid);
            //die;
            return $data[0]->poulid;
        }
        //return $data;}
        else {
            //$data[0]='no_poule';
            return 'no_poule';
        }
    }

    function get_naam_from_team_lidnr($lidnummer) {
        //SELECT poulid FROM `teamindeling` as ti join teams as t on ti.team = t.code where ti.lidnummer = 1
        $this->db->select('code');
        $this->db->from('teamindeling');
        $this->db->join('teams', 'teamindeling.team = teams.code');
        $this->db->where('lidnummer', $lidnummer);
        $query = $this->db->get();
        $data = $query->result();
        //echo "<br>eigen wedstrijd";
        //print_r($this->db->last_query());
        //echo "<br>";
        if ($query->num_rows() > 0) {
            //echo "<br>".$query->num_rows();
            //print_r($data[0]->poulid);
            //die;
            return $data[0]->poulid;
        }
        //return $data;}
        else {
            //$data[0]='no_poule';
            return 'no_poule';
        }
    }

    function get_poulid_from_bank_team_lidnr($lidnummer) {
        //SELECT poulid FROM `teamindeling` as ti join teams as t on ti.team = t.code where ti.lidnummer = 1
        $this->db->select('poulid');
        $this->db->from('teamindeling');
        $this->db->join('teams', 'teamindeling.bank_team = teams.code');
        $this->db->where('lidnummer', $lidnummer);
        $query = $this->db->get();
        $data = $query->result();
        //echo "<br>bankspeler";
        //print_r($this->db->last_query());
        //echo "<br>";
        if ($query->num_rows() > 0) {
            //echo "<br>".$query->num_rows();
            //print_r($data);
            //die;
            return $data[0]->poulid;
        }
        //return $data;}
        else {
            //$data[0]='no_poule';
            return 'no_poule';
        }
    }

    function get_poulid_as_coach($lidnummer) {
        //SELECT poulid FROM `teamindeling` as ti join teams as t on ti.team = t.code where ti.lidnummer = 1
        $this->db->select('poulid');
        $this->db->from('team_begeleider');
        $this->db->join('teams', 'team_begeleider.code = teams.code');
        $this->db->where('lidnr', $lidnummer);
        $this->db->where('ROLE', "C");
        $query = $this->db->get();
        $data = $query->result();
        //echo "<br>";
        //print_r($this->db->last_query());
        //echo "<br>";
        if ($query->num_rows() > 0) {
            //print_r($data[0]->poulid);
            //die;
            return $data[0]->poulid;
        } //retourneert poulid van gecoached team!
        else {
            return 'no_poule';
        }
    }

    function get_coaches($data) {
        $team_arr = $data['team'][0];
        //echo "<br>";echo "<br>";echo "<br>";
        //var_dump($team_arr);
        //die;
        //$team = $team_arr['CODE'];
        $this->db->select('LIDNR');
        $this->db->from('teams');
        $this->db->join('team_begeleider', 'team_begeleider.code = teams.code');
        $this->db->where('team_begeleider.code', $team_arr->CODE);
        $query = $this->db->get();
        //print_r($this->db->last_query());die;
        //$data = $query->result();
        //echo "<br> alle coaches";
        //var_dump($query);die;
        return $query->result_array();
    }

    function get_team_nivo($data) {
        $this->db->select('nivo');
        $this->db->from('teams');
        $this->db->where('poulid', $data['poule']);
        $query = $this->db->get();
        var_dump($query->result_array());
        return $query->result_array();
    }

}
