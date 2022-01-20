<?php
    class Parser {
        private array $tokens;
        private int $current = 0;
    
        public function __construct(array $tokens) {
            $this->tokens = $tokens;
        }

        public function parse() {
            return $this->expression();
        }

        private function expression() {
            return $this->equality();
        }

        private function equality() { 
            $expr = $this->comparison();
        
            while ($this->match(TokenType::BANG_EQUAL, TokenType::EQUAL)) {
                $operator = $this->previous();
                $right = $this->comparison();
                $expr = new BinaryExpr($expr, $operator, $right);
            }
        
            return $expr;
        }

        private function comparison() {
            $expr = $this->term();
        
            while ($this->match(TokenType::GREATER, TokenType::GREATER_EQUAL, TokenType::LESS, TokenType::LESS_EQUAL, TokenType::SPACESHIP)) {
                $operator = $this->previous();
                $right = $this->term();
                $expr = new BinaryExpr($expr, $operator, $right);
            }
        
            return $expr;
        }

        private function term() {
            $expr = $this->factor();
        
            while ($this->match(
                TokenType::MINUS, TokenType::PLUS, 
                TokenType::DOUBLE_PIPE, TokenType::DOUBLE_ESP, TokenType::OR, TokenType::XOR, TokenType::AND)) {
              $operator = $this->previous();
              $right = $this->factor();
              $expr = new BinaryExpr($expr, $operator, $right);
            }
        
            return $expr;
        }

        private function factor() {
            $expr = $this->unary();

            // Implicit factor : unary followed by string
            while($this->check(TokenType::IDENTIFIER)) {
                $operator = new Token(TokenType::STAR, TokenType::STAR, null, 0);
                $right = $this->unary();
                $expr = new BinaryExpr($expr, $operator, $right);
            }

            while ($this->match(TokenType::SLASH, TokenType::DOUBLE_STAR, TokenType::STAR, TokenType::HAT, TokenType::PERCENT, TokenType::TERNARY)) {
                $operator = $this->previous();
                $right = $this->unary();
                if(!$right) {
                    throw new Exception("Expected identifier or number, got " . $operator->lexeme);
                }
                $expr = new BinaryExpr($expr, $operator, $right);
            }
        
            return $expr;
        }

        private function unary() {
            if ($this->match(TokenType::BANG, TokenType::MINUS, TokenType::INTERROGATION, TokenType::TILDE)) {
                $operator = $this->previous();
                $right = $this->unary();
                return new UnaryExpr($operator, $right);
            }
            
            return $this->call();
        }

        private function call() {
            $expr = $this->primary();
        
            while (true) { 
                if ($this->match(TokenType::LEFT_PAREN)) {
                    $expr = $this->finishCall($expr);
                } else {
                    break;
                }
            }
        
            return $expr;
        }

        private function finishCall($callee) {
            $arguments = [];
            if (!$this->check(TokenType::RIGHT_PAREN)) {
                do {
                    $arguments[] = $this->expression();
                } while ($this->match(TokenType::COMMA));
            }
        
            $paren = $this->consume(TokenType::RIGHT_PAREN,
                                  "Expect ')' after arguments.");
        
            return new CallExpr($callee, $paren, $arguments);
        }

        private function primary() {       
            if ($this->match(TokenType::NUMBER, TokenType::STR)) {
                return new LiteralExpr($this->previous()->literal);
            }

            if ($this->match(TokenType::IDENTIFIER)) {
                return new VariableExpr($this->previous());
            }

            if ($this->match(TokenType::LEFT_PAREN)) {
              $expr = $this->expression();
              $this->consume(TokenType::RIGHT_PAREN, "Expected ')' to close group, got '" . $this->peek()->lexeme . "'");
              return new GroupingExpr($expr);
            }

            if ($this->match(TokenType::PIPE)) {
                $expr = $this->expression();
                //var_dump($expr);
                $pipe = $this->consume(TokenType::PIPE, "Expected '|' after abs expression, got '" . $this->peek()->lexeme . "'");
                return new UnaryExpr($pipe, $expr);
            }

            /*
            if ($this->match(TokenType::LEFT_BRACE)) {
                $var = $this->unary();
                $pipe = $this->consume(TokenType::RIGHT_BRACE, "Expected '}' after variable name, got '" . $this->peek()->lexeme . "'");
                return new VariableExpr($var->name);
            }
            */

            if ($this->match(TokenType::LEFT_BRACE)) {
                return $this->buildSet();
            }

            if ($this->match(TokenType::LEFT_SQ_BRACE)) {
                $min = $this->expression();
                $this->consume(TokenType::COMMA, "Expected comma, got '" . $this->peek()->lexeme . "'");
                $max = $this->expression();
                $this->consume(TokenType::RIGHT_SQ_BRACE, "Expected closing ], got '" . $this->peek()->lexeme . "'");
                return new IntervalExpr($min, $max);
            }

            throw new Exception("Parsing failed, unexpected character " . $this->peek());
        }

        private function buildSet() {    
            $parameters = [];
                
            while (!$this->check(TokenType::RIGHT_BRACE)) { 
                do {
                    $paramValue = $this->unary();
                    if($this->check(TokenType::ARROW)) {
                        $this->consume(TokenType::ARROW, "Arrow expected");
                        $paramProb = $this->unary();
                    } else {
                        $paramProb = null;
                    }
                    $parameters[] = new SetElement($paramValue, $paramProb); 
                } while ($this->match(TokenType::COMMA));
            }

            $this->consume(TokenType::RIGHT_BRACE, "Right brace } expected");

            return new SetExpr($parameters);
        }

        private function match(...$types) {
            foreach ($types as $type) {
                if ($this->check($type)) {
                    $this->advance();
                    return true;
                }
            }

            return false;
        }

        private function check($type) {
            if ($this->isAtEnd()) {
                return false;
            } 
            return ($this->peek()->type == $type);
        }

        private function advance() {
            if (!$this->isAtEnd()) $this->current++;
            return $this->previous();
        }

        private function isAtEnd() {
            $peeked = $this->peek();
            return ($this->peek()->type == TokenType::EOF);
        }
        
        private function peek() {
            return $this->tokens[$this->current];
        }
        
        private function previous() {
            return $this->tokens[$this->current - 1];
        }

        private function consume($type, $message) {
            if ($this->check($type)) {
                return $this->advance();
            }
        
            die ($message);
        }
    }