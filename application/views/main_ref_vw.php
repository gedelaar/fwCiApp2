<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "main_ref";
?>

<!DOCTYPE html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css"  href="http://www.forwodians.nl/fwBar/css/main.css">
    <title>Page Title</title>
    <meta name="description" content="Write some words to describe your html page">
</head>
<body>
    <div class="blended_grid">
        <div class="topBanner">
            <?php echo "top"; ?>
        </div>
        <div class="leftColumn">
            <div class="leftContent1">
                <?php echo $wedstrijd_datum; ?>
            </div>
            <div class="leftContent2">
                <?php
                if (isset($wedstrijd_datum_tijd)) {
                    echo $wedstrijd_datum_tijd;
                }
                ?>
            </div>
        </div>
        <div class="midColumn">
            <div class="midContent1">
                <?php echo $wedstrijden; ?>
            </div>
            <div class="midContent2">
                <?php
                if (isset($wd_official['wedstrijd_official_1'])) {
                    echo $wd_official['wedstrijd_official_1'];
                }
                ?>

            </div>
            <div class="midContent3">
                <?php
                if (isset($wd_official['wedstrijd_official_2'])) {
                    echo $wd_official['wedstrijd_official_2'];
                }
                ?>

            </div>
            <div class="midContent4">
                <?php
                if (isset($wd_official['wedstrijd_official_3'])) {
                    echo $wd_official['wedstrijd_official_3'];
                }
                ?>

            </div>
            <div class="midContent5">
                <?php
                if (isset($wd_official['wedstrijd_official_4'])) {
                    echo $wd_official['wedstrijd_official_4'];
                }
                ?>

            </div>
            <div class="midContent6">
                <?php
                if (isset($wd_official['wedstrijd_official_5'])) {
                    echo $wd_official['wedstrijd_official_5'];
                }
                ?>
            </div>

        </div>
        <div class="rightColumn">
            <div class="rightContent1">
                <?php
                if (isset($audit_trail)) {
                    echo $audit_trail;
                }
                ?>
            </div>
            <div class="rightContent2">
                <?php
                if (isset($ref_lid)) {
                    echo $ref_lid;
                }
                ?>
            </div>
        </div>
        <div class="bottomBanner">
        </div>
    </div>
</body>
</html>