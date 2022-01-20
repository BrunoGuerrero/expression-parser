<?php
    interface Expr {
        public function __toString();
        public function accept($visitor);
    }