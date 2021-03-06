<?php
    class LiteralExpr implements Expr {

        public $value;

        public function __construct($value) {
            $this->value = $value;
        }

        public function __toString() {
            return $this->value . "";
        }

        public function accept($visitor) {
            return $visitor->visitLiteralExpr($this);
        }
    }