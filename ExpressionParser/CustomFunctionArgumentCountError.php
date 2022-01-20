<?php
    class CustomFunctionArgumentCountError extends ArgumentCountError {

        public function __construct($functionName, $expected, $given) {
            $this->message = "Interpreted function '" . $functionName  . "()' expects " . $expected . " arguments, " . $given . " given.";
        }

    }