<?php
/*
 * CHECKOUT
 *  
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

$title = "DRUCKSTUDIO / BESTELLUNG ABSCHLIEßEN";

?>
<html>
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/main.css" />
</head>
<body>
    <div id="ds-main">
        <div id="ds-content">
            <div class="ds-wrapper">
                <div id="ds-checkout">
                    <h1>Bestellung konnte nicht abgeschlossen werden!</h1>
                    <span class="ds-info">
                        Es ist ein Fehler bei ihrer Bestellung vorgefallen. Bitte geben Sie die Bestellung erneut auf.<br />
                        Die Daten sind natürlich noch vorhanden. Klicken Sie einfach <a href="?">HIER</a> um zurück zu gelangen.<br /><br />
                        Sollte das Problem dadurch nicht behoben sein, geben Sie diesen Fehler bitte an den Support weiter.
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>