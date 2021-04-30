<!DOCTYPE html>
<html lang="en">
<head>
    <title>Brackets Balancing Evaluator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/form-validation.js"></script>
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/balancing.css">
    <script type="text/javascript" src="js/balancing.js"></script>
</head>

<body>
<div class="card center">
    <div class="card-header">
        <strong>Brackets Balancing Evaluator</strong><br/>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="text_evaluate">Submit a sequence to be evaluated:</label><br>
            <input type="text" class="form-control big-text" id="text_evaluate" 
            name="text_evaluate" placeholder="Brackets sequence"><br>
        </div>
        <button type="submit" class="btn btn-primary" onClick="textEvaluate('provided')">Submit</button>
        <button type="submit" class="btn btn-primary" onClick="textEvaluate('random')">Random</button>
    </div>
    <hr>
    <div class="message">
        <b>Rules to have balanced brackets</b><br/>&nbsp;<br/>

        1. "Pairs": An opened bracket must be closed to be valid, which means they must form pairs;<br/>
        2. "Scope": A pair must have both parts inside another pair or outside any pair, which means 
        that a pair opening inside another pair and closing outside of it is not valid.
        <br/>&nbsp;<br/>
        <b>About the results</b><br/>&nbsp;<br/>

        1. Brackets that follow the rules (valid ones) are marked in blue;<br/>
        2. Brackets that do not follow the rules (not valid ones) are marked in red.
    </div>
    <div id="textEvaluate"></div>
</div>
<br/>&nbsp;<br/>
</body>

</html>
