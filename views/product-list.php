<?php
/*
 * PRODUCT LIST
 * (no product-type given)
 *  
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

$title = "DRUCKSTUDIO / PRODUKT AUSSUCHEN";

?>
<html>
<head>
    <meta charset="utf-8" /> 
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/main.css" />
</head>
<body class="ds-product-choice">
    <div id="ds-main">
        <ul class="ds-product-overview">
            <li>
                <a href="?product=1&state=new" title="24 Square-Prints">
                    <img src="img/layout/product-overview-squares.png" />
                    <h3>24 Square-Prints</h3>
                </a>
            </li>
            <li>
                <a href="?product=2&state=new" title="2x4 Stripes">
                    <img src="img/layout/product-overview-stripes.png" />
                    <h3>2x4 Stripes</h3>
                </a>
            </li>
            <li>
                <a href="?product=1&state=new" title="24 Square-Prints">
                    <img src="img/layout/product-overview-squares.png" />
                    <h3>24 Square-Prints</h3>
                </a>
            </li>
            <li>
                <a href="?product=2&state=new" title="2x4 Stripes">
                    <img src="img/layout/product-overview-stripes.png" />
                    <h3>2x4 Stripes</h3>
                </a>
            </li>
            <li>
                <a href="?product=1&state=new" title="24 Square-Prints">
                    <img src="img/layout/product-overview-squares.png" />
                    <h3>24 Square-Prints</h3>
                </a>
            </li>
            <li>
                <a href="?product=2&state=new" title="2x4 Stripes">
                    <img src="img/layout/product-overview-stripes.png" />
                    <h3>2x4 Stripes</h3>
                </a>
            </li>
        </ul>
    </div>
</body>
</html>