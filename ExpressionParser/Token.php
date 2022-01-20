<?php
    class Token {
        public $type;
        public $lexeme;
        public $literal;
        public $line; 

        public function __construct($type, $lexeme, $literal, $line) {
            $this->type = $type;
            $this->lexeme = $lexeme;
            $this->literal = $literal;
            $this->line = $line;
        }

        public function __toString() {
            return "T[" . $this->type 
                . "•'" . $this->lexeme 
                . "'•'" . $this->literal . "']";
        }
    }