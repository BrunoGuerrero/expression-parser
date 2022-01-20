<?php
    class InterpreterException extends Exception {

        private $fallbackValue;

        public function __construct($message, $fallbackValue) {
            parent::__construct($message);
            $this->fallbackValue = $fallbackValue;
        }

        public function getFallbackValue() {
            return $this->fallbackValue;
        }

    }