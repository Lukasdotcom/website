<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Truth Tree
    </title>
    <?php
    $DESCRIPTION = "This just contains a simple quiz that helps you decompose a truth tree.";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";
    ?>
    <script type='text/javascript' src='index.js'></script>
    <h1>Truth Tree Quiz</h1>
    <p>Notes for all the logic symbols:</p>
    <ul>
        <li>Negation: ~</li>
        <li>Conjuction(and): *</li>
        <li>Disjunction(or): v</li>
        <li>Conditional(if): →</li>
        <li>Biconditional(if and only if): ↔</li>
    </ul>
    <h3>Click on the first logical operation that should be decomposed in a truth tree.</h3>
    <label for="firstOperationComplexity">Complexity of Sentence: </label><input id='firstOperationComplexity' value='4' type='number'>
    <button id='firstOperationGenerate' onClick='firstOperation()'>Generate Sentence</button>
    <p id='firstOperation'></p>
    </div>
</body>

</html>