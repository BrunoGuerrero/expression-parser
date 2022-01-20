<?php
    class CallExpr implements Expr {

        public $callee;
        public $arguments = [];
        //public $paren;

        public function __construct($callee, $paren, $arguments) {
            $this->callee = $callee;
            $this->arguments = $arguments;
            //$this->paren = $paren;
        }

        public function __toString() {
            return $this->callee 
                . "(" 
                . "" . implode(",", $this->arguments) . ")";
        }

        public function accept($visitor) {
            return $visitor->visitCallExpr($this);
        }
    }