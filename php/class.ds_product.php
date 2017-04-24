<?php

class DS_PRODUCT {
    
    // EIGENSCHAFTEN
    // --------------------------------------------------------------------
    private $PID;
    private $orderNr;
    private $userID;
    private $type;
    private $dir;
    private $materials;
    private $cdate;
    
    
    // TEMPLATE
    // --------------------------------------------------------------------
    private $templateDir    = "views/products/squares/";
    private $sizeThumb      = array(600,600);
    private $legalFileTypes = array('jpg','jpeg','png');
    
    
    // KONSTRUKTOR
    // --------------------------------------------------------------------
    
    function __construct($PID=null) {
        $this->PID = 0;
        $this->orderNr = 0;
        $this->type = new DS_PRODUCT_TYPE();
        $this->dir = "";
        $this->materials = array();
        $this->cdate = "";
        // Load Product
        if($PID !== null) {
            $this->getProductByID($PID);
        }
    }
    
    
    // LOAD PRODUCT FROM DB
    // --------------------------------------------------------------------
    
    function getProductByID($PID) {
        return false;
    }

    function getWcID() {
        return $this->type->getWcID();
    }
    
    
    // GET DATA FROM PRODUCT
    // --------------------------------------------------------------------
    
    function getPID() {
        return $this->PID;
    }

    function getOrderNr() {
        return $this->orderNr;
    }
    
    function getUserID() {
        return $this->userID;
    }
    
    function getType() {
        return $this->type;
    }
    
    function getDir($type=null) {
        if($this->dir) {
            switch($type) {
                // ORIGINAL IMAGE PATH
                case 'original':
                    return DS_PATH_UPLOAD.$this->dir.'/'.DS_UPLOAD_ORIGINAL;
                // GALLERY THUMBNAIL PATH
                case 'thumb':
                    return DS_PATH_UPLOAD.$this->dir.'/'.DS_UPLOAD_THUMB;
                // UPLOADER THUMBNAIL PATH
                case 'uploader':
                    return DS_PATH_UPLOAD.$this->dir.'/'.DS_UPLOAD_TMP;
                // PRINT IMAGE PATH
                case 'print':
                    return DS_PATH_UPLOAD.$this->dir.'/'.DS_UPLOAD_PRINT;
                // SHOW DIRECTORY NAME
                case 'full':
                    return DS_PATH_UPLOAD.$this->dir;
                // SHOW DIRECTORY NAME
                default:
                    return $this->dir;
            }
        }
        else {
            return false;
        }
    }
    
    function getDate() {
        return $this->cdate;
    }
    
    function getMaterials($MID=null) {
        if($MID !== null && is_numeric($MID)) {
            if($this->materials[$MID]) {
                return $this->materials[$MID];
            } else {
                DS_LOGS::addEntry("PRODUKT: Material mit angegebener ID (".$MID.") ist nicht vorhanden!");
                return false;
            }
        } 
        else {
            return $this->materials;
        }
    }
    
    function getMaterialCount() {
        return @count($this->materials);
    }
    
    function getThumb($MID) {
        $material = $this->getMaterials($MID);
        if(is_a($material,"DS_MATERIAL")) {
            $nr = (int)$material->getNr();
            $ext = pathinfo($material->getFile(),PATHINFO_EXTENSION);
            $fileName = str_pad($nr,5,'0',STR_PAD_LEFT);
            $filePath = $this->getDir('thumb').$fileName.".".$ext;
            return $filePath;
        }
        else {
            return false;
        }
    }

    function getOriginal($MID) {
        $material = $this->getMaterials($MID);
        if(is_a($material,"DS_MATERIAL")) {
            $fileName = $material->getFile();
            $filePath = $this->getDir('original').$fileName;
            return $filePath;
        }
        else {
            return false;
        }
    }
    
    function getNextPosition() {
        $max = 0;
        if(@count($this->materials) > 0) {
            foreach($this->materials as $material) {
                $pos = $material->getPosition();
                if($max < $pos) {
                    $max = $pos;
                }
            }
            return ($max + 1);
        }
        return $max;
    }
    
    function getTemplate() {
        return $this->templateDir;
    }
    
    function getLegalFileTypes() {
        return $this->legalFileTypes;
    }
    
