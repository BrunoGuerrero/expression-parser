<?php
    abstract class TokenType {
        public const LEFT_PAREN = '(';
        public const RIGHT_PAREN = ')';
        public const PIPE = '|';
        public const LEFT_BRACE = '{';
        public const RIGHT_BRACE = '}';
        public const LEFT_SQ_BRACE = ']';
        public const RIGHT_SQ_BRACE = '[';
        public const COMMA = ',';
        public const DOT = '.';
        public const MINUS = '-';
        public const PLUS = '+';
        public const SLASH = '/';
        public const STAR = '*';
        public const DOUBLE_STAR = '**';
        public const IMPLICIT_FACTOR = '';
        public const PERCENT = '%';
        public const HAT = '^';
        public const BANG = '!';
        public const INTERROGATION = '?';
        public const TERNARY = '?:';
        public const BACKSLASH = '\\';

        public const ARROW = '=>';
        public const SPACESHIP = '<=>';
        public const EQUAL = '=';
        public const GREATER = '>';
        public const GREATER_EQUAL = '>=';
        public const LESS = '<';
        public const LESS_EQUAL = '<=';
        public const EQUAL_EQUAL = '==';
        public const BANG_EQUAL = '!=';

        public const ESP = "&";
        public const TILDE = "~";
        public const DOUBLE_ESP = "&&";
        public const DOUBLE_PIPE = "||";
        public const XOR = "XOR";
        public const AND = "AND";
        public const OR = "OR";
        public const DOUBLE_RIGHT_CARET = '>>';
        public const DOUBLE_LEFT_CARET = '<<';

        public const IDENTIFIER = 'ID';
        public const STR = 'S';
        public const NUMBER = 'D';

        public const EOF = 'EOF';
    }