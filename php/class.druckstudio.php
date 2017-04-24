<?php

class DRUCKSTUDIO {
    
    // EIGENSCHAFTEN
    // --------------------------------------------------------------------
    private $error;
    private $product;
    private $manager;
    
    // KONSTRUKTOR
    // --------------------------------------------------------------------
    function __construct($forceLoadDB=false) {
        if($forceLoadDB) {
            $_REQUEST['loading'] = true;
        }
        $this->error = false;
        $this->product = new DS_PRODUCT();
        $this->manager = new DS_MANAGER();
        // Product-ID given?
        if(array_key_exists('PID', $_REQUEST)) {
            $PID = (int)$_REQUEST['PID'];
            // Check Access
            if($this->checkAccess($PID)) {
                // Save Product in DB
                if(array_key_exists('SUBMIT', $_POST)) {
                    $this->saveDataDB();
                    $this->submitOrder();
                }
                // Load Product from DB
                else {
                    $this->loadDataByID($PID);
                }
            }
        }
        else {
            // Setup new Product
            if(array_key_exists('state', $_REQUEST) and $_REQUEST['state']=="new" && !$this->checkSetupSpam($_REQUEST['product'])) {
                unset($_SESSION['DS_INSTAGRAM']);
                if(!$this->setup($_REQUEST['product'], $_REQUEST['wc_id'])) {
                    $this->error = 'NO_PRODUCT';
                }
            }
            // Load Data from Session
            elseif(!$this->loadData()) {
                $this->error = 'NO_PRODUCT';
            }
        }
    }
    
