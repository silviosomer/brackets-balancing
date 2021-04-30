<?php
echo "<hr>";

/**
 * The information received from a POST request, in JSON format.
 * 
 * The variable $text_evaluate holds the content that will be evaluated.
 */
$text_evaluate = "";
if (isset($_POST['text_json'])) {
    $json = json_decode($_POST['text_json'], true);
    $text_evaluate = $json['text_evaluate'];
}

/**
 * If no sequence of brackets was received, a random one is generated a message 
 * is shown.
 * 
 * If a sequence of brackets was received, its content is copied to the variable
 * $brackets.
 */
$brackets = "";
switch ($text_evaluate) {
    case "":
        echo '<div class="success">No brackets sequence was provided. Generating 
        and analysing a random one.</div>';
    case "random":
        $brackets = str_repeat("(", rand(0, 10));
        $brackets .= str_repeat(")", rand(0, 10));
        $brackets .= str_repeat("[", rand(0, 10));
        $brackets .= str_repeat("]", rand(0, 10));
        $brackets .= str_repeat("{", rand(0, 10));
        $brackets .= str_repeat("}", rand(0, 10));
        $brackets = str_shuffle($brackets);
        break;
    default:
        $brackets = $text_evaluate;
        break;
}
$brackets_length = strlen($brackets);

// Shows the sequence of brackets on the input field.
echo "
<script>
    document.getElementById('text_evaluate').value = '" . $brackets . "';
</script>";

$open = array('(', '[', '{');
$close = array(')', ']', '}');

/**
 * Creates the arrays $brackets_open and $brackets_close. They will be used to
 * check if there are unmatched brackets, called orphans.
 * 
 * Creates the array $valid_brackets with all brackets, after excluding any
 * possible foreign characters.
 */
$valid_brackets = array();
$brackets_open = array();
$brackets_close = array();
for ($i = 0; $i < $brackets_length; $i++) {
    if (in_array($brackets[$i], $open)) {
        $brackets_open[$i] = $brackets[$i];
    } elseif (in_array($brackets[$i], $close)) {
        $brackets_close[$i] = $brackets[$i];
    }

    $current_character = substr($brackets, $i, 1);
    if (in_array($current_character, $open) || in_array($current_character, $close)) {
        $valid_brackets[$i] = $current_character;
    }
}

// Shows the string received, without filtering foreign characters.
$brackets_style = "<pre class='string'>string_received (count: $brackets_length) {\n   $brackets\n}</pre>";
echo $brackets_style;

// Shows the string received, with foreign characters filtered out.
$brackets_style = "<pre class='brackets'>valid_brackets (count: " . count($valid_brackets) . ") {\n";
foreach($valid_brackets as $bk => $bv) {
    $brackets_style .= "   $bv -- position: $bk \n";
}
$brackets_style .= "}</pre>";
echo $brackets_style;

// Shows only opening brackets, in order of appearance.
$brackets_style = "<pre class='brackets'>brackets_open_order (count: " . count($brackets_open) . ") {\n";
foreach($brackets_open as $bk => $bv) {
    $brackets_style .= "   $bv -- position: $bk \n";
}
$brackets_style .= "}</pre>";
echo $brackets_style;

// Shows only closing brackets, in order of appearance.
$brackets_style = "<pre class='brackets'>brackets_close_order (count: " . count($brackets_close) . ") {\n";
foreach($brackets_close as $bk => $bv) {
    $brackets_style .= "   $bv -- position: $bk \n";
}
$brackets_style .= "}</pre>";
echo $brackets_style;

/**
 * Analyses the sequence of brackets and verifies which ones match the rules:
 * - Rule 1 ("Pairs"): An opened bracket must be closed to be valid, which means 
 * they must form pairs;
 * - Rule 2 ("Scope"): A pair must have both parts inside another pair or outside 
 * any pair, which means that a pair opening inside another pair and closing 
 * outside of it is not valid.
 * 
 * Unmatched brackets are kept in the $orphans array. Matched brackets are kept 
 * only in the $brackets array.
 * 
 * The $temp array is used to stare temporarily the brackets being analyzed.
 */
