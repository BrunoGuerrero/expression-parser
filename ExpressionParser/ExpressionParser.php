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
            return  (new Interpreter($userDefined))->interpret($tokens);
        }

        public function evaluate($expression, $userDefined = []) {

            $parsedExpression = $this->parse($expression);
            return $this->interpret($parsedExpression, $userDefined);
        }
    }