    function isLegalFileType($filename) {
        $ext = pathinfo($filename,PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if(in_array($ext,$this->legalFileTypes)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    function countFiles($file) {
        $counter = 0;
        if($file) {
            $fileName = basename($file);
            if(file_exists($this->getDir('original').$fileName)) {
                // Check in materials
                foreach($this->materials as $material) {
                    $iFileName = basename($material->getFile());
                    $iNr = $material->getNr();
                    if($fileName == $iFileName) {
                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }
    
    function serialize() {
        $tmpMaterials = array();
        $materials = $this->materials;
        if(@count($materials)>0) {
            foreach($materials as $key=>$material) {
                if(is_a($material,'DS_MATERIAL')) {
                    $tmpMaterials[$key] = $material->serialize();
                }
                elseif(is_array($material)) {
                    $tmpMaterials[$key] = $material;
                }
            }
        }
        $output = array(
            'PID' => $this->PID,
            'TYPE' => $this->type->getID(),
            'WCID' => $this->type->getWcID(),
            'DIR' => $this->dir,
            'DATE' => $this->cdate,
            'MATERIALS' => $tmpMaterials
        );
        return $output;
    }
    
    // Check Product for saving
    function checkProduct() {
        $error = array();
        // Check product
        $prodType = $this->type;
        $settings = $prodType->getSettings();
        
        // Check Materials
        $materialCount = $this->getMaterialCount();
        // Check product type settings
        if($materialCount < $settings['quantity-min']) {
            $error[] = "zu wenig Fotos angegeben!";
        }
        if($materialCount > $settings['quantity-max']) {
            $error[] = "zu viele Fotos angegeben!";
        }
        // Check materials
        $materialSizeX = $settings['size-x'];
        $materialSizeY = $settings['size-y'];
        $materialRes = $settings['dpi'];
        $materials = $this->materials;
        if(@count($materials)>0) {
            foreach($materials as $key=>$material) {
                $pos = $material->getPosition()+1;
                $materialFile = $material->getFile();
                // Check filetype
                if(!$this->isLegalFileType($materialFile)) {
                    $error[] = "Das Format des Materials (Nr $pos) ist nicht erlaubt!";
                }
            }
        }
        
        if(@count($error)>0) {
            $DS_LOGS = new DS_LOGS();
            $DS_LOGS->loadRequestLogs();
            $DS_LOGS->addLog(array('ProductCheck' => $error));
            $DS_LOGS->saveLog();
            return false;
        }
        else {
            return true;
        }
    }
    
    
    // SET DATA OF PRODUCT
    // --------------------------------------------------------------------
    
    function setPID($PID) {
        $this->PID = (int)$PID;
        return true;
    }
    
    function setOrderNr($orderNr) {
        $this->orderNr = (int)$orderNr;
        return true;
    }
    
    function setType($prodType) {
        if(is_a($prodType,"DS_PRODUCT_TYPE")) {
            $this->type = $prodType;
            return true;
        }
        return false;
    }
    
    function setDir($dir) {
        $this->dir = $dir;
        return true;
    }
    
    function setDate($date) {
        $this->cdate = $date;
        return true;
    }
    
    function setMaterials($materials) {
        $this->materials = $materials;
        return true;
    }
    
    function addMaterial($material) {
        if(is_a($material,"DS_MATERIAL")) {
            if($material->getPosition() == 0) {
                $material->setPosition( $this->getNextPosition() );
            }
            $this->materials[] = $material;
            end($this->materials);
            $nr = key($this->materials);
            $this->materials[$nr]->setNr($nr);
            return $nr;
        }
        else {
            return false;
        }
    }
    
    function removeMaterial($nr) {
        $material = $this->materials[$nr];
        if(is_a($material,"DS_MATERIAL")) {
            // Remove thumb image
            $thumbImage = $this->getThumb($nr);
            if(@file_exists($thumbImage)) {
//                unlink($thumbImage);
            }
            // Remove material data
            $pos = $material->getPosition();
            unset($this->materials[$nr]);
            foreach($this->materials as $iMaterial) {
                $iPos = $iMaterial->getPosition();
                if($iPos > $pos && $iPos > 0) {
                    $iMaterial->setPosition($iPos-1);
                }
            }
            return true;
        }
        else {
            return false;
        }
    }
    
    
    // DIRECTORY FOR FILES
    // --------------------------------------------------------------------
    
    function newDir() {
        // Dont create new directory if one already exists
        if(@file_exists( $this->getDir('full') ) && !empty($this->dir)) {
            return $this->dir;
        }
        // Get next directory
        $path = $_SERVER["DOCUMENT_ROOT"].DS_PATH_BASE.DS_PATH_UPLOAD;             
        if(!$path) {
            DS_LOGS::addEntry("PRODUCT: Der Pfad zum Uploadbereich (".$path.") ist nicht korrekt!");
            return false;
        }
        $dir = scandir($path);
        $dir = preg_grep("/^[0-9]+$/",$dir);
        $max = (@count($dir)>0) ? max($dir) + 1 : 1;
        if($max<=1) $max = 1;
        $dirName = str_pad($max,10,'0',STR_PAD_LEFT);
        // Create main directory
        $mainDir = $path.$dirName;
        if(!mkdir($mainDir)) {
            DS_LOGS::addEntry("PRODUCT: Es konnte kein Verzeichnis angelegt werden!");
            return false;
        }
        // Create sub directories
        if(!mkdir($mainDir.'/'.DS_UPLOAD_ORIGINAL) || !mkdir($mainDir.'/'.DS_UPLOAD_THUMB) || !mkdir($mainDir.'/'.DS_UPLOAD_TMP) || !mkdir($mainDir.'/'.DS_UPLOAD_PRINT)) {
            DS_LOGS::addEntry("PRODUCT: Es konnten keine Unterverzeichnise vom Hauptverzeichnis (".$dirName.") angelegt werden!");
            if(!rmdir($mainDir)) {
                DS_LOGS::addEntry("PRODUCT: Das Hauptverzeichnis (".$dirName.") konnte nach vorliegendem Fehler nicht gel�scht werden!");
            }
            return false;
        }
        $this->dir = $dirName;
        return $dirName;
    }
    
    
    // MANAGE MATERIALS (IMAGES)
    // --------------------------------------------------------------------
    
    function createThumbs($MID=null) {
        $return = true;
        $materials = $this->getMaterials($MID);
        if($materials) {
            if(!is_array($materials)) {
                $materials = array($materials);
            }
            foreach($materials as $material) {
                // CREATE MATERIAL THUMB
                $nr = $material->getNr();
                $materialFile = $material->getFile();
                $ext = pathinfo($materialFile,PATHINFO_EXTENSION);
                $originalFile = $this->getDir('original').$materialFile;
                $thumbFile = $this->getDir('thumb').str_pad($nr,5,'0',STR_PAD_LEFT).".".$ext;
                $offset = $material->getOffset();
                $zoom = $material->getZoom();
                $angle = $material->getAngle();

                $type = $material->getType();
                if(!imageResize($originalFile,$thumbFile,$this->sizeThumb[0],$this->sizeThumb[1],$offset[0],$offset[1],$zoom, 100, 0,$angle)) {
                    DS_LOGS::addEntry("PRODUKT: Material ($nr) konnte nicht als Vorschaubild gespeichert werden!");
                    $return = false;
                }
            }
            return $return;
        } else {
            DS_LOGS::addEntry("PRODUKT: Keine g�ltigen Materialien zur Erstellung der Vorschaubilder vorhanden!");
            return false;
        }
    }
    
    // Create material images for print output
    function createPrintImages($MID=null) {
        // Main settings
        $dir = $this->getDir('print');
        $type = $this->type;
        $settings = $type->getSettings();
        $setting_dpi = $settings['dpi'];
        $setting_x = $settings['size-x'];
        $setting_y = $settings['size-y'];
        $productDate = strtotime($this->cdate);
        // Image looping
        $return = true;
        $materials = $this->getMaterials($MID);
        if($materials) {
            if(!is_array($materials)) {
                $materials = array($materials);
            }
            foreach($materials as $material) {
                // CREATE MATERIAL PRINT IMAGE
                $nr = $material->getNr();
                $materialFile = $material->getFile();
                $ext = pathinfo($materialFile,PATHINFO_EXTENSION);
                $originalFile = $this->getDir('original').$materialFile;
                $fileName = str_pad($nr,5,'0',STR_PAD_LEFT);
                $printFile = $this->getDir('print').$fileName.".".$ext;
                $thumbFile = $this->getDir('thumb').$fileName.".".$ext;
                $offset = $material->getOffset();
                $zoom = $material->getZoom();
                $angle = $material->getAngle();
                
                // Check if image is up to date
                if(@is_file($printFile)) {
                    $thumbDate = filemtime($thumbFile);
                    $printDate = filemtime($printFile);
                    if($printDate >= $thumbDate) {
                        continue;
                    }
                }
                // Create image
                if(!imageResize($originalFile,$printFile,$setting_x,$setting_y,$offset[0],$offset[1],$zoom,$setting_dpi, 0, $angle)) {
                    DS_LOGS::addEntry("PRODUKT: Material ($nr) konnte nicht als Druckversion gespeichert werden!");
                    $return = false;
                }
            }
            return $return;
        } else {
            DS_LOGS::addEntry("PRODUKT: Keine g�ltigen Materialien zur Erstellung der Druckdateien!");
            return false;
        }
    }
    
    
    // CREATE PRINT DOCUMENT OF PRODUCT
    // --------------------------------------------------------------------
    
    function showPrint() {
        // Create print-version of images
        if($this->createPrintImages()) {
            // Doc settings
            $pdf = new FPDF('P','mm', 'A4');
            $pdf->SetAutoPageBreak(false);
            // Material pages
            $materials = $this->getMaterials();

            $pdf->AddPage('P', array(45,174));
            //first coordinate
            $coordsX = 2;
            $imgIndent = 2;
            $imgWdth = 41;
            $imgCount = 0;
            foreach($materials as $material) {
                $imgCount++;
                if ($imgCount > 4){
                        //next page
                        $pdf->AddPage('P', array(45,174));
                        $coordsX = 2;
                        $imgCount = 1;
            
                }
                $materialFile = $material->getFile();
                $ext = pathinfo($materialFile,PATHINFO_EXTENSION);
                $nr = $material->getNr();
                $fileName = str_pad($nr,5,'0',STR_PAD_LEFT);
                $materialFile = $this->getDir('print').$fileName.".".$ext;

                $pdf->Image($materialFile,$imgIndent,$coordsX,$imgWdth,$ext);
                $coordsX = $coordsX+$imgIndent+$imgWdth;
                
            }
            // Infopage

            
            $productName = $this->type->getFullname();
            $materialCount = $this->getMaterialCount();
            $productName .= " ($materialCount)";
            $orderNr = ($this->orderNr) ? $this->orderNr : $this->PID;
            $orderNr = str_pad($orderNr,10,'0',STR_PAD_LEFT);
            $orderDate = date("d.m.Y - H:i",strtotime($this->cdate))." Uhr";
            $orderDateDoc = date("Y_m_d",strtotime($this->cdate));
            $userID = ($this->userID) ? (int)$this->userID : 0;
            $userID = str_pad($userID,6,'0',STR_PAD_LEFT);
            $prodTypeNr = $this->type->getID();
            $prodTypeNr = str_pad($prodTypeNr,2,'0',STR_PAD_LEFT);

            // TODO: Get the customer data from the database
            
            $customerName = utf8_decode($_GET['customer_name']);
            $order_id = $_GET['order_id'];

            $pdf->addPage('L', array(30,90));
            $pdf->SetFont('Arial','B',9);
            $pdf->setXY(2,0);
            $pdf->Cell(65,15,$productName,0,1,'L',false);
            $pdf->setXY(2,5);
            $pdf->Cell(65,15,"Datum: $orderDate",0,1,'L',false);
            $pdf->setXY(2,10);
            $pdf->Cell(65,15,"Bestellung: $order_id",0,1,'L',false);
            $pdf->setXY(2,15);
            $pdf->Cell(65,15,"Kunde: $customerName",0,0,'L',false);
            // Output
            $docName = "DS-".$orderNr."-".$userID."-".$prodTypeNr.".pdf";

            $pdf->Output($docName,'I');

            return true;
            
        }
        return false;
    }
    
}
        
?>
