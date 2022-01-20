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
        public static function evaluate($expression, $variables = []) {

            $scanner = new Scanner($expression);
            $tokens = $scanner->scanTokens();
        
            $parser = new Parser($tokens);
            try {
                $parsedExpression = $parser->parse();
            } catch(Exception $e) {
                die($e->getMessage());
            }
        
            return (new Interpreter($variables))->interpret($parsedExpression);
        }
    }