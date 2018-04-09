<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//$rf = $ref[0];
//var_dump($rf);
//echo "trail " . $rf;
?>

<div id="off_hist_body">
    <?php
    if ($ref_lid):foreach ($ref_lid as $row):
            //var_dump($ld);
//            $wd = $wedstrijd[0];
//            $rf = $ref[0];
//            $sts = $stats[0];
            //var_dump($rf);
            //die;
            //die;
            //if (!isset($hlp_datum) or $hlp_datum <> $wedstrijd->datum) {
            //  if ((isset($wedstrijd_datum_tijd_selected) and $wedstrijd_datum_tijd_selected === $wedstrijd->tijd) or ( !isset($wedstrijd_datum_tijd_selected))) {
            ?>
            <div id='off_hist_main_container' class="main_container">
                <?php
                //var_dump($row);
                //die;
                echo $row['run_id'] . " " . $row['run_id_sq'] . " ";
                echo $row['poule'] . " " . $row['code'] . " ";
                echo $row['role'] . " " . $row['match_nivo'] . " ";
                echo $row['datum'] . " " . $row['tijd'] . " " . $row['thuis'];
                ?>
                <?php
                //$hlp_datum = $wedstrijd->datum;
                ?>

            </div>
            <?php
            //}
            // }
        endforeach;
    else:
        ?>
        <h4>No entry yet!</h4>
    <?php endif; ?>
</div>

