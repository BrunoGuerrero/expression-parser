<?php
    class ExpressionInterpreter {

        public static function evaluate($expression, $variables) {
            $tokens = (new Scanner($expression))->scanTokens();

            $parser = new Parser($tokens);
            try {
                $expressions = $parser->parse();
            } catch(Exception $e) {
                die($e->getMessage());
            }

            return (int)(new Interpreter($variables))->interpret($expressions);
        }

    }