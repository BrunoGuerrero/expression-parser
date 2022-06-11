<?php
   class Scanner {
        private $source;
        private array $tokens = [];
        
        private int $start = 0;
        private int $current = 0;
        private int $line = 1;

        public function __construct($source) {
            $this->source = $source;
            return $this;
        }

        public function scanTokens() {
            while (!$this->isAtEnd()) {
              // We are at the beginning of the next lexeme.
              $this->start = $this->current;
              $this->scanToken();
            }
        
            $this->tokens[] = new Token(TokenType::EOF, "", null, $this->line);
            return $this->tokens;
        }

        private function scanToken() {
            $c = $this->advance();
            switch ($c) {
                case '(': $this->addToken(TokenType::LEFT_PAREN); break;
                case ')': $this->addToken(TokenType::RIGHT_PAREN); break;
                case '{': $this->addToken(TokenType::LEFT_BRACE); break;
                case '}': $this->addToken(TokenType::RIGHT_BRACE); break;
                case '[': $this->addToken(TokenType::LEFT_SQ_BRACE); break;
                case ']': $this->addToken(TokenType::RIGHT_SQ_BRACE); break;
                case ',': $this->addToken(TokenType::COMMA); break;
                case '.': $this->addToken(TokenType::DOT); break;
                case '-': $this->addToken(TokenType::MINUS); break;
                case '+': $this->addToken(TokenType::PLUS); break;
                case '/': $this->addToken(TokenType::SLASH); break; 
                case '*': 
                    $this->addToken($this->match('*') ? TokenType::DOUBLE_STAR : TokenType::STAR); 
                    break;
                case '%': $this->addToken(TokenType::PERCENT); break; 
                case '^': $this->addToken(TokenType::HAT); break; 
                case '~': $this->addToken(TokenType::TILDE); break; 
                case '?': 
                    $this->addToken($this->match(':') ? TokenType::TERNARY : TokenType::INTERROGATION); 
                    break; 
                case '|': 
                    $this->addToken($this->match('|') ? TokenType::DOUBLE_PIPE : TokenType::PIPE); 
                    break;
                case '!': 
                    $this->addToken($this->match('=') ? TokenType::BANG_EQUAL : TokenType::BANG); 
                    break;
                case '=': 
                    $this->addToken($this->match('>') ? TokenType::ARROW : TokenType::EQUAL);
                    break;
                case '<': 
                    if($this->match('<')) {
                        $this->addToken(TokenType::DOUBLE_LEFT_CARET);
                    } else {
                        $this->addToken(($this->match('='))
                            ? (($this->match(">") ? TokenType::SPACESHIP : TokenType::LESS_EQUAL))
                            : TokenType::LESS); 
                    }
                    break;
                case '>': 
                    if($this->match('>')) {
                        $this->addToken(TokenType::DOUBLE_RIGHT_CARET);
                    } else {
                        $this->addToken(($this->match('=')) ? TokenType::GREATER_EQUAL : TokenType::GREATER); 
                    }
                    break;
                case '&': 
                    $this->addToken(($this->match('&')) ? TokenType::DOUBLE_ESP : TokenType::ESP); 
                    break;
                case '\\': 
                    $this->addToken(TokenType::BACKSLASH); break; 
                case ' ':
                case '\r':
                case '\t':
                    break;

              default:
                if ($this->isDigit($c)) {
                    $this->number();
                } else if ($this->isAlpha($c)) {
                    $this->identifier();
                } else {
                    die(" Unexpected character : <i>" . $c . "</i> at line <b>" . $this->line . "</b>");
                }
                break;
            }
        }

        private function number() {
            while ($this->isDigit($this->peek())) {
                $this->advance();
            }
        
            // Look for a fractional part
            if ($this->peek() == '.' && $this->isDigit($this->peekNext())) {
                // Consume the "."
                $this->advance();
        
                while ($this->isDigit($this->peek())) {
                    $this->advance();
                }
            }
        
            $this->addToken(TokenType::NUMBER,
                (float)substr($this->source, $this->start, $this->current - $this->start));
        }

        private function identifier() {
            while ($this->isAlphaNumeric($this->peek())) $this->advance();
            $this->addToken(TokenType::IDENTIFIER);
        }

        private function isAlpha($c) {
            return ctype_alpha($c);
        }

        private function isDigit($c) {
            return ctype_digit($c);
        }
    
        private function isAlphaNumeric($c) {
            return $this->isAlpha($c) || $this->isDigit($c) || $c === '_';
        }

        private function advance() {
            return substr($this->source, $this->current++, 1);
        }
        
        private function addToken($type, $literal = null) {
            $text = substr($this->source, $this->start, $this->current - $this->start);
            $this->tokens[] = new Token($type, $text, $literal, $this->line);
        }

        private function isAtEnd() {
            return $this->current >= strlen($this->source);
        }

        private function match($expected) {
            if ($this->isAtEnd()) { 
                return false;
            }

            if (substr($this->source, $this->current, 1) != $expected) {
                return false;
            }
        
            $this->current++;
            return true;
          }

        private function peek() {
            if ($this->isAtEnd()) {
                return '\0';
            }
            return substr($this->source, $this->current, 1);
        }

        private function peekNext() {
            if ($this->current + 1 >= strlen($this->source)) {
                return '\0';
            }
            return substr($this->source, $this->current + 1, 1);
          } 
    }