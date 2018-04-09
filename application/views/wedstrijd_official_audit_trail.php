<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$rf = $ref[0];
//var_dump($rf);
//echo "trail " . $rf;
?>

<div id="audit_body">
    <?php
    if ($audit_trail):foreach ($audit_trail as $row):
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
            <div id='audit_main_container' class="main_container">
                <?php
                //var_dump($wedstrijd);
                //die;
                echo $row->reden;
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

