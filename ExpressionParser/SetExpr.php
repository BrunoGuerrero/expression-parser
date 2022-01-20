<?php  
    class SetExpr {

        public $elements = [];

        public function __construct($elements) {
            $this->elements = $elements;
        }

        public function __toString() {
            return "{" . implode(",", 
                array_map(function($elt) { return $elt->value . (($elt->probability) ? "=>" . $elt->probability : ""); }, $this->elements)) . "}";
        }

        public function accept($visitor) {
            try {
                return $visitor->visitSetExpr($this);
            } catch (Exception $e) {
                throw $e;
            }
        }

    }

    class SetElement {
        
        public $value;
        public $probability;

        public function __construct($value, $probability = null) {
            $this->value = $value;
            $this->probability = $probability;
        }

    }