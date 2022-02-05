# PHP Expression Parser

Simple library allowing for interpreting various expressions computed as numeric values, obviously without using `eval`.

Once computed, expression will always return either `int` or `float` values.

## Disclaimer

This library has been developed for a dedicated purpose and is released for anyone to toy around with. While everything is working as intended, this library is offered as is.

## Acknowledgment

Architecture is directly inspired by [Bob Nystrom](https://github.com/munificent) rather excellent book [Crafting Interpreters](http://craftinginterpreters.com). Most of the code is ported either from selected parts of the book examples or from the java implementation [available on the book's repository](https://github.com/munificent/craftinginterpreters/tree/master/java/com/craftinginterpreters/lox), with some liberties taken by yours truly to craft some new features.

## Documentation

- [Basic usage](#usage)
- [Availabe operations](#available-operations)
  - [Grouping parentheses](#grouping-parentheses)
  - [Arithmetic operators](#arithmetic-operators)
  - [Modifiers](#modifiers)
  - [Comparison operators](#comparison-operators)
  - [Comparison functions](#comparison-functions)
  - [Math functions](#math-functions)
  - [Logic operators](#logic-operators)
  - [Random expressions](#random-expressions)
  - [Bit manipulation](#bit-manipulation)
  - [Bitwise operators](#bitwise-operators)
  - [Back-referencing](#back-referencing)
- [Expanding parser capabilities](#expanding-parser-capabilities)
  -  [Custom variables and functions](#custom-variables-and-functions)
  -  [Sub-expressions as user-defined variables](#sub-expressions-as-user-defined-variables)
  -  [Implicit factor and volatile functions](#implicit-factor-and-volatile-functions)

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

### Grouping parentheses
| Function | Notation | Behaviour|
|:---------|:---------|:---------|
| Grouping | `(a + b) * c` | Adds precedence to expression set into parentheses |

### Arithmetic operators
| Function | Notation | Behaviour|
|:---------|:---------|:---------|
| Addition | `a + b` | Returns sum of `a` and `b` values. I mean... yeah. |
| Substraction | `a - b` | Substracts `b` from `a`. |
| Multiplication | `a * b` | Returns the product of `a` and `b` values. |
| Division | `a / b` | Divides `a` value by `b`. Divisions by 0 will throw an error because, you know. |
| Exponentiation | `a ** b` | Returns `a` to the power of `b` |

### Modifiers
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| Minus | `-a` | Multiplies `a` by -1. |
| Not | `!a` | Returns 1 if `a` is 0, returns 0 otherwise. |
| To boolean | `!!a` | Returns 1 if `a` is different from 0, returns 0 otherwise. |
| Zero-safe | `?a` | Return 1 if `a` is 0, returns `a` otherwise. Mostly used to avoid unintended divisions by 0. |
| Absolute value | `\|a\|` | Returns positive value of `a`. Same as `abs(a)` function. |

### Comparison operators
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| Equals | `a = b` | Returns 1 if `a` equals `b`, returns 0 otherwise. |
| Greater than | `a > b` | Returns 1 if `a` is greater than `b`, returns 0 otherwise. |
| Greater or equals | `a >= b` | Returns 1 if `a` is greater than, or equals `b`; returns 0 otherwise. |
| Less than | `a < b` | Returns 1 if `a` is less than `b`, returns 0 otherwise. |
| Less or equals | `a <= b` | Returns 1 if `a` is less than, or equals `b`; returns 0 otherwise. |
| Compare | `a <=> b` | Returns -1 if `a < b`, 1 if `a > b`, 0 if `a = b`. Built upon php [Spaceship operator](https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.spaceship-op).

### Comparison functions
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| Min value | `min(a, b)` | Returns minimum value between `a` and `b`. |
| Max value | `max(a, b)` | Returns maximum value between `a` and `b`. |
| Clamp | `clamp(val, a, b)` | Returns `a` if `val < a`, `b` if `val > b`, returns `val` otherwise. |
| Swap | `swap(val, a, b)` | Returns `b` if `val = a`, `a` if `val = b`, returns `val` otherwise. |

### Math functions	
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| Square root | `sqrt(a)` | Returns square root value of `a`. |
| Sinus | `sin(a)` | Returns sinus value of `a`. |
| Cosinus | `cos(a)` | Returns cosinus value of `a`. |
| Tangent | `tan(a)` | Returns tangent value of `a`. |
| Arc tagent | `atan(a)` | Returns arc tangent value of `a`. |
| Exponent | `exp(a)` | Returns value of *e* to the power of `a`. |
| Logarithm | `log(a, base)`| Returns the logarithm of `a` to `base`. |

### Logic operators
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| And | `a && b` | Returns 0 if either `a` or `b` equals 0, returns 1 otherwise. |
| Or | `a \|\| b` | Returns 1 if either `a` or `b` is different from 0, returns 0 otherwise. |
| Zero-check | `a ?: b` | Returns `a` if different from 0, returns `b` otherwise. |

### Random expressions
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| Random in range | `[a, b]` | Returns a random integer value within included boundaries `a` and `b`. |
| Random in set | `{a, b, c, ...}` | Returns a random value among the ones defined in set. |
| Weighted random | `{a=>x, b=>y, c}` | Returns random value in set, with weighted probabilities. |

Regarding weighted randoms: 
- All unweighted values have a weight of 1
- Weighted values are relative to 1, meaning a weight of 2 have 2x more chances to be picked, while a weight of 0.5 have 2x less chances of being picked.
- Weights can go down to 6 decimal digits

### Bit manipulation
| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| To base 2 | `bin(a)` | Returns base 2 value of `a`. |
| Get bit at | `bit(val, pos)` | Returns bit at position `pos` of base 2 value of `val` (Starting from the rightest bit). `pos` value starts at 1. |

### Bitwise operators
All the following operators return a value built from a bitwise operation. These are built upon their PHP equivalent built-in operators, which the [documentation on bitwise operators](https://www.php.net/manual/en/language.operators.bitwise.php) will do a better job at explaining than I will ever do.
| Function | Notation |
|:---------|:---------|
| Bitwise and | `a & b` |
| Bitwise or | `a \| b` | 
| Bitwise xor | `a ^ b` |
| Bitwise not | `~a` |
| Left bit shift | `a << b` |
| Right bit shift | `a >> b` | 

### Back-referencing
Using back-reference allows to re-use the result of a grouping notation used earlier in the expression.

| Function | Notation | Behaviour |
|:---------|:---------|:---------|
| Back-reference | `\pos` | Returns the result of grouped expression at position `pos`. |

- Example : `(2 + 3) * \1` will be equivalent to `(2 + 3) * (2 + 3)`. 
- This can be useful to retrieve a value that has been generated at random, for expressions such as `([1,100]) + sqrt(\1)`
- `pos` value starts at 1, starting with the most leftward group, with inner parentheses having higher precedence:  
![equation](https://user-images.githubusercontent.com/16825882/151226242-6bae1c1d-6b3f-400a-a10b-f726582b2c66.png)

## Expanding parser capabilities
### Custom variables and functions

Custom variables can be passed into the interpreter to extend the system capabilities. In a similar fashion, custom functions can also be passed to the interpreter.

Functions without arguments can be called in expressions without parentheses.

```php
$userDefined = [
  "ten" => 10,
  "PI" => M_PI,
  "double" => function($value) { return $value * 2; },
  "d6" => function() { return rand(1, 6); }, // Expecting no argument, d6() can be called without parentheses as simply 'd6'
  "d20" => fn() => rand(1, 20) // Same syntax as above, with PHP 7.4 or above
]
$value = (new Interpreter($userDefined))->interpret($parsedExpression);

// Expressions such as "10 * double(d6 + PI)" can now be evaluated.
```

### Sub-expressions as user-defined variables

Custom variables can also be expressions themselves. In this case, call the `preProcess()` method to pre-compute every expression and ensure there is no cyclical redundancy:

```php
$parser = new ExpressionParser();

// Calling the preProcess() methods allows for ordering variables in any fashion
$userDefined = [
  "tau" => "2 * PI",
  "PI" => M_PI,
  "double" => function($value) { return $value * 2; },
  "d6" => function() { return rand(1, 6); }, // Expecting no argument, d6() can be called without parentheses as simply 'd6'
  "d20" => fn() => rand(1, 20) // Same syntax as above, with PHP 7.4 or above
]

$userDefined = $parser->preProcess($userDefined); // Reorders, pre-compute and checks integrity of user defined elements

try {
    return (new ExpressionParser())->evaluate($expression, $userDefined);
} catch (InterpreterException $e) {
    echo $e->getMessage();
}

// Expressions such as "10 * double(d6 + tau)" can now be evaluated.
```

### Implicit factor and volatile functions

Custom variables and functions allow for implicit multiplication in expression, such as `2PI+2` or `10double(PI)`. 

**Sometimes, a function is meant to be called multiple times, rather than being multiplied by its factor**; this can be done **by defining a function as volatile**. This can be achieved by passing the custom function into an array, and setting the second element of the array as `true`:

```php
$userDefined = [
    "double" => function($value) { return $value * 2; },            // Non volatile
    "d" => [function($value) { return rand(1, $value); }, true],    // Volatile
    "d6" => [function() { return rand(1, 6); }, true],              // Volatile
]

$value = (new Interpreter($userDefined))->interpret($parsedExpression);

// '4double(20)' will be equivalent to 4 * double(20)

// '10d(100)' will trigger 10 d(100) calls, and return the total of their results. 
// '10 * d(100)' will however compute d(100) once, and multiply result by 10.

// '2d6' will trigger two d6() calls, and return the total of their results. 
// '2 * d6' will however call d6() once, and multiply result by 2.
```
