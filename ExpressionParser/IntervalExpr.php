<?php
    class IntervalExpr {

        public $min;
        public $max;

        public function __construct($min, $max) {
            $this->min = $min;
            $this->max = $max;
        }

        public function __toString() {
            return "[" . $this->min . "," . $this->max . "]";
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