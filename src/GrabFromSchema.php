<?php

namespace smrtg\VeryGrabber;


class GrabFromSchema
{
    function __construct($doc)
    {
        $this->doc = $doc;
        $dom = new \DOMDocument();
        @$dom->loadHtml($this->doc);
        $this->dom = $dom;
        $xpath = new \DOMXPath($dom);
        $this->xpath = $xpath;
        global $do_debug;
        if($do_debug) {
            $this->do_debug = true;
        } else {
            $this->do_debug = false;
        }
    }

    function getStruct($schema)
    {
        try{
            if(is_string($schema)) {
                $sc = json_decode($schema);
            } else {
                $sc = $schema;
            }
            return $this->getStruFromSchema($sc);
        } catch(\Exception $e) {
            $str = $e->getTraceAsString();
            print $str;
        }
    }

    function getStruFromSchema($sc,$context = null)
    {
        if(!$sc) {
            throw new \Exception();
        }
        //print_r($sc);
        switch($sc->type) {
        case 'array':
            $data = $this->repeatOn($sc->repeatOn, $context, $sc->elementDef);
            if(count($data) == 0 && $this->do_debug) {
                $this->prettyPrint($sc, $context);
            }
            return $data;
            break;
        case 'object':
            if(!isset($sc->attrs)) {
                print_r($sc);
                exit();
            }
            $data = $this->extractAttrs($sc->attrs, $context);
            return $data;
            break;
        case 'string':
            $data = $this->extractOneAttr($sc, $context);
            return $data;
            break;
        }
    }

    function repeatOn($path, $context, $elementDef)
    {
        $xpath = $this->xpath;
        $nodes = $this->xpathQuery($path,$context);
        $elements = array();
        foreach($nodes as $node) {
            $elements[] = $this->getStruFromSchema($elementDef,$node);
        }
        return $elements;
    }

    function isSetted($obj, $attributes =array())
    {
        $ok = true;
        foreach($attributes as $attr) {
            if (! isset($obj->{$attr})) {
                $ok = false;
            }
        }
        return $ok;
    }

    function extractAttrs($attrs, $context)
    {
        $obj = new \StdClass();
        foreach($attrs as $attr) {
            // must be setted: name, elementDef
            $requiredEl = ['name','elementDef'];
            if(! $this->isSetted($attr,$requiredEl) ) {
                print "WRONG SCHEMA DEFINITION. Attribute required:\n\n";
                print implode(', ',$requiredEl)."\n\nCheck:\n ";
                print_r($attr);
                print "in (outer struct):\n";
                print_r($attrs);
                exit();
            }
            $name = $attr->name;
            $obj->{$name} = $this->getStruFromSchema($attr->elementDef,$context);
        }
        return $obj;
    }

    function calcPathHasClass($attr)
    {
        $path = ".//*[contains(@class,'".$attr->hasClass."')]";
        if(isset($attr->elAttribute)) {
            $path .="/@".$attr->elAttribute;
        }
        return $path;
    }
    
    function extractOneAttr($attr, $context)
    {
        if(isset($attr->hasClass)) {
            $path = $this->calcPathHasClass($attr);
        } else {
            $path = $attr->relXpath;
        }
        $nodes = $this->xpathQuery($path,$context);
        $c = '';
        foreach($nodes as $node) {
            $c = $this->DOMinnerHTML($node);
            if(isset($attr->pregMatch)) {
                if(preg_match($attr->pregMatch,$c,$matches)) {
                    $c = $matches[1];
                }
            } else if(isset($attr->wrapFun)) {
                $fun = $attr->wrapFun;
                $c = $fun($c);
            }
        }
        
        return $c;
    }

    function DOMinnerHTML($element) 
    { 
        $innerHTML = ""; 
        $children  = $element->childNodes;

        foreach ($children as $child) 
        { 
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML; 
    } 

    private function xpathQuery($path,$node=null)
    {
        if($node != null) {
            #print "PATH: $path\n";
            return $this->xpath->query($path, $node);
        } else {
            return $this->xpath->query($path);
        }
    }

    function prettyPrint($sc, $context)
    {
        $html = $this->DOMinnerHTML($context);
        print $this->makePretty($html);
        print "\nNot found anything in this code. Searching for\n";
        foreach($sc as $k => $v) {
            if(is_string($v)) {
                print "$k :\t\"$v\"\n";
            }
        }
        print "\n";
        $boh = readline("Would you check?");
        print "you replied $boh\n";
    }
    
    function makePretty($html)
    {
        $config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 200);

        // Tidy
        $tidy = new \tidy;
        $tidy->parseString($html, $config, 'utf8');
        //$tidy->cleanRepair();

        // Output
        return $tidy;

    }

}