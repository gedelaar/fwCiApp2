<html>

    <?php
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */
    $this->load->helper('url');
    // $this->load->model('team_model');
//echo $css;
    ?>
    <link rel="stylesheet" type="text/css"  href="http://www.forwodians.nl/fwBar/css/main.css">

    <!DOCTYPE html>
    <style>
        .tooltip {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 1s;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        body, html{
            margin: 0;
            height: 100%;
        }

        body{
            background: url(3_kolommen_layout/left_bg.gif) repeat-y 0 0;
        }

        #main_container{
            float: left;
            width: 100%;
            _height: 100%;
            margin: 0px 0 0 0;
            background: url(3_kolommen_layout/right_bg.gif) repeat-y top right;
        }

        /* container boxen */
        #header_container{
            width: 100%;
            height: 81px;
            padding: 46px 0 0 0;
            text-align: center;
            background: url(3_kolommen_layout/header_bg.gif) repeat-x 0 46px;
        }

        #links_container{
            float: left;
            width: 164px;
        }

        #content_container{
            margin: 0 259px 0 164px;
        }

        #rechts_container{
            float: right;
            width: 259px;
        }

        #footer_container{
            clear: both;
            height: 46px;
            background: url(3_kolommen_layout/bottom_right.gif) no-repeat top right;
            width: 100%;
            text-align: center;
        }

        /* content boxen */
        #header{
            width: 100%;
            height: 81px;
            padding: 10px 0;
        }

        #rechts, #links, #content{
            padding: 10px 10px 0 20px;
        }

        #footer{
            padding: 20px 163px 0 0;
        }

        /* background image boxen */
        #top_left{
            position: absolute;
            width: 163px;
            height: 89px;
            top: 0;
            left: 0;
            background: url(3_kolommen_layout/top_left.gif) no-repeat 0 0;
        }

        #top_right{
            position: absolute;
            width: 264px;
            height: 89px;
            top: 0;
            right: 1px;
            background: url(3_kolommen_layout/top_right.gif) no-repeat 0 0;
        }

        #footer_left{
            height: 46px;
            width: 163px;
            float: left;
            background: url(3_kolommen_layout/bottom_left.gif) no-repeat 0 0;
        }

    </style>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Indeling</title>
    </head>

    <body>
        <div id="page">
            <h2>Te plannen wedstrijden</h2>
            <form action="verwerk_vw_wedstrijden" method="post">

                <div id="menu">
                    <?php $this->load->view('menus/menu1'); ?>
                </div>
                <div id="content_container">
                    <?php
                    $hlp_poule = "";
                    $hlp_code = "";
                    $file = "";
                    //if(isset($run_id)){
                    //  $run_id="";
                    //}
                    //if(isset($run_id_sq)){
                    //   $run_id_sq="";
                    //}
                    if ($wedstrijden):foreach ($wedstrijden as $wedstrijd):
                            ?>
                            <div id='links_container'>

                            </div>
                            <div id='main_container'>
                                <label for = "msg"></label>
                                <text id = "msg" name = "user_message" style="width: 1202px;">

                                <?php
                                $naam = $wedstrijd->Voornaam . " " . $wedstrijd->Tussenvoegsel . " " . $wedstrijd->Naam;

                                if (($wedstrijd->poule === $hlp_poule) and ( $wedstrijd->code === $hlp_code)) {
                                    ?>

                                    <?php
                                } else {
                                    echo "<br><hr>";
                                    ?>
                                    <input type="submit" value="plan_wedstrijd" name="<?php echo $wedstrijd->poule; ?><?php echo $wedstrijd->code; ?>">
                                    <input type="submit" value="plan_scheidsrechter" name="<?php echo $wedstrijd->poule; ?><?php echo $wedstrijd->code; ?>">
                                    <input type="submit" value="plan_tafelaar" name="<?php echo $wedstrijd->poule; ?><?php echo $wedstrijd->code; ?>">
                                    <input type="submit" value="del_planning" name="<?php echo $wedstrijd->run_id; ?>">
                                    <input type="submit" value="bevestig_planning" name="<?php echo $wedstrijd->run_id; ?>">

                                    <br>
                                    <?php
                                }
                                echo $wedstrijd->run_id . "-" . $wedstrijd->run_id_sq . " " . $wedstrijd->datum . " " . $wedstrijd->tijd . " ";
                                echo $wedstrijd->poule . " " . $wedstrijd->code . " ";
                                echo $wedstrijd->thuis . "<->" . $wedstrijd->uit . " " . $wedstrijd->ref_id;
                                ?>
                                <div class="tooltip">    
                                    <?php
                                    echo $naam . " (<i>" . $wedstrijd->ref_team . "</i>)";
                                    //$ref_team = $this->team_model->get_naam_from_team_lidnr($wedstrijd->ref_id);
                                    ?>
                                    <span class="tooltiptext"><?php echo $wedstrijd->ref_team; ?></span>
                                </div>
                                <?php
                                echo $wedstrijd->nivo . " (" . $wedstrijd->awgr . ")";
                                //var_dump($wedstrijd);die;
                                $hlp_file = $wedstrijd->run_id . "-" . $wedstrijd->run_id_sq . "-" . $wedstrijd->poule . "-" . $wedstrijd->code . "-" . $wedstrijd->categorie . "-";
                                $hlp_file.= $wedstrijd->nivo . "-" . $wedstrijd->run_id_sq . "-" . $wedstrijd->ref_id;
                                if (isset($wedstrijd->run_id)) {
                                    ?>
                                    <input type="submit" value="audit_trail" name="<?php echo $wedstrijd->run_id; ?>-<?php echo $wedstrijd->run_id_sq; ?>-<?php echo $wedstrijd->ref_id; ?>">
                                    <input type="submit" value="vervang_pers" name="<?php echo $hlp_file; ?>">
                                    <input type="submit" value="bevestig_planning" name="<?php echo $wedstrijd->poule; ?>-<?php echo $wedstrijd->code; ?>">
                                    <?php
                                    if ($wedstrijd->naam01 <> '' or $wedstrijd->naam03 <> '') {
                                        echo " ! al in wedstrijd opgenomen !!";
                                    }
                                }
                                ?>
                                </text>
                            </div>
                            <div id="rechts_container">
                                <?php
                                //var_dump($run_id);
                                if ((isset($run_id) and $run_id == $wedstrijd->run_id) and ( $run_id_sq == $wedstrijd->run_id_sq) and ( $wedstrijd->run_id > 0)) {
                                    echo $trailhtml;
                                }
                                ?>
                            </div>
                            <?php
                            //var_dump($wedstrijd);
                            /* $naam = $wedstrijd->Voornaam . " " . $wedstrijd->Tussenvoegsel . " " . $wedstrijd->Naam;
                              if (($wedstrijd->poule === $hlp_poule) and ( $wedstrijd->code === $hlp_code)) {
                              $file.=$wedstrijd->datum . " " . $wedstrijd->tijd . " " . $wedstrijd->thuis . " " . $wedstrijd->uit . " " . $wedstrijd->ref_id . " " . $naam . " <br>";
                              } else {
                              echo $file . "<br><hr><br>";
                              $file = "<br><a href='http://www.forwodians.nl/fwBar/index.php/referee/select_refs/" . $wedstrijd->poule . "/" . $wedstrijd->code . "'>plan</a> -> ";
                              $file.="" . $wedstrijd->poule . " " . $wedstrijd->code . "<br>";
                              $file.= $wedstrijd->datum . " " . $wedstrijd->tijd . " " . $wedstrijd->thuis . " " . $wedstrijd->uit . " " . $wedstrijd->ref_id . " " . $naam . "<br> ";
                              } */

                            $hlp_poule = $wedstrijd->poule;
                            $hlp_code = $wedstrijd->code;
                        endforeach;
                    else:
                        //$file = "";
                        ?>
                        <h4>No entry yet!</h4>
                    <?php endif; ?>
                </div>
                <input type="submit" value="Submit">
            </form>
        </div>

    </body>
</html>
