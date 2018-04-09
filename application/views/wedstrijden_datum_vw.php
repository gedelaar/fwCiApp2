<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "wedstrijd datums";
?>

<div id="wd_body">
    <?php
    if ($wedstrijden):foreach ($wedstrijden as $wedstrijd):
            if (!isset($hlp_datum) or $hlp_datum <> $wedstrijd->datum) {
                ?>
                <div id='wd_main_container' class='main_container'>
                    <form action="test_view" method="post">
                        <?php
                        //echo $wedstrijd->datum;
                        ?>
                        <input type="submit" value="<?php echo $wedstrijd->datum; ?>" name="<?php echo $wedstrijd->datum; ?>">
                        <input type="hidden" value="wd_datum" name="<?php echo $wedstrijd->datum; ?>">

                        <?php
                        $hlp_datum = $wedstrijd->datum;
                        ?>
                    </form>

                </div>
                <?php
            }
        endforeach;
    else:
        ?>
        <h4>No entry yet!</h4>
    <?php endif; ?>
</div>

