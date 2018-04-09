<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "ref indeling";
?>
<form action="verwerk_vw_wedstrijden" method="post">

    <div id="body">
        <?php
        $hlp_poule = "";
        $hlp_code = "";
        $file = "";
        if ($wedstrijden):foreach ($wedstrijden as $wedstrijd):
                ?>
                <div id='main_container'>
                    <?php
                    $naam = $wedstrijd->Voornaam . " " . $wedstrijd->Tussenvoegsel . " " . $wedstrijd->Naam;
                    ?>


                    <br>
                    <?php
                    ?>
                    <?php
                    //var_dump($wedstrijd);die;
                    $hlp_file = $wedstrijd->run_id . "-" . $wedstrijd->run_id_sq . "-" . $wedstrijd->poule . "-" . $wedstrijd->code . "-" . $wedstrijd->categorie . "-";
                    $hlp_file.= $wedstrijd->nivo . "-" . $wedstrijd->run_id_sq . "-" . $wedstrijd->ref_id;
                    if (isset($wedstrijd->run_id)) {
                        echo $hlp_file;
                        ?>

                        <?php
                    }
                    ?>
                    </text>
                </div>
                <?php
                $hlp_poule = $wedstrijd->poule;
                $hlp_code = $wedstrijd->code;
            endforeach;
        else:
            ?>
            <h4>No entry yet!</h4>
        <?php endif; ?>
    </div>
    <input type="submit" value="Submit">
</form>
