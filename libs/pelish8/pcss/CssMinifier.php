<?php
namespace pelish8\pcss;

class CssMinifier {
    private $iterator;
    private $buffer = '';

    public function __construct($source) {
		$source = str_replace("\r\n", "\n", $source); // removeWindows line neding
		$source	= str_replace("\r", "\n", $source); // remove Mac line ending
        // $source    = str_replace("\n", " ", $source); // remove Unix line ending

        $this->iterator = new StringIterator($source);
        
        $this->parse();
    }
    
    private function parse() {
        $iterator = &$this->iterator;
        $buffer = &$this->buffer;
        
        while ($iterator->valid()) { // main loop
            $startOver = false;
            $current = $iterator->current();

            if ($current === "\n" || $current === "\t") {
                $current = ' ';
            }
            if ($current === '/' && $iterator->nextChar() === '*') {
                $this->parseComments();
                continue;
            } else if ($current === '{') {
                $this->parseProperty();
                continue;
            } else if ($current === ',') {
                $iterator->next();
                $this->removeWhitespace();
                $buffer .= ',';
                continue;
            } else if ($current === ' ' || $current === "\n") {
                $this->removeWhitespace();
                if (preg_match('/[a-zA-Z\.#]/', $iterator->nextChar())) {
                    $bufferLastChar = substr($buffer, strlen($buffer) - 1);
                    if ($bufferLastChar === '}' || !$bufferLastChar) {
                        $buffer .= '';
                    } else {
                        $buffer .= ' ';
                    }
                } else {
                    $buffer .= '';
                }
                continue;
            } else {
                $buffer .= $current;
            }
            
            $iterator->next();
        }
    }
    
    public function parseComments() {
        $iterator = &$this->iterator;
        $iterator->next();
        $iterator->next();
        
        while ($iterator->valid()) {
            $char = $iterator->current();
            if ($char === '*' && $iterator->nextChar() === '/') {
                $iterator->next();
                $iterator->next();

                return true;
            }
            
            $iterator->next();
        }
    }
    
    public function parseProperty() {
        $iterator = &$this->iterator;
        $buffer = &$this->buffer;

        while ($iterator->valid()) {
            $current = $iterator->current();
            if ($current === '}') {
                $bufferLastChar = substr($buffer, strlen($buffer) - 1);
                if ($bufferLastChar === ';') {
                    $buffer = rtrim($buffer, ';');
                }
                $buffer .= $current;
                break;
            } else if ($current === '/') {
                if ($this->parseComments()) {
                    continue;
                }
            } else if ($current === ':') {
                $this->handleColon();
            } else if ($current === ' ' || $current === "\n") {
                $this->removeWhitespace();
                continue;
            } else {
                $buffer .= $current;  
            }
            $iterator->next();
        }
    }
    
    public function handleColon() {
        $iterator = &$this->iterator;
        $buffer = &$this->buffer;
        $word = '';
        $processWord = false;
        
        while ($iterator->valid()) {
            if ($processWord) {
                // echo $word . '<br>';
                $buffer .= $word;
                $word = '';
            }
            $current = $iterator->current();
            if ($current === ';' || $current === '}') {
                $word = rtrim($word); // remove space, new line, tab before "}"
                $buffer .= $this->replaceWord($word) . $current;
                break;
            } else if ($current === ' ') {
                $this->removeWhitespace();
                if ($word[strlen($word) - 1] !== ':') {
                    $word = $this->replaceWord($word);
                    $word .= ' '; // space in css propetrty value : "border:solid 1px red"
                }
                $processWord = true;
                continue;
            } if ($current !== "'" && $current !== "'") {
                // $buffer .= $current;
                $word .= $current;
                $processWord = false;
            }
            $iterator->next();
        }
    }
    
    private function replaceWord($word) {
        $color = [
            "red" => "#f00",
            "white"	=> "#fff",
    ];
        
        if (isset($color[$word])) {
            return $color[$word];
        } else {
            return $word;
        }
    }
    
    public function removeWhitespace() {
        $iterator = &$this->iterator;

        while ($iterator->valid()) {
            $current = $iterator->current();
            if ($current === ' ' || $current === "\n") {
                $iterator->next();
            } else {
                break;
            }
        }
    }
    
    public function getMinified() {
        return $this->buffer;
    }
}

class StringIterator implements \Iterator {
     private $position = 0;
     private $string = null;
     
     public function __construct($string) {
         $this->position = 0;
         $this->string = $string;
     }
     
     public function current() {
         if ($this->valid()) {
             return $this->string[$this->position];
         } else {
             return null;
         }
     }
     
     public function key() {
         return $this->position;
     }
     
     public function next() {
         ++$this->position;
     }
     // return next char without changing position
     public function nextChar() {
         if (isset($this->string[$this->position + 1])) {
             return $this->string[$this->position + 1];
         } else {
             return null;
         }
     }
     
     // return prev char without changing position
     public function prevChar() {
         if (isset($this->string[$this->position - 1])) {
             return $this->string[$this->position - 1];
         } else {
             return null;
         }
     }
     
     public function rewind() {
         $this->position = 0;
     }
     
     public function valid() {
         return isset($this->string[$this->position]);
     }
}
