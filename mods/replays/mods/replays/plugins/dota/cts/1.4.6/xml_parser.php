<?php
/******************************************************************************
Last revision:
- Author: Seven
- Email: zabkar@gmail.com  (Subject DotaParser)
- Date: 6.10.2009 (1.2) 
******************************************************************************/  
  
  /**
  * @desc XML Paser of the Replay Data
  * @author Seven
  * @time 15.2.2009
  */
  class xml_data {
    private $xmlparser;
    
    public $Heroes;
    public $Skills;
    public $Items;
    public $Info;   // Stores general Map info like version, etc. 
    
    public $HashMap;   // Links ID's to the appropriate Object.
    public $SkillToHeroMap; // Links Skills to Heroes
    
    
    /* Parser related */
    private $current; // Tag being currently parsed
    private $prev;   // Previously parsed tag
    private $item;  // Array of "Item" data tags
    private $inItem; // Currently parsing an "Item"
    
    
    /**
    * @desc Parse an XML file
    * @param String file name
    */
    public function parse_file($filename) {
     if (!($fp = fopen($filename, "r"))) { die("cannot open ".$filename); }
        
        $datas = "";
        while ($data = fread($fp, 4096)){
           // Remove all blankspaces 
           $datas .= $data;
        }
        // $datas = eregi_replace(">"."[[:space:]]+"."<","><",$datas);   
           // Parsing
           if (!xml_parse($this->xmlparser, $datas, feof($fp))) {  
              $reason = xml_error_string(xml_get_error_code($this->xmlparser));
              $reason .= xml_get_current_line_number($this->xmlparser);
              die($reason);
           }
        
        xml_parser_free($this->xmlparser);    
    }
    
    public function xml_data() {
        $this->current = ""; 
        $this->prev = "";
        $this->inItem = false;
        $this->HashMap = array(); 
        
        if (! ($this->xmlparser = xml_parser_create()) )
        { 
            die ("Cannot create parser");
        }
        xml_set_object ( $this->xmlparser, $this );
        xml_set_element_handler($this->xmlparser, "start_tag", "end_tag");
        xml_set_character_data_handler($this->xmlparser, "tag_contents");
    }
    
    /**
    * @desc Data handling
    * @param handle to our parser
    * @param data 
    */
    function tag_contents($parser, $data) {
        $data = trim($data);
        
        if ( $data == "" ) return;
         
        if($this->inItem) {
            $this->item[$this->current] = $data;
        }
        else {
            $this->Info[$this->current] = $data;    
        }
    }
    
    /**
    * @desc Start tag parsing
    * @param handle to our parser
    * @param name of the current tag
    * @param an array containing any attributes of the current tag
    */
    function start_tag($parser,  $name, $attribs) {     
        $this->current = $name;
                
        switch ($name) {
            // Reset the current item's data, start building it
            case 'ITEM':
                $this->item = array();
                $this->inItem = true;
                break;
            // Map info
            default:
                break;
        }
        
        if (!empty($attribs) && is_array($attribs)) {
            echo "Attributes : <br />";
            while(list($key,$val) = each($attribs)) {
                echo "Attribute ".$key." has value ".$val."<br />";
             }
        }
    }    
    
    /**
    * @desc End tag parsing
    * @param handle to our parser
    * @param name of the current tag
    */    
    function end_tag($parser, $name) {
        $this->prev = $name;
        
        
        
        if($name == "ITEM" && isset($this->item['TYPE'])) {        
            $this->inItem = false;   
            switch ($this->item['TYPE']) {
                
                case 'HERO':                 
                    $tmp = new Hero($this->item['NAME'],
                                    (isset($this->item['ART']) ? $this->item['ART'] : ""), 
                                    (isset($this->item['COMMENT']) ? $this->item['COMMENT'] : ""),  
                                    (isset($this->item['COST']) ? $this->item['COST'] : ""),
                                    $this->item['ID'], 
                                    (isset($this->item['PROPERNAMES']) ? $this->item['PROPERNAMES'] : ""),
                                    (isset($this->item['RELATEDTO']) ? $this->item['RELATEDTO'] : ""),
                                    $this->item['TYPE']);
                   
                    
                    $split = $tmp->parseRelated();
                    foreach($split as $skill) {
                        $this->SkillToHeroMap[$skill] = $this->item['ID'];   
                    }
                    break;
                case 'ITEM':
                    $tmp = new Item($this->item['NAME'],
                                    (isset($this->item['ART']) ? $this->item['ART'] : ""), 
                                    (isset($this->item['COMMENT']) ? $this->item['COMMENT'] : ""),  
                                    (isset($this->item['COST']) ? $this->item['COST'] : ""),
                                    $this->item['ID'], 
                                    (isset($this->item['PROPERNAMES']) ? $this->item['PROPERNAMES'] : ""),
                                    (isset($this->item['RELATEDTO']) ? $this->item['RELATEDTO'] : ""),
                                    $this->item['TYPE']); 
                    break;
                case 'ULTIMATE':
                case 'SKILL':
                case 'STAT':
                    $tmp = new Skill($this->item['NAME'],
                                    (isset($this->item['ART']) ? $this->item['ART'] : ""), 
                                    (isset($this->item['COMMENT']) ? $this->item['COMMENT'] : ""),  
                                    (isset($this->item['COST']) ? $this->item['COST'] : ""),
                                    $this->item['ID'], 
                                    (isset($this->item['PROPERNAMES']) ? $this->item['PROPERNAMES'] : ""),
                                    (isset($this->item['RELATEDTO']) ? $this->item['RELATEDTO'] : ""),
                                    $this->item['TYPE']);
                    break;
            }
            
            // Add the Object to the HashMap
            if(isset($tmp)) {
                $this->HashMap[$tmp->getId()] = $tmp;    
            }
        }
    }
  
  }
  /**
  * @desc Superclass for Items, Heroes, Skills..
  */
class Entity {
    private $Name;
    private $Art;
    private $Comment;
    private $Cost;
    private $Id;
    private $Type;
    private $ProperNames;
    private $RelatedTo;
    
    public $extra;
    
    public function Entity($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type) {
        $this->Name = $Name;
        $this->Art = $Art;
        $this->Cost = $Cost;
        $this->Id = $Id;
        $this->ProperNames = $ProperNames;
        $this->RelatedTo = $RelatedTo;
        $this->Type = $Type;    
    }
    
    public function getName() {
        return $this->Name;
    }
    
    public function getArt() {
        return $this->Art;
    }
    
    public function getComment() {
        return $this->Comment;
    }
    
    public function getCost() {
        return $this->Cost;
    }
    
    public function getId() {
        return $this->Id;
    }
    
    public function getProperNames() {
        return $this->ProperNames;
    }
    
    public function getRelatedTo() {
        return $this->RelatedTo;          
    }
    
    public function getEntityType() {
        return $this->Type;
    } 
      
  }
  
  
  /**
  * @desc Class for Hero type
  */
  class Hero extends Entity {
    public function Hero($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type) {
        $this->Entity($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type);    
    }
    
    public function parseRelated() {
        return explode(",", $this->getRelatedTo()); 
    } 
  }
  
  /**
  * @desc Class for Skill and Ultimates
  */
  class Skill extends Entity {
    public function Skill($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type) {
        $this->Entity($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type);    
    } 
  }
  
  /**
  * @desc Class for Items
  */
  class Item extends Entity {
    public function Item($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type) {
        $this->Entity($Name, $Art, $Comment, $Cost, $Id, $ProperNames, $RelatedTo, $Type);    
    } 
  }
  
?>