<?php
    class IntervalExpr {

        public $min;
        public $max;
        public $precision;

        public function __construct($min, $max, $precision) {
            $this->min = $min;
            $this->max = $max;
            $this->precision = $precision;
        }

        public function __toString() {
            return "[" . $this->min . "," . $this->max . "(" . $this->precision . ")]";
        }

        public function accept($visitor) {
            try {
                return $visitor->visitIntervalExpr($this);
            } catch (Exception $e) {
                throw $e;
            }
        }

    }
?>