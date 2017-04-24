<html>
<head>
    <title>DRUCKSTUDIO: FEHLER GEFUNDEN</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/main.css" />
</head>
<body class="error404">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
<?php
    // Produkt-Nr auslesen
    $PID = (int)$_GET['PID'];
    
    // Links
    $startLink = "./";
    $prodLink = "index.php";
    if($PID>0) {
        $prodLink .= "?PID=$PID";
    }

    // ERROR PAGES
    switch($_GET['errorPage']) {
        // PRINT OUTPUT: NO LEGAL PRODUCT
        case 'PRINT':
    ?>
    <div class="jumbotron ds-error-box">
      <h1>FEHLER IM DRUCKFORMAT</h1>
      <p>Die Ausgabe des Produkts im Druckformat konnte nicht umgesetzt werden. Das Produkt erf&uuml;llt nicht alle Voraussetzungen. 
      Bitte rufe die Produkt-Bearbeitung auf, um die Fehler des Produkts zu sehen. Nachdem die Fehler korrigiert sind, kannst du das Druckformat erneut abrufen.</p>
      <p>
        <a class="btn btn-warning btn-lg" href="<?php echo $startLink ?>" role="button">ZUR&Uuml;CK ZUR STARTSEITE</a>
        <a class="btn btn-primary btn-lg" href="<?php echo $prodLink ?>" role="button">PRODUKT BEARBEITEN</a>
      </p>
    </div>
    <?php
        break;
        
        // DEFAULT ERROR: NO PAGE FOUND
        default:
?>
    <div class="jumbotron ds-error-box">
      <h1>INHALTE NICHT GEFUNDEN</h1>
      <p>Die gesuchte Seite wurde nicht gefunden. M&ouml;glicherweise gab es Komplikationen bei der Produkt-Bearbeitung. 
      Du kannst unten wahlweise zur Startseite oder zur Produkt-Bearbeitung zur&uuml;ckkehren.</p>
      <p>
        <a class="btn btn-warning btn-lg" href="<?php echo $startLink ?>" role="button">ZUR&Uuml;CK ZUR STARTSEITE</a>
        <a class="btn btn-primary btn-lg" href="<?php echo $prodLink ?>" role="button">ZUR&Uuml;CK ZUM PRODUKT</a>
      </p>
    </div>
<?php    
    }

?>
            </div>
        </div>
    </div>
</body>
</html>