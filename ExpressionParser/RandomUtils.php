<?php
    class RandomUtils {
        public static function weightedRandom($probabilities = [], $value = null) {       
            $precision = 10**6;

            $rand = $value ?? rand(1, array_sum($probabilities) * $precision);

            $cursor = 0;
            foreach ($probabilities as $key => $probability) {
                if($probability < 0) {
                    throw new Exception("Probability weight cannot be negative, " . $probability . " given.");
                }
                $cursor += $probability * $precision;
                if($rand <= $cursor) {
                    return $key;
                }
            }
            return;
        }
    }

