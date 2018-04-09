<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//echo "wedstrijd officials";
?>

<div id="wdo_body">
    <?php
    if ($lid):foreach ($lid as $ld):
            $wd = $wedstrijd[0];
            $rf = $ref[0];
            $sts = $stats[0];
            echo $rf->role;
            ?>
            <div id='wdo_main_container' class="main_container">
                <form action="test_view" method="post">
                    <?php
                    echo $ld->Lidnr . " ";
                    echo $ld->Voornaam . " ";
                    echo $ld->Naam . " ";
                    echo "stats:(" . $sts . ")";
                    echo "nivo:(" . $rf->match_nivo . ")";
                    echo $rf->huidig_team . " " . $rf->bank_team;
                    ?>
                    <input type="submit" value="<?php echo $ld->Lidnr; ?>" name="<?php echo $ld->Lidnr; ?>">
                    <input type="hidden" value="wd_ref" name="<?php echo $ld->Lidnr; ?>">
                    <input type="hidden" value="wd_run_id" name="<?php echo $rf->run_id; ?>">
                    <input type="hidden" value="wd_run_id_sq" name="<?php echo $rf->run_id_sq; ?>">                    
                    <input type="hidden" value="wd_datum_tijd" name="<?php echo $wd->tijd; ?>">
                    <input type="hidden" value="wd_datum" name="<?php echo $wd->datum; ?>">   
                    <input type="hidden" value="officials" name="<?php echo $wd->poule; ?>-<?php echo $wd->code; ?>">
                    <?php
                    $hlp_file = $rf->run_id . "-" . $rf->run_id_sq . "-" . $wd->poule . "-" . $wd->code . "-" . $rf->role . "-";
                    $hlp_file .= $rf->match_nivo . "-" . $rf->run_id_sq . "-" . $rf->ref_id;
                    ?>
                    <input type="text" value="" name="new_lidnr">
                    <input type="submit" value="update" name="<?php echo $ld->Lidnr; ?>-<?php echo $rf->run_id; ?>-<?php echo $wd->poule; ?>-<?php echo $wd->code; ?>">
                    <input type="submit" value="plan" name="<?php echo $wd->poule; ?>-<?php echo $wd->code; ?>-<?php echo $wd->datum; ?>-<?php echo $wd->tijd; ?>">
                    <input type="submit" value="vervang" name="<?php echo $hlp_file; ?>">
                    <input type="submit" value="verwijder" name="<?php echo $rf->run_id; ?>">                    
                </form>
            </div>
            <?php
        endforeach;
    else:
        ?>
        <h4>No entry yet!</h4>
    <?php endif; ?>
</div>

