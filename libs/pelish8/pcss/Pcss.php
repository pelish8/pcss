<?php
    
namespace pelish8\pcss;

class Pcss {
    const VERSION = '0.1';
    
    private $baseUrl = '';
    private $css = '';
    
    private $config = [
        'debug' => false,
        'baseUrl' => null,
        'cache.path' => null,
        'template.path' => null
    ];
    
    public function __construct(array $config) {
        
        $configuration = array_merge($this->config, $config);
        
        $this->config = &$configuration;
        try {
            $this->prepare($_SERVER['PATH_INFO']);
        } catch (Exception $e) {
            echo $e->getMessage(), "\n";
        }
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
        $path = $this->config['baseUrl'];
        return ($path === null) ? '' : $path  . $name;
    }
    
    private function loop($mainTag, $property) {
        
        $propertys = '';
        if (is_object($property)) {
            foreach ($property as $tag => &$value) {
                if (is_object($value)) {
                    $this->loop($mainTag . ' ' . $tag, $value);
                } else {
                    $propertys .= "    " . $tag . ': ' . $value . ";\n";
                }
            }
        }
        if ($propertys !== '') {
            $this->css .= $mainTag . " {\n" . $propertys . "}\n";
        }
    }
    
    private function minifyLoop($mainTag, $property) {
        
        $propertys = '';
        if (is_object($property)) {
            foreach ($property as $tag => &$value) {
                if (is_object($value)) {
                    $this->loop($mainTag . ' ' . $tag, $value);
                } else {
                    $propertys .= $tag . ': ' . $value . ';';
                }
            }
        }
        if ($propertys !== '') {
            $this->css .= $mainTag . '{' . $propertys . '}';
        }

    }
    
    private function includeFiles($files) {
        foreach ($files as &$file) {
               $ext = pathinfo($file)['extension'];
            if ($ext === 'pcss') {
                $this->prepare($file);
            } else {
                $content = @file_get_contents($this->fileName($file));
                if ($content !== false) {
                    $this->css .= $content . "\n";
                }
                
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
            case JSON_ERROR_DEPTH:
            case JSON_ERROR_STATE_MISMATCH:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_UTF8:
                throw new \Exception("Error Parsing JSON.", 1);
                break;
        }
        // implement all json parsing errors
        return null;
    }
    
    private function dump() {
        $this->setHeaders();
        echo $this->getCss();
    }
    
    public function getCss() {
        return $this->css;
    }
    
    public function setHeaders() {
        header("Content-type: text/css", true);
    }
    
}