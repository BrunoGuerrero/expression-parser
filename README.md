# PHP Expression Parser

Simple library allowing for interpreting various expressions computed as numeric values, obviously without using `eval`.

Once computed, expression will always return either `int` or `float` values.

## Disclaimer

This library has been developed for a dedicated purpose and is released if it may help. While everything is working as intended, this library is still in beta.

## Acknowledgment

Architecture (And most of the code) is directly inspired by @munificent/craftinginterpreters rather excellent book [Crafting Interpreters](http://craftinginterpreters.com)

## Usage

This library is currently available the old-school way, by adding `require_once('ExpressionParser/ExpressionParser.php)` on top of your PHP script.

The most straight-forward way of interpreting an expression is by instanciating an `ExpressionParser` and call the `evaluate()` function :

```php
$expression = "2+3*min(sqrt(9), 4)-!2"

try {
    return (new ExpressionParser())->evaluate($expression);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

If a same expression is meant to be evaluated multiple times (when using random values of user-defined variables), a good practice would be to `parse()` the expression once, and `interpret()` the parsed expression:

```php
$expression = "[1, 1000]";
$parser = new ExpressionParser();

try {
    $parsedExpression = $parser->parse($expression);
} catch (Exception $e) {
    die($e->getMessage());
}

for($i = 0; $i < 100; $i++) {
    try {
        echo $parser->interpret($parsedExpression) . " ";
    } catch (InterpreterException $e) {
        echo $e->getMessage();
    }
}
```

## Available operations

This library supports basic arithmetic and math operations, comparisons, as well as some extra stuff for giggles, such as bit manipulation and random notations. Values set as examples, such as `a` and `b`, can be `int`, `float`, or expressions themselves.

### Grouping
- Grouping expression `(a + b) * c` : adds precedence to expression set into parentheses.
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
- Zero-safe `?a` : Return 1 if `a` is 0, returns `a` otherwise. This is mostly used to avoid unintended divisions by 0.
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
### Random expressions
- Random number in range `[a, b]` : Returns a random integer value within included boundaries `a` and `b`.
- Random value in set `{a, b, c[, ...]}` : Returns a random value within the ones defined in the set.
- Weighted random values in set `{a=>x, b=>y, c}` : Returns random value, with weighted probabilities. Weights are automatically adjusted so that their sum is 100.  
If a value has no defined weight, its weight will be automatically set so that the total of weights is 100. If the total of user-defined weights is already over 100, unweighted values will have their weight automatically set at 100.
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
### Back-reference
- `\pos` : Returns the result of grouped expression at position `pos`: `(2 + 3) * \1` will be equivalent to `(2 + 3) * (2 + 3)`. `pos` value starts at 1.


## Custom variables and functions

Custom variables and functions can be passed into the interpreter to extend the system capabilities. In a similar fashion, custom functions can also be passed to the interpreter; functions without arguments can be called in expressions without parentheses. If a variable and a function shares the same name, the variable will be interpreted.

```php
// $userDefined can either be array or an object

$userDefined = [
  "ten" => 10,
  "PI" => M_PI,
  "double" => function($value) { return $value * 2; },
  "d6" => function() { return rand(1, 6); }, // Expecting no argument, d6() can be called without parentheses as simply 'd6'
  "d20" => fn() => rand(1, 20) // Same syntax as above, with PHP 7.4
]
$value = (new Interpreter($userDefined))->interpret($parsedExpression);

// Expression such as "10 * double(d6 + PI)" can now be evaluated.
```

### Implicit factor and volatile functions

Custom variables and functions allow for implicit multiplication in expression, such as `2PI+2` or `10double(PI)`. 

**Sometimes, a function is meant to be called multiple times,** rather than being multiplied by its factor; this can be achieved by using **volatile functions**. This can be achieved by passing the custom function into an array, and setting the second element of the array as `true`

```php
$userDefined = [
    "double" => function($value) { return $value * 2; },            // Non volatile
    "d" => [function($value) { return rand(1, $value); }, true],    // Volatile
    "d6" => [function() { return rand(1, 6); }, true],              // Volatile
]

$value = (new Interpreter($userDefined))->interpret($parsedExpression);

// '4double(20)' will be equivalent to 4 * double(20),
// '10d(100)' will trigger 10 d(100) calls, and return the total of their results,
// '2d6' will trigger two d6() calls, and return the total of their results
```

## Todo
- Allowing for user-defined variables to be expressions too,
- Allowing for enabling / disabling features of interpreter
