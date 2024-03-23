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

        public static function randomWithStep($min, $max, $step) { 
            
            $precisionMin = (int) strpos(strrev(strval($min)), ".");
            $precisionMax = (int) strpos(strrev(strval($max)), ".");
            $precisionStep = (int) strpos(strrev(strval($step)), ".");

            $randPrecision = max($precisionMin, $precisionMax, $precisionStep);

            $min = round($min, $randPrecision);
            $max = round($max, $randPrecision);

            if($step === 0) {
                throw "Precision in random range cannot be zero.";
            }
            
            if($step < 0) {
                $val = $max - rand(
                                intval($min * 1 / $step), 
                                intval($max * 1 / $step))
                            * $step;

                return round($val, $randPrecision);
            } else {
                $rand = rand(
                    intval($min * 1 / $step), 
                    intval($max * 1 / $step));

                return round($rand * $step, $randPrecision);
            }

        }
    }

