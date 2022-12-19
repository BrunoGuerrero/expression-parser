<?php
    include_once('RandomUtils.php');

    class Interpreter implements Visitor {

        public $userDefined;
        public $groups = [];

        function __construct($userDefined = []) {
            $this->userDefined = $userDefined;
        }

        public function interpret($expression) { 
            try {
                $value = $this->evaluate($expression);
                return $this->stringify($value);
            } catch (Exception $e) {
                throw new InterpreterException("<b>ERROR - " . $e->getMessage() . "</b>");
                return "";
            }
        }

        private function stringify($object) {
            if ($object === null) return "null";
        
            if (is_numeric($object)) {
              $text = $object . "";
              return $text;
            }
        
            return $object . "";
          }

        function visitBinaryExpr($expr) {
            $left = $this->evaluate($expr->left);
            $right = $this->evaluate($expr->right);
        
            switch ($expr->operator->type) {
                case TokenType::MINUS:
                    return $left - $right;
                case TokenType::PLUS:
                    return $left + $right;
                case TokenType::SLASH:
                    if($right != 0) {
                        return $left / $right;
                    } else {
                        throw new InterpreterException("Division by zero: " . $left . "/" . $right);
                    }
                case TokenType::IMPLICIT_FACTOR:
                    $rightExpr = $expr->right;
                    if(is_a($rightExpr, "VariableExpr")) {
                        $functionName = $rightExpr->name->lexeme;
                    } else if(is_a($rightExpr, "CallExpr")) {
                        $functionName = $rightExpr->callee->name->lexeme;
                    }

                    $userDefined = $this->getUserDefined($functionName);
                    if(is_array($userDefined) && $userDefined[1] === true) {
                        $total = 0;
                        for($i = 0; $i < $left; $i++) {
                            $total += $this->evaluate($expr->right);
                        }
                        return $total;
                    }

                // Don't break : if right is not volatile, fallback to STAR mode :
                case TokenType::STAR:
                    return $left * $right;
                case TokenType::PERCENT:
                    return $left % $right;
                case TokenType::DOUBLE_STAR:
                    return pow($left, $right);
                case TokenType::SPACESHIP:
                    return $left <=> $right;
                case TokenType::TERNARY:
                    return $left ?: $right;

                // Logic operators
                case TokenType::ESP:
                    $result = ($left & $right);
                    return intval($result);
                case TokenType::HAT:
                    $result = ($left ^ $right);
                    return intval($result);
                case TokenType::PIPE:
                    $result = ($left | $right);
                    return intval($result);
                case TokenType::DOUBLE_PIPE:
                    $result = ($left || $right);
                    return intval($result);
                case TokenType::DOUBLE_ESP:
                    $result = ($left && $right);
                    return intval($result);

                // Bitshift
                case TokenType::DOUBLE_LEFT_CARET:
                    $result = ($left << $right);
                    return $result;
                case TokenType::DOUBLE_RIGHT_CARET:
                    $result = ($left >> $right);
                    return $result;

                // Comparisons
                case TokenType::EQUAL:
                    $result = ($left == $right);
                    return intval($result);
                case TokenType::GREATER:
                    $result = ($left > $right);
                    return intval($result);
                case TokenType::GREATER_EQUAL:
                    $result = ($left >= $right);
                    return intval($result);
                case TokenType::LESS:
                    $result = ($left < $right);
                    return intval($result);
                case TokenType::LESS_EQUAL:
                    $result = ($left <= $right);
                    return intval($result);               
            }
        }

        function visitCallExpr($expr) {
            
            $functionName = $expr->callee->name->lexeme;

            switch ($functionName) {
                /* Math functions */
                case "sin":
                    return sin($this->arg($expr, 0));
                case "cos":
                    return cos($this->arg($expr, 0));
                case "tan":
                    return tan($this->arg($expr, 0));
                case "asin":
                    return asin($this->arg($expr, 0));
                case "acos":
                    return acos($this->arg($expr, 0));
                case "atan":
                    return atan($this->arg($expr, 0));
                case "ceil":
                case "cl":
                    return ceil($this->arg($expr, 0));
                case "floor":
                case "fl":
                    return floor($this->arg($expr, 0));
                case "round":
                case "rn":
                    return round($this->arg($expr, 0));
                case "abs":
                    return abs($this->arg($expr, 0));  
                case "exp":
                    return exp($this->arg($expr, 0));  
                case "log":
                    return log($this->arg($expr, 0), $this->arg($expr, 1));
                case "log10":
                    return log10($this->arg($expr, 0));     
                case "sqrt":
                    $value = $this->arg($expr, 0);
                    if($value >= 0) {
                        return sqrt($value);
                    } else {
                        throw new Exception("Sqrt on negative value " . $value);
                    }     
                case "wave":
                    $min = $this->arg($expr, 0);
                    $max = $this->arg($expr, 1);
                    $period = $this->arg($expr, 2);
                    $t = $this->arg($expr, 3);
                    $amplitude = ($max - $min) / 2;

                    return $amplitude - $amplitude * cos((M_PI * $t) / ($period / 2)) + $min;

                /* Comparison functions */
                case "min":
                    return min($this->arg($expr, 0), $this->arg($expr, 1));
                case "max":
                    return max($this->arg($expr, 0), $this->arg($expr, 1));
                case "clamp":
                    return max($this->arg($expr, 1),
                        min($this->arg($expr, 2), $this->arg($expr, 0))                  
                    );
                case "swap":
                    $value = $this->arg($expr, 0);
                    $swap1 = $this->arg($expr, 1);
                    $swap2 = $this->arg($expr, 2);

                    if($value === $swap1) {
                        return $swap2;
                    } else if($value === $swap2) {
                        return $swap1;
                    } else {
                        return $value;
                    }
                case "gcd":
                    $value1 = $this->arg($expr, 0);
                    $value2 = $this->arg($expr, 1);

                    if((int)$value1 != $value1) {
                        throw new Exception("gcd() function only expects integers, " . $value1 . " received.");
                    }

                    if((int)$value2 != $value2) {
                        throw new Exception("gcd() function only expects integers, " . $value2 . " received.");
                    }

                    return gmp_intval(gmp_gcd(intval($value1), intval($value2)));
                case "lcm":
                    $value1 = $this->arg($expr, 0);
                    $value2 = $this->arg($expr, 1);

                    if((int)$value1 != $value1) {
                        throw new Exception("lcm() function only expects integers, " . $value1 . " received.");
                    }

                    if((int)$value2 != $value2) {
                        throw new Exception("lcm() function only expects integers, " . $value2 . " received.");
                    }

                    return gmp_intval(gmp_lcm(intval($value1), intval($value2)));
                case "pm":
                    $value = $this->arg($expr, 0);

                    return [-$value, $value][rand(0, 1)];
                case "pmi":
                    $value = $this->arg($expr, 0);

                    if(isset($expr->arguments[1])) {
                        return RandomUtils::randomWithStep(-$value, $value, $this->arg($expr, 1));
                    } else {
                        return rand(-$value, $value);
                    }
                case "to":
                    $value = $this->arg($expr, 0);

                    if(isset($expr->arguments[1])) {
                        return RandomUtils::randomWithStep(0, $value, $this->arg($expr, 1));
                    } else {
                        return rand(0, $value);
                    }
    

                /* Extra functions */
                case "bin":
                    return decbin($this->arg($expr, 0));
                case "bit":
                    $bitPos = $this->arg($expr, 1);
                    if($bitPos <= 0) {
                        throw new Exception("Bit value in bit() function starts at position 1,  " . $bitPos . " given.");
                    }
                    $binValue = strval(decbin($this->arg($expr, 0)));
                    $bit = strlen($binValue) - $bitPos;

                    if($bitPos > strlen($binValue)) {
                        return 0;
                    }
                    return substr($binValue, $bitPos - 1, 1);
            }

            $customFuncResult = $this->callCustomFunc($expr);
            if($customFuncResult !== null) {
                return $customFuncResult;
            } else {
                throw new InterpreterException("Unknown function '" . $functionName . "'");
            }
        }

        function visitGroupingExpr($expr) {
            $result = $this->evaluate($expr->expression);
            $this->groups[] = $result;
            return $result;
        }

        function visitLiteralExpr($expr) {
            return $expr->value;
        }

        function visitUnaryExpr($expr) {
            $right = $this->evaluate($expr->right);

            switch ($expr->operator->type) {
                case TokenType::MINUS:
                    return $right * -1;
                case TokenType::BANG:
                    return ($right > 0) ? 0 : 1;
                case TokenType::TILDE:
                    return ~$right;
                case TokenType::INTERROGATION:
                    return ($right == 0) ? 1 : $right;
                case TokenType::PIPE:
                    return abs($right);
                case TokenType::BACKSLASH:
                    if($right <= 0) {
                        throw new InterpreterException("Back references start at position 1, '\\" . $right . "' given.");
                    }
                    if(isset($this->groups[$right - 1])) {
                        return $this->groups[$right - 1];
                    } else {
                        throw new InterpreterException("Back reference to group at position '\\" . $right . "' did not return any value");
                    }
            }
        
            // Unreachable.
            return null;
        }

        function visitVariableExpr($expr) {

            $variableName = $expr->name->lexeme;
            $variable = $this->getUserDefined($variableName);

            if($variable === null) {
                throw new Exception("Variable or function '" . $variableName . "' could not be found");
            }

            if(is_array($variable) || is_callable($variable)) {
                return $this->callCustomFunc($expr);
            } else {
                return $variable;
            }
        }

        function callCustomFunc($expr) {
            if(is_a($expr, "CallExpr")) {
                $functionName = $expr->callee->name->lexeme;
                $arguments = array_map(function($arg) {
                    return $this->evaluate($arg);
                }, $expr->arguments);
            } else {
                $functionName = $expr->name->lexeme;
                $arguments = [];
            }

            $function = $this->getUserDefined($functionName);

            if($function === null) {
                return null;
            } else if (is_array($function)) {
                $function = $function[0];
            } else if(!is_callable($function)) {
                throw new InterpreterException("Variable '" . $functionName . "' is not a function.");
            }

            $fct = new ReflectionFunction($function);
            $nbRequiredArguments = $fct->getNumberOfRequiredParameters();
            $nbArguments = is_a($expr, "CallExpr") ? sizeof($expr->arguments) : 0;

            if($nbRequiredArguments != $nbArguments) {
                throw new InterpreterException(
                    "Function '" . $functionName  . "()' expects " . $nbRequiredArguments . " arguments, "
                    . $nbArguments . " given.");
            }

            try {   
                $result = call_user_func_array($function, $arguments);
            } catch (Exception $e) {
                throw new InterpreterException($e->getMessage());
            }

            return $result;
        }

        public function visitIntervalExpr($expr) {
            $stepExpr = $expr->precision ?? 1;

            if($stepExpr === 1) {
                return mt_rand($this->evaluate($expr->min), $this->evaluate($expr->max));
            } else {
                return RandomUtils::randomWithStep($this->evaluate($expr->min), $this->evaluate($expr->max), $this->evaluate($stepExpr));
            }
        }

        public function visitSetExpr($expr) {
            $parameters = [];
            
            foreach($expr->elements as $element) {
                if($element->probability !== null) {
                    $parameters[$this->evaluate($element->value)] = $this->evaluate($element->probability);
                } else {
                    $parameters[$this->evaluate($element->value)] = 1;
                }
            }

            return RandomUtils::weightedRandom($parameters);
        }

        private function evaluate($expr) {
            return $expr->accept($this);
        }


        private function arg($expr, $index) {
            if(!isset($expr->arguments[$index])) {
                throw new Exception("Could not find parameter at position " . ($index + 1));
            }
            return (float)$this->evaluate($expr->arguments[$index]);
        }

        private function getUserDefined($name) {
            if(is_array($this->userDefined) && isset($this->userDefined[$name])) {
                return $this->userDefined[$name];
            } else if (is_object($this->userDefined) && property_exists($this->userDefined, $name)) {
                return $this->userDefined->$name;
            }
            return null;
        }

    }