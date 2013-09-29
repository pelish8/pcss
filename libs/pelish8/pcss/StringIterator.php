<?php

namespace pelish8\pcss;

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