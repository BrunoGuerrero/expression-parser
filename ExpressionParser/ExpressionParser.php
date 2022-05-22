<?php
    include ('Scanner.php');
    include ('Token.php');
    include ('TokenType.php');
    include ('Parser.php');
    include ('Expr.php');
    include ('LiteralExpr.php');
    include ('BinaryExpr.php');
    include ('UnaryExpr.php');
    include ('CallExpr.php');
    include ('SetExpr.php');
    include ('GroupingExpr.php');
    include ('VariableExpr.php');
    include ('IntervalExpr.php');
    include ('Visitor.php');
    include ('Interpreter.php');
    include ('InterpreterException.php');
    include ('ExpressionInterpreter.php');

    class ExpressionParser {

        public function parse($expression) {

            $scannedExpression = (new Scanner($expression))->scanTokens();
            $parsedExpression = (new Parser($scannedExpression))->parse();

            return $parsedExpression;
        }

        public function interpret($tokens, $userDefined = []) {
            return (new Interpreter($userDefined))->interpret($tokens);
        }

        public function preProcess($userDefined) {

            $userDefined = $this->reorderVariables($userDefined);

            $processedVariables = [];
            foreach($userDefined as $index => $parameter) {
                if(is_string($parameter)) {
                    $processedVariables[$index] = (new ExpressionParser())->evaluate($parameter, $processedVariables);
                } else {
                    $processedVariables[$index] = $parameter;
                }
            }

            return $processedVariables;
        }

        public function evaluate($expression, $userDefined = []) {
                        
            $parsedExpression = $this->parse($expression);
            return $this->interpret($parsedExpression, $userDefined);
        }

        public function reorderVariables($paramsToReorder) {
            $variables = [];

            foreach($paramsToReorder as $name => $parameter) {
                if(is_string($parameter)) {
                    $scanner = new Scanner($parameter);
                    $tokens = $scanner->scanTokens();
                    $parser = new Parser($tokens);
                    $expression = $parser->parse();

                    $this->findVariables($expression, $variables);
                }
            } 

            $priorityVariables = [];
            $remainingVariables = [];

            foreach($paramsToReorder as $name => $parameter) {
                foreach($variables as $variableName) {
                    if($name === $variableName) {
                        $priorityVariables[$name] = $parameter;
                        continue;
                    }
                }
                $remainingVariables[$name] = $parameter;
            }

            if(count($priorityVariables) === count((array)$paramsToReorder)) {
                throw new Exception("Cyclical redundancy detected");
            } else if(sizeof($priorityVariables) > 0) {
                $priorityVariables = $this->reorderVariables($priorityVariables);
            }

            $allVariables = array_merge($priorityVariables, $remainingVariables);

            return $allVariables;
        }

        function findVariables($expression, &$variables, $index=0) {
            if(is_a($expression, 'VariableExpr')) {
                $lexeme = $expression->name->lexeme;
                if(!in_array($lexeme, $variables)) {
                    $variables[] = $lexeme;
                }
            } else if(is_a($expression, 'Expr')) {
                foreach(get_object_vars($expression) as $subexpr) {
                    if (is_array($subexpr)) {
                        foreach($subexpr as $elt) {
                            $this->findVariables($elt, $variables, $index + 1);
                        }
                    }
                    $this->findVariables($subexpr, $variables, $index + 1);
                }
            } else {
                return;
            }
        }
    }