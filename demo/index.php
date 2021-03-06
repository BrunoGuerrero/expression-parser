<?php 
    include_once("../ExpressionParser/ExpressionParser.php"); 
    
    $expression = null;
    $value = null;
    
    if(isset($_GET['expression'])) {
        $expression = $_GET['expression'];
        $value = evaluate($expression);
    }

    function evaluate($expression) {       
        $parser = new ExpressionParser();
        
        $userDefined = [
            "tau" => "2PI",
            "PI" => M_PI,
            "ten" => 10,
            "double" => function($value) { return $value * 2; },
            "d" => [function($value) { return rand(1, $value); }, true],
            "d6" => [function() { return rand(1, 6); }, true],
        ];

        $userDefined = $parser->preProcess($userDefined);

        try {
            return (new ExpressionParser())->evaluate($expression, $userDefined);
        } catch (InterpreterException $e) {
            echo $e->getMessage();
        }
    }
?>

<head>
    <style>
        * {
            font-family: monospace;
        }
        input[type="text"] {
            height: 40px;
            width: 50vw;
            font-size: 1.2em;
        }
        td {
            width: 25vw;
            padding: 10px;
            vertical-align: top;
            border-top: 1px solid #ccc;
        }
        td>label {
            display: inline-block;
            width: 150px;
        }
        small {
            display: block;
            padding-top: 6px;
        }
        code {
            display: inline-block;
            border-radius: 2px;
            background-color: #ddd;
            padding: 4px 10px;
            border: 1px solid #888;
            color: #777;
        }
        code>b {
            color: #222;
        }
    </style>
</head>

<body>
    <form action="" method="get">
        <label>Expression : </label>
        <input type="text" name="expression" value="<?= $expression ?? "" ?>">
        <input type="submit" value="Evaluate">
        <?php
        if($value !== null && $expression  !== null)
            echo "👉 <b>" . $expression . "</b> = <b>" . $value . "</b>";
        ?>
    </form>

    <br>

    <table>
        <thead>
            <th>Arithmetic operators</th>
            <th>Modifiers</th>
            <th>Comparison</th>
            <th>Logic operators</th>
        </thead>
        <tr>
            <td>
                <label>Addition</label> <code>a <b>+</b> b</code><hr>
                <label>Substraction</label> <code>a <b>-</b> b</code><hr>
                <label>Multiplication</label> <code>a <b>*</b> b</code><hr>
                <label>Division</label> <code>a <b>/</b> b</code><hr>
                <label>Mod</label> <code>a <b>%</b> b</code><hr>
                <label>Power</label> <code>a <b>**</b> b</code><hr>
            </td>
            <td>
                <label>Minus</label> <code><b>-</b>a</code><hr>
                <label>Not</label> <code><b>!</b>a</code><hr>
                <label>Absolute</label> <code><b>|</b>a<b>|</b></code><hr>
                <label>Zero-safe</label> <code><b>?</b>a</code><hr>
                <label>To boolean</label> <code><b>!!</b>a</code><hr>
            </td>
            <td>
                <label>Equals</label> <code>a <b>=</b> b</code><hr>
                <label>Greater than</label> <code>a <b>></b> b</code><hr>
                <label>Greater or equals</label> <code>a <b>>=</b> b</code><hr>
                <label>Less than</label> <code>a <b><</b> b</code><hr>
                <label>Less or equals</label> <code>a <b><=</b> b</code><hr>
                <label>Compare</label> <code>a <b><=></b> b</code><hr>
            </td>
            <td>
                <label>And</label> <code>a <b>&&</b> b</code><hr>
                <label>Or</label> <code>a <b>||</b> b</code><hr>
                <label>Ternary</label> <code>a <b>?:</b> b</code><hr>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <th>Comparison functions</th>
            <th>Math functions</th>
            <th>Bit manipulation</th>
            <th>Other features</th>
        </thead>
        <tr>
            <td>
                <label>Min value</label> <code><b>min(a, b)</b></code><hr>
                <label>Max value</label> <code><b>max(a, b)</b></code><hr>
                <label>Clamp value</label> <code><b>clamp(a, min, max)</b></code>
                <small>Clamp value <b>a</b> to boundaries <b>min</b> and <b>max</b>.</small>
                <hr>
                <label>Swap value</label> <code><b>swap(val, a, b)</b></code>
                <small>If <b>val=a</b>, returns <b>b</b>. If <b>val=b</b>, returns <b>a</b>. Otherwise, <b>val</b> is returned.</small>
                <hr>
            </td>
            <td>
                <label>Square root</label> <code><b>sqrt(a)</b></code><hr>
                <label>Sinus</label> <code><b>sin(a)</b></code><hr>
                <label>Cosinus</label> <code><b>cos(a)</b></code><hr>
                <label>Tangent</label> <code><b>tan(a)</b></code><hr>
                <label>Arc tangent</label> <code><b>atan(a)</b></code><hr>
                <label>Exponent</label> <code><b>exp(a)</b></code><hr>
                <label>Natural logarithm</label> <code><b>log(a, exp)</b></code><hr>
            </td>
            <td>
                <label>To base 2</label> <code><b>bin(a)</b></code><hr>
                <label>Get bit at</label> <code><b>bit(val, pos)</b></code><hr>
                <label>Bitwise and</label> <code>a <b> & </b> b</code><hr>
                <label>Bitwise or</label> <code>a <b> | </b> b</code><hr>
                <label>Bitwise xor</label> <code>a <b> ^ </b> b</code><hr>
                <label>Bitwise not</label> <code><b>~</b>a</code><hr>
                <label>Left bit shift</label> <code>a <b> << </b> b</code><hr>
                <label>Right bit shift</label> <code>a <b> >> </b> b</code><hr>
            </td>
            <td>
                <label>Grouping</label> <code><b>(</b>a + b<b>)</b> * c</code><hr>
                <label>Random in range</label> <code><b>[</b>a, b<b>]</b></code>
                <small>Returns a random integer within the set boundaries. Boundary values are included.</small>
                <hr>
                <label>Random in set</label> <code><b>{</b>a, b, c<b>}</b></code>
                <small>Returns randomly one value defined in set.</small>
                <hr>
                <label>Weighted random</label> <code><b>{</b>a<b>=>x</b>, b<b>=>y</b>, c<b>}</b></code><br>
                <small>Returns value at random with weighted probabilities for each value. 
                    <br>Unweighted values are automatically set at 1.
                    <br>A weight of 2 have 2x more chances to be picked, a weight of 0.5 have 2x less chances of being picked.
                </small>
                <hr>
                <label>Custom variables</label> <code><b>PI</b> + <b>ten</b></code>
                <small>Values for custom variables can be passed to interpreter.</small>
                <hr>
                <label>Custom functions</label> <code><b>double(2)</b> + <b>d6</b></code>
                <small>Custom functions can be passed to the interpreter. Argument-less functions can be called without parentheses.</small>
                <hr>
            </td>
        </tr>
    </table>
</body>