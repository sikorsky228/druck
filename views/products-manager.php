<?php
/*
 * PRODUCT MANAGEMENT
 *  
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

$title = "DRUCKSTUDIO / PRODUKTE VERWALTEN";

$qry = $DSDB->query("
    SELECT p.id, p.directory, p.last_update, pt.fullname FROM ".DS_TABLE_PRODUCT." AS p
    LEFT JOIN ".DS_TABLE_PRODUCT_TYPE." AS pt ON pt.id = p.product_type
");

?>
<html>
<head>
    <meta charset="utf-8" /> 
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/main.css" />
</head>
<body class="ds-product-management">
    <div id="ds-main">
        <div class="ds-content">
            <h1>PRODUKTVERWALTUNG</h1>
            <div class="table-responsive">
                <table class="ds-product-overview table">
                    <tr>
                        <th class="col-1 center">#</th>
                        <th class="col-2">PRODUKT</th>
                        <th class="col-3">ORDNER NR</th>
                        <th class="col-4">LETZTE &Auml;NDERUNG</th>
                        <th colspan="3"></th>
                    </tr>
                    <?php
                    if($qry) {
                        while($row = $qry->fetch_assoc()) {
                    ?>
                    <tr data-id="<?php echo $row['id'] ?>">
                        <td class="col-1 center"><?php echo $row['id'] ?></td>
                        <td class="col-2"><?php echo $row['fullname'] ?></td>
                        <td class="col-3"><?php echo $row['directory'] ?></td>
                        <td class="col-4"><?php echo date("d.m.Y - H:i",strtotime($row['last_update'])) ?> Uhr</td>
                        <td class="col-5 right"><a class="btn ds-button-print" href="product-print.php?PID=<?php echo $row['id'] ?>&amp;format=print" target="_blank"><i class="fa fa-print"></i> PDF</a></td>
                        <td class="col-6 right"><a class="btn ds-button-edit" href="index.php?PID=<?php echo $row['id'] ?>"><i class="fa fa-edit"></i> &Ouml;FFNEN</a></td>
                        <td class="col-7 right"><a class="btn ds-button-remove"><i class="fa fa-remove"></i> L&Ouml;SCHEN</a></td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery.fileupload.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/bootstrap.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/main.js"></script>
</body>
</html>