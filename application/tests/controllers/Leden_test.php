<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Leden_test
 *
 * @author gerard
 */
class Leden_test extends TestCase {

    //put your code here
    public function test_CheckAntwoord() {
        $output = $this->request('GET', 'Leden/CheckAntwoord/yes');
        $this->assertTrue(true, $output);
    }

    public function test_CheckIdTrue() {
        $output = $this->request('GET', 'Leden/CheckId(1)');
        $this->assertTrue(true, $output);
        print $output;
    }

    public function test_CheckIdFalse() {
        $output = $this->request('GET', 'Leden/CheckId/');
        $this->assertFalse(false, $output);
    }

    /* public function test_UpdateBardienst() {
      $obj = new Leden_model();
      $obj->id = 78864;
      $obj->antwoord_bardienst = 1;
      $output = $this->request('GET', 'Leden/UpdateBardienst');
      $this->assertTrue(true, $output);
      } */

    public function test_retrieve_db() {
        
    }

    /*
      public function test_method_404() {
      $this->request('GET', 'welcome/method_not_exist');
      $this->assertResponseCode(404);
      }

      public function test_APPPATH() {
      $actual = realpath(APPPATH);
      $expected = realpath(__DIR__ . '/../..');
      $this->assertEquals(
      $expected, $actual, 'Your APPPATH seems to be wrong. Check your $application_folder in tests/Bootstrap.php'
      );
      }
     */
}
