<?php ?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <title>Forwodians - Bardienst mail</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <div>
            <div> 
                <div style="font-size: 26px;
                     font-weight: 700;
                     letter-spacing: -0.02em;
                     line-height: 32px;
                     color: #41637e;
                     font-family: sans-serif;
                     text-align: center" align="center" id="emb-email-header">

                </div>

                <br>Wij, de barcommissie, doen erg veel moeite om tijdens de thuiswedstrijden van uw zoon/dochter de bardiensten in te
                roosteren. <p>Per telefoon ouders benaderen kost veel tijd. 
                    Daarom doen we dit via de mail. Aangezien uw kind tijdens 
                    de wedstrijd graag toeschouwers heeft, is het juist tijdens 
                    deze wedstrijd handig als er een bardienst wordt gedraaid 
                    door 1 van de ouders.</p> 
                Deze barinkomsten drukken de kosten van de club. Wij hebben een bardienstrooster gemaakt en wij
                gaan ervan uit dat alle diensten ook kunnen worden gedraaid. 
                <p>Wij hebben U voor de bardienst ingedeeld voor onderstaande wedstrijd:</p>
                <p><h2>"<?php echo $thuis_ploeg ?> " tegen " <?php echo $uit_ploeg ?></h2></p>
                <br>Mocht u van mening zijn dat u geen bardienst hoeft te draaien omdat u andere activiteiten voor de vereniging verricht,
                dan klikt u op de link hieronder en zult u dit deel van het seizoen niet verder worden ingedeeld voor bardiensten.<br>
                <?php echo anchor("leden/neverbardienst/" . strlen($mail_id) . $mail_id . NOW(), "vrijstelling van bardienst", "afmelding") ?>
                <br><br>
                anchor("http://www.forwodians.nl/fwBar/Handleiding_bardienst_2013-2014v1.pdf", 'handleiding bardienst')
                <br>

                "voor andere vragen of opmerkingen, graag even een reply op dit mailadres!";

            </div>

    </body>

</html>
