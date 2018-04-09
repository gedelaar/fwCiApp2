<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of sendmails
 *
 * @author gerard
 */
//put your code here

class Sendmails extends CI_Controller {

    public function htmlmail() {
        $this->email->initialize($this->config->item('smtp'));
        $this->email->set_newline("\r\n");

        $this->email->from($this->config->item('smtp')['smtp_user'], $this->config->item('mail_from'));
        $data = array(
            'userName' => 'test',
            'thuis_ploeg' => "thuis",
            'uit_ploeg' => "uit",
            'mail_id' => 123456
        );
        $userEmail = "test@edelaar.nl";
        $this->email->to($userEmail); // replace it with receiver mail id
        $this->email->subject($this->config->item('mail_subject1')); // replace it with relevant subject
        $this->email->message($this->load->view($this->config->item('mail_body1'), $data, TRUE));
        $this->email->send();
    }

}
