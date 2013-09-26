<?php
    
namespace pelish8\pcss;

class Pcss {
    const VERSION = '0.1';
    
    private $baseUrl = '';
    private $css = '';
    
    public function __construct($config) {
        if (isset($config['baseUrl'])) {
            $this->baseUrl = $config['baseUrl'];
        }
        $this->prepare($_SERVER['PATH_INFO']);
        $this->dump();
    }
    
    private function prepare($name) {
        $fileUrl = $this->fileName($name);
        
        $content = @file_get_contents($fileUrl);
         if ($content !== false) {
             $jsonObject = $this->decodeJSON($content);
             $this->parse($jsonObject);
         } else {
             if (!empty($content)) {
                 throw new \Exception("Error File Not Found ($name)", 1);
             }
         }
    }
    
    private function parse($object) {
        // if file is empty object should be null
        if (!$object) {
            return;
        }
        foreach ($object as $tag => $property) {
            if (is_object($property)) {
                $this->loop($tag, $property);
            } else if (is_array($property)) {
                $this->includeFiles($property);
            } else {
                throw new \Exception("Error Parsing " . $this->fileName($_SERVER['PATH_INFO']), 1);
            }
        }
    }
    
    private function fileName($name) {
        return $this->baseUrl . $name;
    }
    
    private function loop($mainTag, $property) {
        $content = $mainTag . " {\n";
        if (is_object($property)) {
            foreach ($property as $tag => &$value) {
                if (is_object($value)) {
                    $this->loop($mainTag . ' ' . $tag, $value);
                } else {
                    $content .= "    " . $tag . ': ' . $value . ";\n" ;
                }
            }
        }
        $content .= "}\n";
        
        $this->css .= $content;
    }
    
    private function includeFiles($files) {
        foreach ($files as &$file) {
               $ext = pathinfo($file)['extension'];
            if ($ext === 'css') {
                $this->css .= "\n" . @file_get_contents($this->fileName($file)) . "\n";
            } else {
                $this->prepare($file);
            }
        }
    }
    
    private function decodeJSON($string) {
        $object = json_decode($string);
        $error = json_last_error();
        switch ($error) {
            case JSON_ERROR_NONE:
                return $object;
                break;
        }
        // implement all json parsing errors
        return null;
    }
    
    private function dump() {
        header("Content-type: text/css", true);
        // echo '<pre>';
        echo $this->css;
    }
    
}