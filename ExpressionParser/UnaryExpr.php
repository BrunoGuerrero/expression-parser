<?php
    class UnaryExpr implements Expr {

        public $operator;
        public $right;

        public function __construct($operator, $right) {
            $this->operator = $operator;
            $this->right = $right;
        }

        public function __toString() {
            return $this->operator->type . $this->right . "";
        }

        public function accept($visitor) {
            return $visitor->visitUnaryExpr($this);
        }
    }