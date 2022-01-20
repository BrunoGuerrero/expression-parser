<?php
    class MathUtils {
        public static function weightedRandom($probabilities = [], $default, $value = null) {       
            // If total of probabilities is under 100, adds a default value
            // that can be returned.
            $rand = $value ?? rand(1, max(array_sum($probabilities), 100));

            $cursor = 0;
            foreach ($probabilities as $key => $probability) {
                $cursor += $probability;
                if($rand <= $cursor) {
                    return $key;
                }
            }
            return $default;
        }
    }

