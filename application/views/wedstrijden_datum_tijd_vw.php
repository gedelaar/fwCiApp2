<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "wedstrijd tijd";
?>


<div id="wdt_body">
    <?php
    echo $wedstrijd_datum_selected;
    if ($wedstrijden):foreach ($wedstrijden as $wedstrijd):
            ?>
            <form action="test_view" method="post">
                <div id='wdt_main_container' class="main_container">
                    <?php
                    if (isset($wedstrijd_datum_selected) and $wedstrijd_datum_selected === $wedstrijd->datum) {
                        //echo $wedstrijd_datum_selected;
                        if (!isset($hlp_tijd) or $hlp_tijd <> $wedstrijd->tijd) {
                            //echo $wedstrijd->tijd;
                            ?>
                            <input type="submit" value="<?php echo $wedstrijd->tijd; ?>" name="<?php echo $wedstrijd->tijd; ?>">
                            <input type="hidden" value="wd_datum_tijd" name="<?php echo $wedstrijd->tijd; ?>">
                            <input type="hidden" value="wd_datum" name="<?php echo $wedstrijd->datum; ?>">                            
                            <?php
                            $hlp_tijd = $wedstrijd->tijd;
                        }
                    }
                    ?>
                </div>
            </form>
            <?php
        endforeach;
    else:
        ?>
        <h4>No entry yet!</h4>
    <?php endif; ?>
</div>


