<?php 
    include_once("./ExpressionParser/ExpressionParser.php"); 
    
    $expression = null;
    $value = null;
    
    if(isset($_GET['expression'])) {
        $expression = $_GET['expression'];
        $value = evaluate($expression);
    }

    function evaluate($expression) {
        $parser = new ExpressionParser();
        
        $userDefined = [
            "PI" => M_PI,
            "ten" => 10,
            "double" => function($value) { return $value * 2; },
            "d6" => function() { return rand(1, 6); },
        ];

        try {
            return $parser->evaluate($expression, $userDefined);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
?>

<head>
    <style>
        * {
            font-family: monospace;
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
                    <br>If total of probabilities < 100, probabilities are readjusted to be 100 total.
                    <br>Unweighted values are automatically adjusted to be 100 total. However, if total of weighted values is already over 100, unweighted values are set to 100.
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