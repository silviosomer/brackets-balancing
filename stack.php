<?php
echo "<hr>";

$text_evaluate = "";
if (isset($_POST['text_json'])) {
    $json = json_decode($_POST['text_json'], true);
    $text_evaluate = $json['text_evaluate'];
}

$brackets = "";
switch ($text_evaluate) {
    case "":
        echo '<div class="success">No brackets sequence was provided. Generating and analysing a random one.</div>';
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

echo "
<script>
    document.getElementById('text_evaluate').value = '" . $brackets . "';
</script>";

$open = array('(', '[', '{');
$close = array(')', ']', '}');

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

$brackets_style = "<pre class='string'>string_received (count: $brackets_length) {\n   $brackets\n}</pre>";
echo $brackets_style;

$brackets_style = "<pre class='brackets'>valid_brackets (count: " . count($valid_brackets) . ") {\n";
foreach($valid_brackets as $bk => $bv) {
    $brackets_style .= "   $bv -- position: $bk \n";
}
$brackets_style .= "}</pre>";
echo $brackets_style;

$brackets_style = "<pre class='brackets'>brackets_open_order (count: " . count($brackets_open) . ") {\n";
foreach($brackets_open as $bk => $bv) {
    $brackets_style .= "   $bv -- position: $bk \n";
}
$brackets_style .= "}</pre>";
echo $brackets_style;

$brackets_style = "<pre class='brackets'>brackets_close_order (count: " . count($brackets_close) . ") {\n";
foreach($brackets_close as $bk => $bv) {
    $brackets_style .= "   $bv -- position: $bk \n";
}
$brackets_style .= "}</pre>";
echo $brackets_style;

$orphans = array();
$temp = array();
foreach($valid_brackets as $brackets_key => $brackets_value) {
    $brackets_key_found = array_search($brackets_value, $open);
    if ($brackets_key_found !== false) {
        $temp[$brackets_key] = $brackets_value;
    } else {
        $brackets_close_key = array_search($brackets_value, $close);
        $brackets_open_value = $open[$brackets_close_key];

        $reversed = array_reverse($temp, true);
        $last_occurrence = array_search($brackets_open_value, $reversed);
        if ($last_occurrence !== false) {
            unset($temp[$last_occurrence]);
            if (count($temp) > 0) {
                foreach ($temp as $k => $v) {
                    if ($k > $last_occurrence) {
                        $orphans[$k] = $v;
                        unset($temp[$k]);
                    }
                }
            }
        } else {
            $orphans[$brackets_key] = $brackets_value;
        }
    }
}
if (count($temp) > 0) {
    foreach ($temp as $k => $v) {
        $orphans[$k] = $v;
        unset($temp[$k]);
    }
}
ksort($orphans);

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
    echo $brackets_style;

    $aux_str = "";
    $offset = 3;
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
