<?php
    include_once('MathUtils.php');

    class Interpreter implements Visitor {

        public $variables;
        public $functions;

        function __construct($variables = [], $functions = []) {
            $this->variables = $variables;
            $this->functions = $functions;
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
            if ($object === null) return "nil";
        
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
                        throw new InterpreterException("Division by zero: " . $left . "/" . $right, 1);
                    }
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
                case TokenType::PIPE:
                    $result = ($left | $right);
                    return intval($result);
                case TokenType::DOUBLE_PIPE:
                    $result = ($left || $right);
                    return intval($result);
                case TokenType::DOUBLE_ESP:
                    $result = ($left && $right);
                    return intval($result);

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
                case "atan":
                    return atan($this->arg($expr, 0));
                case "ceil":
                    return ceil($this->arg($expr, 0));
                case "floor":
                    return floor($this->arg($expr, 0));
                case "round":
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
                    if($this->arg($expr, 0) == $this->arg($expr, 1)) {
                        return $this->arg($expr, 2);
                    } else if($this->arg($expr, 0) == $this->arg($expr, 2)) {
                        return $this->arg($expr, 1);
                    } else {
                        return $this->arg($expr, 0);
                    }

                /* Extra functions */
                case "bin":
                    return decbin($this->arg($expr, 0));
                case "bit":
                    $bitPos = $this->arg($expr, 1);
                    if($bitPos <= 0) {
                        throw new Exception("Bit value in bit() function starts at position 1,  " . $this->arg($expr, 1) . " given.");
                    }
                    $binValue = strval(decbin($this->arg($expr, 0)));
                    $bit = strlen($binValue) - $bitPos;

                    if($bitPos > strlen($binValue)) {
                        return 0;
                    }
                    return substr($binValue, $bitPos - 1, 1);
            }

            // Custom function handling
            if(is_array($this->functions)) {
                if(isset($this->functions[$functionName])) {
                    $function = $this->functions[$functionName];
                }
            } else if (is_object($this->functions)) {
                if(property_exists($this->functions, $functionName)) {
                    $function = $this->functions->$functionName;
                }
            }

            if(empty($function)) {
                throw new Exception("Unknown function " . $functionName . "()");
            }

            $arguments = array_map(function($arg) {
                return $this->evaluate($arg);
            }, $expr->arguments);

            $fct = new ReflectionFunction($function);
            $nbRequiredArguments = $fct->getNumberOfRequiredParameters();
            $nbArguments = sizeof($expr->arguments);

            if($nbRequiredArguments != $nbArguments) {
                throw new InterpreterException(
                    "Function '" . $expr->callee->name->lexeme  . "()' expects " . $nbRequiredArguments . " arguments, "
                    . $nbArguments . " given.", 1);
            }

            try {   
                $result = call_user_func_array($function, $arguments);
            } catch (Exception $e) {
                throw new InterpreterException($e->getMessage());
            }

            return $result;
        }

        function visitGroupingExpr($expr) {
            return $this->evaluate($expr->expression);
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
            }
        
            // Unreachable.
            return null;
        }

        function visitVariableExpr($expr) {

            $variableName = $expr->name->lexeme;

            if(is_array($this->variables)) {
                if(isset($this->variables[$variableName])) {
                    return $this->variables[$variableName];
                } else {
                    throw new Exception("Variable '" . $variableName . "' does not exist");
                }
            } else if (is_object($this->variables)) {
                if(property_exists($this->variables, $variableName)) {
                    return $this->variables->$variableName;
                } else {
                    throw new Exception("Variable '" . $variableName . "' does not exist");
                }
            }
        }

        public function visitIntervalExpr($expr) {
            return rand($this->evaluate($expr->min), $this->evaluate($expr->max));
        }

        public function visitSetExpr($expr) {
            $parameters = [];
            $totalProbabilities = 0;
            $nbWeightlessValues = 0;
            
            foreach($expr->elements as $element) {
                if($element->probability !== null) {
                    $totalProbabilities += $this->evaluate($element->probability);
                    $nbWeightlessValues++;
                }
            }

            $nbRemainingValues = sizeof($expr->elements) - $nbWeightlessValues;

            foreach($expr->elements as $element) {
                if($element->probability !== null) {
                    $parameters[$this->evaluate($element->value)] = $this->evaluate($element->probability);
                } else {
                    if($totalProbabilities < 100) {
                        $parameters[$this->evaluate($element->value)] = floor((100 - $totalProbabilities) / $nbRemainingValues);
                    } else {
                        $parameters[$this->evaluate($element->value)] = 100;
                    }
                }
            }

            return MathUtils::weightedRandom($parameters, 0);
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

    }