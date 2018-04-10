<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testLeden_model
 *
 * @author gerard
 */
class Leden_model_test extends TestCase {

    //put your code here
    public function test_CreateIdForMail() {
        $obj = new Leden_model();
        $obj->setId(20000);
        $output = $this->request('GET', 'Leden_model/CreateIdForMail/2');
        $this->assertNotNull($output, "hello");
    }

    public function test_DetectIdFromMailTrue() {
        $output = $this->request('GET', 'Leden_model/DetectIdFromMail/192045990002');
        $this->assertTrue(true, $output);
    }

    public function test_DetectIdFromMailFalse() {
        $objBardienst = new leden_model();
        $output = $objBardienst->DetectIdFromMail('19000202');
        $this->assertFalse(false, $output);
    }

    public function test_CheckstringLen() {
        $this->objBardienst = new leden_model();
        $output = $this->objBardienst->CheckstringLen('123');
        $this->assertEquals($output, 1);
    }

}
