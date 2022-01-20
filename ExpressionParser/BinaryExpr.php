<?php
    class BinaryExpr implements Expr {

        public $left;
        public $operator;
        public $right;

        public function __construct($left, $operator, $right) {
            $this->left = $left;
            $this->operator = $operator;
            $this->right = $right;
        }

        public function __toString() {
            return $this->left . $this->operator->type . $this->right . "";
        }

        public function accept($visitor) {
            return $visitor->visitBinaryExpr($this);
        }
    }