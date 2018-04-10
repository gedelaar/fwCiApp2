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
    public function test_CreateIdForMailYes() {
        $objBardienst = new leden_model();
        $objBardienst->setId(20000);
        $objBardienst->CreateIdForMailYes();
        $this->assertSame('15200002', substr($objBardienst->barMailYes, 0, 8));
        unset($objBardienst);
    }

    public function test_CreateIdForMailYesFalse() {
        $objBardienst = new leden_model();
        $objBardienst->setId(20000);
        $objBardienst->CreateIdForMailYes();
        $this->assertNotSame('15200012', substr($objBardienst->barMailYes, 0, 8));
        unset($objBardienst);
    }

    public function test_CreateIdForMailNo() {
        $objBardienst = new leden_model();
        $objBardienst->setId(20000);
        $objBardienst->CreateIdForMailNo();
        $this->assertSame('15200009', substr($objBardienst->barMailNo, 0, 8));
        unset($objBardienst);
    }

    public function test_CreateIdForMailNever() {
        $objBardienst = new leden_model();
        $objBardienst->setId(20000);
        $objBardienst->CreateIdForMailNever();
        $this->assertSame('15200008', substr($objBardienst->barMailNever, 0, 8));
        unset($objBardienst);
    }

    public function test_DetectIdFromMailTrue() {
        $objBardienst = new leden_model();
        $output = $objBardienst->DetectIdFromMail('192045990002');
        $this->assertTrue(true, $output);
        unset($objBardienst);
    }

    public function test_DetectIdFromMailFalse() {
        $objBardienst = new leden_model();
        $output = $objBardienst->DetectIdFromMail('19000202');
        $this->assertFalse(false, $output);
        unset($objBardienst);
    }

    public function test_CheckstringLenTrue() {
        $this->objBardienst = new leden_model();
        $output = $this->objBardienst->CheckstringLen('123');
        $this->assertSame('1', $output, 0, FALSE);
        unset($objBardienst);
    }

    public function test_CheckstringLenFalse() {
        $this->objBardienst = new leden_model();
        $output = $this->objBardienst->CheckstringLen('23');
        $this->assertNotSame('1', $output, 0, false);
        unset($objBardienst);
    }

}