$orphans = array();
$temp = array();
foreach($valid_brackets as $brackets_key => $brackets_value) {
    // Is the current bracket an opening one?
    $brackets_key_found = array_search($brackets_value, $open);

    // If it is, keep it in the $temp array.
    if ($brackets_key_found !== false) {
        $temp[$brackets_key] = $brackets_value;

    // If it isn't...
    } else {
        // 1. Find out which bracket matches it;
        $brackets_close_key = array_search($brackets_value, $close);
        $brackets_open_value = $open[$brackets_close_key];

        // 2. Search for the most recent match.
        $reversed = array_reverse($temp, true);
        $last_occurrence = array_search($brackets_open_value, $reversed);

        // If the match was found...
        if ($last_occurrence !== false) {
            // Remove it from the $temp array...
            unset($temp[$last_occurrence]);
            if (count($temp) > 0) {
                foreach ($temp as $k => $v) {
                    /**
                     * and remove existing brackets between the pair.
                     * 
                     * For example: Consider the sequence "{[((]".
                     * It has a matching pair: "[" and "]".
                     * Everything else is unmatched.
                     * 
                     * What we do here is to remove only what's inside the pair.
                     * When we got to this point, the $temp array had the following
                     * contents: "{((", because we already removed the "[" bracket.
                     * Here we will remove what was between "[" and "]", which was
                     * "((". As a result, $temp will have the following content: "{".
                     * 
                     * We kept this content because it might match some (possible)
                     * further bracket.
                     */
                    if ($k > $last_occurrence) {
                        $orphans[$k] = $v;
                        unset($temp[$k]);
                    }
                }
            }

        // If no match was found...
        } else {
            // it means this is an orphan bracket.
            $orphans[$brackets_key] = $brackets_value;
        }
    }
}

// If the $temp array has any content left, they are all orphans.
if (count($temp) > 0) {
    foreach ($temp as $k => $v) {
        $orphans[$k] = $v;
        unset($temp[$k]);
    }
}
ksort($orphans);

// Shows the orphan brackets.
if (count($orphans) > 0) {
    $brackets_style = "<pre class='brackets'>orphan_brackets (count: " . count($orphans) . ") {\n";
    foreach($orphans as $bk => $bv) {
        $brackets_style .= "   $bv -- position: $bk \n";
    }
    $brackets_style .= "}</pre>";
    echo $brackets_style;
}

if (count($orphans) > 0) {
    $aux_str = "";
    $brackets_style = "<div style='padding-left: 20px;'><span class='result'><pre>brackets_result_in_line (count: $brackets_length) {\n   ";
    for ($i = 0; $i < $brackets_length; $i++) {
        if (isset($orphans[$i])) {
            $aux_str .= '<span class="orphan-brackets">' . substr($brackets, $i, 1) . '</span>';
        } elseif (isset($orphans[$i])) {
            $aux_str .= '<span class="orphan-brackets">' . substr($brackets, $i, 1) . '</span>';
        } else {
            $aux_str .= '<span class="non-orphan-brackets">' . substr($brackets, $i, 1) . '</span>';
        }
    }
    $brackets_style .= $aux_str;
    $brackets_style .= "\n}</pre></span></div>";
    // Shows, in a line, the sequence of no valid brackets.
    echo $brackets_style;

    $aux_str = "";
    // $offset if used to control the indentation.
    $offset = 3;
    /**
     * $previous_open_close_move is used to keep track of which kind of bracket
     * was last shown. If its content is "o", it means it was an opening one.
     * If its content is "c", it means it was a closing one.
     * 
     * It is necessary, because we need to know when the $offset variable should
     * have its value subtracted, which means we have a closing bracket with some
     * other brackets inside it. 
     * 
     * For example:
     * {
     *    [((]( 
     * }
     * 
     * In this case, the "}" bracket needs its offset reduced to be shown correctly.
     */ 
    $previous_open_close_move = "o";
    $brackets_style = "<div style='padding-left: 20px;'><span class='result'><pre>brackets_result_with_visual_aid (count: $brackets_length) {\n   ";
    for ($i = 0; $i < $brackets_length; $i++) {
        if (isset($orphans[$i])) {
            $aux_str .= '<span class="orphan-brackets">' . substr($brackets, $i, 1) . '</span>';
        } elseif (isset($orphans[$i])) {
            $aux_str .= '<span class="orphan-brackets">' . substr($brackets, $i, 1) . '</span>';
        } else {
            if (in_array(substr($brackets, $i, 1), $open)) {
                $aux_str .= "\n" . str_repeat(" ", $offset) . "<span class='non-orphan-brackets'>" . substr($brackets, $i, 1) . "</span>";
                $offset += 3;
                $previous_open_close_move = "o";
            }

            if (in_array(substr($brackets, $i, 1), $close)) {
                $offset -= 3;
                if ($previous_open_close_move == "c") {
                    $aux_str .= "\n" . str_repeat(" ", $offset) . "<span class='non-orphan-brackets'>" . substr($brackets, $i, 1) . "</span>";
                } else {
                    $aux_str .= "<span class='non-orphan-brackets'>" . substr($brackets, $i, 1) . "</span>";
                }
                $previous_open_close_move = "c";
            }
        }
    }
    $brackets_style .= $aux_str;
    $brackets_style .= "\n}</pre></span></div>";
    // Shows, with visual aid, the sequence of no valid brackets.
    echo $brackets_style;
}

if (count($orphans) == 0) {
    echo '
    <div class="alert alert-success evaluation" role="alert">
        The sequence of brackets received is valid.
    </div>';
} else {
    echo '
    <div class="alert alert-danger evaluation" role="alert">
        The sequence of brackets received is not valid.
    </div>';
}
?>