    // LOAD DATA FROM SESSION
    // --------------------------------------------------------------------
    function loadData($PID=null) {
        $data = $_SESSION['DS_DATA'];
        // Check cached data
        if(!$data || !is_array($data) || @count($data)<=0) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Datenobjekt nicht vorhanden!");
            return false;
        }
        // Check if cached data from database is given
        $PID = (int)$PID;
        if($PID != $data['PRODUCT']['PID'] && $PID>0) {
            return false;
        } 
        // Check if product-type is valid
        $product = $this->getProductObject($data['PRODUCT']['TYPE'], $data['PRODUCT']['WCID']);
        if(!$product) {
            return false;
        }
        // Product data
        $checksum = true;
        $checksum *= $product->setPID($data['PRODUCT']['PID']);
        $checksum *= $product->setDir($data['PRODUCT']['DIR']);
        $checksum *= $product->setDate($data['PRODUCT']['DATE']);
        $materials = array();
        $materialList = $data['PRODUCT']['MATERIALS'];
        if(is_array($materialList)) {
            foreach($materialList as $key=>$material) {
                $materials[$key] = new DS_MATERIAL();
                $materials[$key]->setMID($material['MID']);
                $materials[$key]->setNr($key);
                $materials[$key]->setFile($material['file']);
                $materials[$key]->setPosition($material['position']);
                $materials[$key]->setZoom($material['zoom']);
                $materials[$key]->setAngle($material['angle']);
                $materials[$key]->setOffset($material['offset'][0],$material['offset'][1]);
                $materials[$key]->setOptions($material['options']);
            }
        }
        $checksum *= $product->setMaterials($materials);
        // Manager
        $manager = new DS_MANAGER();
        // File Manager
        $fileManager = $manager->getUploader();
        $checksum *= $fileManager->setPath( $product->getDir('uploader') );
        $fileList = $data['MANAGER']['UPLOADER']['FILES'];
        global $instagram;
        if(isset($data['MANAGER']['INSTAGRAM']['TOKEN']))
        	$manager->setInstagram(new DS_MANAGER_INSTAGRAM($instagram, $data['MANAGER']['INSTAGRAM']['TOKEN'], $data['MANAGER']['INSTAGRAM']['IMGTOSHOW']));
        if(is_array($fileList)) {
            foreach($fileList as $key=>$file) {
                $fileManager->addFile($file['name'],$file['manualUpload']);
            }
        }
        // Save in main handler
        $this->product = $product;
        $this->manager = $manager;
        return (!$checksum) ? false : true;
    }
    
    // LOAD DATA BY PRODUCT ID (DATABASE)
    // --------------------------------------------------------------------
    function loadDataByID($PID) {
        global $DSDB;
        $PID = (int)$PID;
        if($PID<=0) {
            return false;
        }
        $resLoadProduct = $DSDB->query("
            SELECT p.id, p.order_nr, p.directory, p.product_type, p.last_update, pt.name, pt.fullname, pt.baseprice, p.wc_id
            FROM ".DS_TABLE_PRODUCT." AS p
            LEFT JOIN ".DS_TABLE_PRODUCT_TYPE." AS pt ON pt.id = p.product_type
            WHERE p.id='".$PID."' LIMIT 0,1
        ");
        if($resLoadProduct) {
            // Product is given
            if($resLoadProduct->num_rows>0) {
                // Load cached data
                if(!array_key_exists('loading', $_REQUEST)) {
                    if($this->loadData($PID)) {
                        return true;
                    }
                }
                // Setup new product data from database
                $data = $resLoadProduct->fetch_assoc();
                $resLoadProduct->close();
                // Check cached data
                if(!$data || !is_array($data) || @count($data)<=0) {
                    DS_LOGS::addEntry("DRUCKSTUDIO: Datenobjekt nicht vorhanden!");
                    return false;
                }
                // Check if product-type is valid
                $product = $this->getProductObject($data['product_type'], $data['wc_id']);
                if(!$product) {
                    return false;
                }
                // Product data
                $checksum = true;
                $checksum *= $product->setPID($PID);
                $checksum *= $product->setDir($data['directory']);
                $checksum *= $product->setDate($data['last_update']);
                // Read materials
                $materials = array();
                $resLoadMaterial = $DSDB->query("SELECT * FROM ".DS_TABLE_MATERIAL." WHERE product=".$PID);
                if($resLoadMaterial) {
                    while($row = $resLoadMaterial->fetch_assoc()) {
                        $MID = $row['id'];
                        $nr = $row['nr'];
                        $materials[$nr] = new DS_MATERIAL();
                        $materials[$nr]->setMID($MID);
                        $materials[$nr]->setNr($nr);
                        $materials[$nr]->setMaterial($row['filename'],$row['position'],$row['zoom'],$row['offset_x'],$row['offset_y'],$row['angle']);
                    }
                    $resLoadMaterial->close();
                }
                $checksum *= $product->setMaterials($materials);
                // Manager
                $manager = new DS_MANAGER();
                // File Manager
                $fileManager = $manager->getUploader();
                $checksum *= $fileManager->setPath( $product->getDir('uploader') );
                $fileList = array();
                if(is_array($product->getMaterials())) {
                    foreach($product->getMaterials() as $key=>$file) {
                        if(!in_array($file->getFile(),$fileList)){
                            $fileList[] = $file->getFile();
                            $fileManager->addFile($file->getFile());
                        }
                    }
                }
                // Save in main handler
                $this->product = $product;
                $this->manager = $manager;

                if($checksum) {
                    $this->saveData();
                    return true;
                } 
                else {
                    return false;
                }
            }
        }
        // No product found
        DS_LOGS::addEntry("PRODUCT: Produkt (".$PID.") nicht gefunden!");
        return false;
    }
    
    // SETUP NEW DATA BY PRODUCT-TYPE
    // --------------------------------------------------------------------
    function setup($prodType, $wcID) {
        if($prodType && $wcID) {
            $product = $this->getProductObject($prodType, $wcID);
            $manager = new DS_MANAGER();
            if($product) {
                // Set product dir
                $prodDir = $product->newDir();
                if($prodDir) {
                    // Set manager path
                    $manager->getUploader()->setPath( $product->getDir('uploader') );
                    // Set data manager
                    $this->product = $product;
                    $this->manager = $manager;
                    // Save data for caching 
                    if($this->saveData()) {
                        $_SESSION['DS_DATA']['CREATED'] = time();
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    function checkSetupSpam($prodType=null) {
        $data = $_SESSION['DS_DATA'];
        if($data['CREATED'] && ($prodType==$data['PRODUCT']['TYPE'] || !$prodType)) {
            $NOW = time();
            $TIMELIMIT = $data['CREATED'] + DS_SETUP_TIMELIMIT;
            $TIMELEFT = $TIMELIMIT-$NOW;
            if($TIMELEFT>=0) {
                DS_LOGS::addEntry("DRUCKSTUDIO: Verdacht auf Spam! Ein neues Produkt kann erst wieder in $TIMELEFT Sekunden erstellt werden.");
                return true;
            }
        }
        return false;
    } 
    
    // GET PRODUCT CLASS
    // --------------------------------------------------------------------
    function getProductObject($prodType, $wcID) {
        // Check product type
        if(!$prodType || !$wcID) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Kein Produkt-Typ bekannt!");
            return false;
        }
        $prodTypeObj = new DS_PRODUCT_TYPE();
        

        if(!$prodTypeObj->setTypeByID($prodType, $wcID)) {
            return false;
        }
        $prodClass = $prodTypeObj->getClass();

        if(!$prodClass) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Die Klasse des Produkt-Typs (".$prodClass.") ist nicht vorhanden!");
            return false;
        }
        
        if(!class_exists($prodClass)) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Die Klasse des Produkt-Typs (".$prodType.") ist unbekannt! - ");
            return false;
        }
        
        // Set up product object
        $product = new $prodClass();
        if(!$product->setType($prodTypeObj)) {
            return false;
        }
        return $product;
    }
    
    // SAVE DATA
    // --------------------------------------------------------------------
    function saveData() {
        if($this->error) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Zwischenspeicherung nicht m�glich! Fehler vorhanden!");
            return false;
        }
        if(!$this->product) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Zwischenspeicherung nicht m�glich! Produkt nicht vorhanden!");
            return false;
        }
        $CREATED = (int)$_SESSION['DS_DATA']['CREATED'];
        $CREATED = $CREATED>0 ? $CREATED : time();
        $productData = array(
            'PRODUCT' => $this->product->serialize(),
            'MANAGER' => $this->manager->serialize(),
            'CREATED' => $CREATED
        );
        $_SESSION['DS_DATA'] = $productData;
        return true;
    }
    
    // SAVE DATA IN DATABASE
    // --------------------------------------------------------------------
    function saveDataDB() {
        global $DSDB;
        if($this->error) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Zwischenspeicherung nicht m�glich! Fehler vorhanden!");
            return false;
        }
        if(!$this->product) {
            DS_LOGS::addEntry("DRUCKSTUDIO: Zwischenspeicherung nicht m�glich! Produkt nicht vorhanden!");
            return false;
        }
        // No Product in DB yet --> CREATE DB ENTRY
        $prodType = $this->product->getType();
        $PID = (int)$this->product->getPID();
        if($PID<=0) {
            if($this->product->checkProduct()) {
                // SAVING PROCESS
                $DSDB->autocommit(0);
                // 1. Add Product
                $addSID = session_id();
                $addUID = $this->getUserID();
                $addTime = date('Y-m-d H:i:s');
                $addOrderNr = $this->product->getOrderNr();
                $addDir = $this->product->getDir();
                $addProdType = $prodType->getID();
                $addWcId = $prodType->getWcID();
                $DSDB->query("
                    INSERT INTO ".DS_TABLE_PRODUCT." 
                    (session_id, user_id, last_update, order_nr, directory, product_type, wc_id) VALUES 
                    ('".$addSID."','".$addUID."','".$addTime."','".$addOrderNr."','".$addDir."','".$addProdType."','".$addWcId."')
                ");
                $PID = (int)$DSDB->insert_id;
                // 2. Add Materials
                $materials = $this->product->getMaterials();
                if(@count($materials)>0 && $PID>0) {
                    foreach($materials as $key=>$material) {
                        $addNr = $material->getNr();
                        $addFile = $material->getFile();
                        $addPos = $material->getPosition();
                        $addZoom = $material->getZoom();
                        $addAngle = $material->getAngle();
                        $offset = $material->getOffset();
                        $addOffsetX = $offset[0];
                        $addOffsetY = $offset[1];
                        $DSDB->query("
                            INSERT INTO ".DS_TABLE_MATERIAL." 
                            (product, nr, filename, position, zoom, angle, offset_x, offset_y, last_update) VALUES 
                            ('".$PID."','".$addNr."','".$addFile."','".$addPos."','".$addZoom."','".$addAngle."','".$addOffsetX."','".$addOffsetY."','".$addTime."')
                        ");
                        $MID = (int)$DSDB->insert_id;
                        // 3. Material Options
                        $options = $material->getOptions();
                        if(@count($options)>0 && $MID>0) {
                            foreach($options as $key=>$option) {
                                if(is_array($option)) {
                                    $addName = $option['name'];
                                    $addValue = $option['value'];
                                    if(preg_match('/^[a-zA-Z0-9]([_-]?[a-zA-Z0-9])+$/',$addName) && strlen($addName)<=50) {
                                        $addName = $DSDB->real_escape_string($addName);
                                        $addValue = $DSDB->real_escape_string($addValue);
                                        $DSDB->query("
                                            INSERT INTO ".DS_TABLE_MATERIAL_OPTIONS." 
                                            (material_id, option_name, option_value, last_update) VALUES 
                                            ('".$MID."','".$addName."','".$addValue."','".$addTime."')
                                        ");
                                    }
                                }
                            }
                        }
                    }
                }
                // Rollback if saving operation failed
                if(!$DSDB->commit()) {
                    DS_LOGS::addEntry("DRUCKSTUDIO: Produkt konnte nicht gespeichert werden! Fehler bei der Prozedur des Produkteintrags (".$PID.")!");
                    $DSDB->rollback();
                    return false;
                }
                $this->loadDataByID($PID);
                return true;
            }
            return false;
        }
        // Product already exists in DB -> OVERWRITE DATA
        elseif($this->productExists($PID)) {
            if($this->product->checkProduct()) {
                // SAVING PROCESS
                $DSDB->autocommit(0);
                // 1. Add Product
                $addSID = session_id();
                $addUID = $this->getUserID();
                $addTime = date('Y-m-d H:i:s');
                $addOrderNr = $this->product->getOrderNr();
                $addDir = $this->product->getDir();
                $addProdType = $prodType->getID();
                $DSDB->query("
                    UPDATE ".DS_TABLE_PRODUCT." SET
                      session_id='".$addSID."', user_id='".$addUID."', last_update='".$addTime."', 
                      order_nr='".$addOrderNr."', directory='".$addDir."', product_type='".$addProdType."'
                    WHERE id=".$PID."
                ");
                // 2. Add Materials
                $materials = $this->product->getMaterials();
                if(@count($materials)>0 && $PID>0) {
                    foreach($materials as $key=>$material) {
                        $MID = (int)$material->getMID();
                        $addNr = (int)$material->getNr();
                        $addFile = $material->getFile();
                        $addPos = $material->getPosition();
                        $addZoom = $material->getZoom();
                        $addAngle = $material->getAngle();
                        $offset = $material->getOffset();
                        $addOffsetX = $offset[0];
                        $addOffsetY = $offset[1];
                        // Add new / Change existing materials
                        $checkMaterial = $DSDB->query("SELECT id FROM ".DS_TABLE_MATERIAL." WHERE id=".$MID);
                        // Update existing material
                        if($checkMaterial->num_rows>0) {
                            $DSDB->query("
                                UPDATE ".DS_TABLE_MATERIAL." SET
                                  product='".$PID."', nr='".$addNr."', filename='".$addFile."', position='".$addPos."', zoom='".$addZoom."', angle='".$addAngle."', 
                                  offset_x='".$addOffsetX."', offset_y='".$addOffsetY."', last_update='".$addTime."'
                                WHERE id=".$MID."
                            ");
                        }
                        // Create new material
                        else {
                            $DSDB->query("
                                INSERT INTO ".DS_TABLE_MATERIAL." 
                                  (product, nr, filename, position, zoom, offset_x, offset_y, last_update) VALUES 
                                  ('".$PID."','".$addNr."','".$addFile."','".$addPos."','".$addZoom."','".$addOffsetX."','".$addOffsetY."','".$addTime."')
                            ");
                        }
                        // 3. Material Options
                        $options = $material->getOptions();
                        if(@count($options)>0 && $MID>0) {
                            foreach($options as $key=>$option) {
                                // Add new / Change existing material options
                                if(is_array($option)) {
                                    $addName = $option['name'];
                                    $addValue = $option['value'];
                                    if(preg_match('/^[a-zA-Z0-9]([_-]?[a-zA-Z0-9])+$/',$addName) && strlen($addName)<=50) {
                                        $addName = $DSDB->real_escape_string($addName);
                                        $addValue = $DSDB->real_escape_string($addValue);
                                        $checkOption = $DSDB->query("SELECT id FROM ".DS_TABLE_MATERIAL_OPTIONS." WHERE material_id=".$MID." AND option_name='".$addName."'");
                                        // Update existing material option
                                        if($checkOption->num_rows>0) {
                                            $DSDB->query("
                                                UPDATE ".DS_TABLE_MATERIAL_OPTIONS." SET
                                                  option_name='".$addName."', option_value='".$addValue."', last_update='".$addTime."' 
                                                WHERE material_id='".$MID."' AND option_name='".$addName."'
                                            ");
                                        }
                                        // Create new material option
                                        else {
                                            $DSDB->query("
                                                INSERT INTO ".DS_TABLE_MATERIAL_OPTIONS." 
                                                  (material_id, option_name, option_value, last_update) VALUES 
                                                  ('".$MID."','".$addName."','".$addValue."','".$addTime."')
                                            ");
                                        }
                                    }
                                }
                                // Delete outdated material options
                                $DSDB->query("DELETE FROM ".DS_TABLE_MATERIAL_OPTIONS." WHERE material_id=".$MID." AND last_update<'".$addTime."'");
                            }
                        }
                    }
                    // Delete outdated materials
                    $DSDB->query("DELETE FROM ".DS_TABLE_MATERIAL." WHERE product=".$PID." AND last_update<'".$addTime."'");
                }
                // Rollback if saving operation failed
                if(!$DSDB->commit()) {
                    DS_LOGS::addEntry("DRUCKSTUDIO: Produkt konnte nicht gespeichert werden! Fehler bei der Prozedur des Produkteintrags (".$PID.")!");
                    $DSDB->rollback();
                    return false;
                }
                return true;
            }
            return false;
        }
        // Product ID is set but does not exists --> ERROR
        else {
            DS_LOGS::addEntry("DRUCKSTUDIO: Produkt konnte nicht gespeichert werden! Produkteintrag (".$PID.") existiert nicht!");
            return false;
        }
        return true;
    }
    
    function productExists($PID=null) {
        global $DSDB;
        if(!$PID) {
            $PID = $this->product->getPID();
        }
        $PID = (int)$PID;
        $qry = $DSDB->query("SELECT id FROM ".DS_TABLE_PRODUCT." WHERE id=".$PID." LIMIT 0,1");
        if($qry) {
            return ($qry->num_rows>0);
        }
        else {
            return false;
        }
    }
    
    // REMOVE PRODUCT FROM DATABASE
    // --------------------------------------------------------------------
    function removeProduct($PID=null) {
        global $DSDB;
        if(!$PID) {
            $PID = (int)$this->product->getPID();
        }
        if($this->productExists($PID)) {
            $DSDB->autocommit(0);
            $DSDB->query("DELETE FROM ".DS_TABLE_MATERIAL_OPTIONS." WHERE material_id IN (SELECT id FROM ".DS_TABLE_MATERIAL." WHERE product=".$PID.")");
            $DSDB->query("DELETE FROM ".DS_TABLE_MATERIAL." WHERE product=".$PID);
            $DSDB->query("DELETE FROM ".DS_TABLE_PRODUCT." WHERE id=".$PID);
            if($DSDB->commit()) {
                $qry = $DSDB->query("SELECT id FROM ".DS_TABLE_PRODUCT." WHERE id=".$PID);
                if(!$qry || $qry->num_rows<=0) {
                    return true;
                }
            }
        }
        return false;
    }
    
    // SUBMIT ORDER
    // --------------------------------------------------------------------
    function submitOrder() {
        $this->error = 'SUBMIT_FAIL';
        return false;
    }
    
    // USER INFORMATION
    // --------------------------------------------------------------------
    function getUserID() {
        return 0;
    }
    
    // CHECK ACCESS
    // --------------------------------------------------------------------
    function checkAccess($PID=null) {
        if(!$PID) {
            $PID = $this->product->id;
        }
        // ACCESS LIMITATION
        // ...
        return true;
    }
    
    // GET DATA
    // --------------------------------------------------------------------
    function getProduct() {
        return $this->product;
    }
    function getManager() {
        return $this->manager;
    }
    function getError() {
        return $this->error;
    }
    
    // SHOW TEMPLATE OUTPUT
    // --------------------------------------------------------------------
    function show() {
        switch($this->error) {
            // Product-Order failed
            case 'SUBMIT_FAIL':
                $this->showSubmitError();
            break;
            // Product data and type not found
            case 'NO_PRODUCT':
                $this->showProdTypes();
            break;
            // Show main template
            default:
                $this->showProduct();
        }
        return true;
    }
    
    // SHOW PRODUCT
    // --------------------------------------------------------------------
    function showProduct() {
        $tempFile = $this->product->getTemplate()."index.php";
        if(is_file($tempFile)) {
            require_once($tempFile);
            return true;
        }
        DS_LOGS::addEntry("DRUCKSTUDIO: Template (".$tempFile.") nicht gefunden!");
        return false;
    }
    
    // SHOW LIST OF PRODUCTS (WHEN PRODUCT NOT FOUND)
    // --------------------------------------------------------------------
    function showProdTypes() {
        require_once("views/product-list.php");
    }
    
    // SHOW ERROR PAGE (WHEN ORDERING FAILS)
    // --------------------------------------------------------------------
    function showSubmitError() {
        require_once("views/checkout.php");
    }
    
    // SHOW ERROR PAGE (404 NOT FOUND)
    // --------------------------------------------------------------------
    function showNoPageError() {
        require_once("views/404.php");
    }
    
}

?>
