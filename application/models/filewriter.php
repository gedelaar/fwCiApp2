<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileWriter
 *
 * @author gerard
 */

//Should file_writer take backups of files 
define("WRITER_BACKUP", TRUE); 

//Where should file_writer store backups 
define("WRITER_RESTORE_DIR", "./restore"); 

//Should file_writer lock files while writing to them 
define("WRITER_LOCK_FILES", TRUE); 

class filewriter 
    { 
    public $files = array(); 
     
    function __construct() 
        { 
         
        } 
         
    function write($file, $content) 
        { 
        $date = date("d-m-Y_his"); 
        //Copy the original file to the restore dir 
        if(file_exists($file)) 
            { 
            if(WRITER_BACKUP) 
                { 
                copy($file, WRITER_RESTORE_DIR."/".$date."_".$file); 
                } 
            } 
             
        if(!isset($files[$file])) 
            {     
            //Make the file handle 
            $files[$file] = fopen($file, "w"); 
            if(WRITER_LOCK_FILES) 
                { 
                flock($files[$file], LOCK_EX); 
                } 
            } 
             
        //Overwrite the file 
        fwrite($files[$file], $content); 
        } 
         
    function append($file, $content) 
        { 
        $date = date("d-m-Y_his"); 
        //Copy the original file to the restore dir 
        if(file_exists($file)) 
            { 
            if(WRITER_BACKUP) 
                { 
                copy($file, WRITER_RESTORE_DIR."/".$date."_".$file); 
                } 
            } 
         
        if(!isset($files[$file])) 
            {     
            //Make the file handle 
            $files[$file] = fopen($file, "a"); 
            if(WRITER_LOCK_FILES) 
                { 
                flock($files[$file], LOCK_EX); 
                } 
            } 
             
        //Overwrite the file 
        fwrite($files[$file], $content); 
        } 
         
    function __destruct() 
        { 
        foreach($this->files as $file) 
            { 
            fclose($file); 
            } 
        if(WRITER_LOCK_FILES) 
            { 
            foreach($this->files as $file) 
                { 
                flock($file, LOCK_EX); 
                } 
            } 
        } 
    } 

?>
