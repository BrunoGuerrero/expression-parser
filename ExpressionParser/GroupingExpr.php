<?php
    class GroupingExpr implements Expr {

        public $expression;

        public function __construct($expression) {
            $this->expression = $expression;
        }

        public function __toString() {
            return "(" . $this->expression . ")";
        }

        public function accept($visitor) {
            return $visitor->visitGroupingExpr($this);
        }

    }