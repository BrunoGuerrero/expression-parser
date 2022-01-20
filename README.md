Simple library allowing for interpreting various expressions computed as numeric values, without using `eval` for safer operations.
Once computed, expression will always return either `int` of `float` values.

## Disclaimer

This library has been developed for a dedicated purpose and is released because why not. While everything is working as intended, this library is still in beta.

## Acknowledgment

Architecture (And most of the code) is directly inspired by @munificent rather excellent book [Crafting Interpreters](http://craftinginterpreters.com)

## Usage

As of now, import this library by adding `require_once('ExpressionParser/ExpressionParser.php)` on top of your PHP script. Yeah, pretty old school.

Expression can then be interpreted using:

```php
$expression = "2+3*min(sqrt(9), 4)-!2"

// Split expression into interpretable tokens
$tokens = (new Scanner($expression))->scanTokens();
$parser = new Parser($tokens);
try {
    $parsedExpression = $parser->parse();
} catch(Exception $e) {
    die($e->getMessage());
}

// Interpret tokens and display result of expression evaluation
try {
    echo (new Interpreter($variables, $functions))->interpret($parsedExpression);
} catch (InterpreterException $e) {
    echo $e->getMessage();
}
```

## Available operations

This library supports basic arithmetic and math operations, comparisons, as well as some extra stuff for giggles, such as bit manipulation and random notations. Values set as examples, such as a and b, can be int, float, or expressions themselves.

### Grouping
- Grouping expression `(a + b) * c` : adds precedence to expression set into parenthesis.
### Arithmetic operators
- Addition `a + b` : Adds `a` and `b` values. I mean... yeah.
- Substraction `a - b` : Substracts `b` from `a`. Like a subsctraction does.
- Multiplication `a * b` : Returns the product of `a` and `b` values.
- Division `a / b` : Divides `a` value by `b`. Divisions by 0 will throw an error because, you know.
- Mod `a % b` : Returns the remainder of `a / b` 
- Exponentiation `a ** b` : Returns `a` to the power of `b`
### Modifiers
- Minus `-a`
- Not `!a` : Returns 1 if `a` is 0, returns 0 otherwise.
- To boolean `!!a` : Returns 1 if `a` is different from 0.
- Zero-safe `?a` : Return 1 if `a` is 0, returns a otherwise. This is mostly used to avoid unintended divisions by 0.
- Absolute `|a|` : Returns positive value of `a`. Same as `abs(a)` function.
### Comparison
- Equals `a = b` : Returns 1 if `a` equals `b`, returns 0 otherwise.
- Greater than `a > b` : Returns 1 if `a` is greater than `b`, returns 0 otherwise.
- Greater or equals `a >= b` : Returns 1 if `a` is greater than, or equals `b`; returns 0 otherwise.
- Less than `a < b` : Returns 1 if `a` is less than `b`, returns 0 otherwise.
- Less or equals `a <= b` : Returns 1 if `a` is less than, or equals `b`; returns 0 otherwise.
- Compare `a <=> b` : Returns -1 if `a < b`, 1 if `a > b`, 0 if `a = b`. Inspired by php [Spaceship operator](https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.spaceship-op).
### Logic operators
- And `a && b` : Returns 0 if either `a` or `b` equals 0, returns 1 otherwise.
- Or `a || b` : Returns 1 if either `a` or `b` is different from 0, returns 0 otherwise.
- Zero-check `a ?: b` : Returns `a` if different from 0, returns `b` otherwise.
### Comparison functions
- Min value `min(a, b)` : Returns minimum value between `a` and `b`.
- Max value `max(a, b)` : Returns maximum value between `a` and `b`.
- Clamp `clamp(val, a, b)` : Returns `a` if `val < a`, `b` if `val > b`, returns `val` otherwise.
- Swap `swap(val, a, b)` : Returns `b` if `val = a`, `a` if `val = b`, returns `val` otherwise.
### Math functions	
- Square root `sqrt(a)` : Returns square root value of `a`.
- Sinus `sin(a)` : Returns sinus value of `a`.
- Cosinus `cos(a)` : Returns cosinus value of `a`.
- Tangent `tan(a)` : Returns tangent value of `a`.
- Arc tagent `atan(a)` : Returns arc tangent value of `a`.
- Exponent `exp(a)` : Returns value of *e* to the power of `a`.
- Logarithm `log(a, base)`: Returns the logarithm of `a` to base.
### Bit manipulation
-  To base 2 `bin(a)` : Returns base 2 value of `a`.
-  Get bit at `bit(val, pos)` : Returns bit at position `pos` of base 2 value of `val` (Starting from the rightest bit). `pos` value starts at 1.
### Bitwise operators
All the following operators return a value built from a bitwise operation. These are built upon their PHP equivalent built-in operators, which the [documentation on bitwise operators](https://www.php.net/manual/en/language.operators.bitwise.php) will do a better job at explaining than I will ever do.
-  Bitwise and `a & b`
-  Bitwise or `a | b` 
-  Bitwise xor `a ^ b`
-  Bitwise not `~a`
-  Left bit shift `a << b`
-  Right bit shift `a >> b`
### Random expressions
- Random number in range `[a, b]` : Returns a random integer value within included boundaries `a` and `b`.
- Random value in set `{a, b, c[, ...]}` : Returns a random value within the ones defined in the set.
- Weighted random values in set `{a=>x, b=>y, c}` : Returns random value, with weighted probabilities. Weights are automatically adjusted so that their sum is 100.  
If a value has no defined weight, its weight will be automatically set so that the total of weights is 100. If the total of user-defined weights is already over 100, unweighted values will have their weight automatically set at 100.

## Custom variables and functions
Custom variables can be passed into the interpreter to extend the system capabilities:

```php
$variables = [
  "PI" => M_PI,
  "ten" => 10
]
$value = (new Interpreter($variables))->interpret($parsedExpression);

// Expression such as "ten + PI * 2" can now be evaluated.
```

In a similar fashion, custom functions can also be passed to the interpreter:

```php
$functions = [
    "double" => function($value) { return $value * 2; },
    "d6" => fn() => rand(1, 6) // With PHP 7.4
];
$value = (new Interpreter([], $functions))->interpret($parsedExpression);

// Expression such as "10 * double(d6())" can now be evaluated.
```

Arguments `$variable` and `$functions` can either be arrays or php objects.
