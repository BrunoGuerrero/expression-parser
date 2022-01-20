<?php
    class VariableExpr implements Expr {

        public $name;

        public function __construct($name) {
            $this->name = $name;
        }

        public function __toString() {
            return $this->name->lexeme;
        }

        public function accept($visitor) {
            try {
                return $visitor->visitVariableExpr($this);
            } catch (Exception $e) {
                throw $e;
            }
        }

    }