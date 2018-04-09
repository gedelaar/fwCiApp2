<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "wedstrijden";
?>


<div id="body">
    <?php
    $hlp_poule = "";
    $hlp_code = "";
    $file = "";
    if ($wedstrijden):foreach ($wedstrijden as $wedstrijd):
            ?>
            <form action="test_view" method="post">
                <div id='main_container' class="main_container">
                    <?php
                    //var_dump($wedstrijd);
                    //die;
                    $klas = "non_sel";
                    if (isset($wedstrijd_datum_selected) and $wedstrijd_datum_selected === $wedstrijd->datum) {
                        if ((isset($wedstrijd_datum_tijd_selected) and $wedstrijd_datum_tijd_selected === $wedstrijd->tijd) or ( !isset($wedstrijd_datum_tijd_selected))) {
                            $klas = "sel";
                        }
                        //$naam = $wedstrijd->Voornaam . " " . $wedstrijd->Tussenvoegsel . " " . $wedstrijd->Naam;
                        if ($wedstrijd->accode === "VHTSC") {
                            ?>
                            <input type = "submit" value = "plan_wedstrijd" name = "<?php echo $wedstrijd->poule; ?>-<?php echo $wedstrijd->code; ?>">
                            <input type = "submit" value = "plan_scheidsrechter" name = "<?php echo $wedstrijd->poule; ?>-<?php echo $wedstrijd->code; ?>">
                            <input type = "submit" value = "plan_tafelaar" name = "<?php echo $wedstrijd->poule; ?>-<?php echo $wedstrijd->code; ?>">
                            <input type = "submit" value = "officials" name = "<?php echo $wedstrijd->poule; ?>-<?php echo $wedstrijd->code; ?>">
                            <input type="hidden" value="wd_datum_tijd" name="<?php echo $wedstrijd->tijd; ?>">
                            <input type="hidden" value="wd_datum" name="<?php echo $wedstrijd->datum; ?>">                            
                            <br>
                            <?php
                        }
                        echo '<div class="' . $klas . '">';
                        echo $wedstrijd->datum . " " . $wedstrijd->tijd . " ";
                        echo $wedstrijd->poule . " " . $wedstrijd->code . " ";
                        echo $wedstrijd->thuis . "<->" . $wedstrijd->uit . " ";
                        echo $wedstrijd->team . " " . $wedstrijd->nivo;
                        echo "<br>" . $wedstrijd->naam01 . " " . $wedstrijd->naam02 . " " . $wedstrijd->naam03 . " " . $wedstrijd->naam04 . " " . $wedstrijd->naam05;
                        echo "</div>";
                        ?>
                        <div class="tooltip">    
                            <?php
                            echo "<br>";
                            //   echo $naam . " (<i>" . $wedstrijd->ref_team . "</i>)";
                            //$ref_team = $this->team_model->get_naam_from_team_lidnr($wedstrijd->ref_id);
                            ?>
                            <span class="tooltiptext"><?php echo ""; ?></span>
                        </div>
                        <?php
                        //   echo $wedstrijd->nivo . " (" . $wedstrijd->awgr . ")";
                        //var_dump($wedstrijd);die;
                        //$hlp_file = $wedstrijd->run_id . "-" . $wedstrijd->run_id_sq . "-" . $wedstrijd->poule . "-" . $wedstrijd->code . "-" . $wedstrijd->categorie . "-";
                        //$hlp_file.= $wedstrijd->nivo . "-" . $wedstrijd->run_id_sq . "-" . $wedstrijd->ref_id;
                        //if (isset($wedstrijd->run_id)) {
                        ?>
                        <?php
                        //}
                        //}
                    }
                    ?>
                    </text>
                </div>
            </form>
            <?php
            $hlp_poule = $wedstrijd->poule;
            $hlp_code = $wedstrijd->code;
        endforeach;
    else:
        ?>
        <h4>No entry yet!</h4>
    <?php endif; ?>
</div>

