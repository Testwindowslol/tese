<?php
header("Content-Type: text/css");

// Margin & Padding auto-generation

$sides = ["right", "left", "bottom", "top"];

for ($si = 0; $si < sizeof($sides); $si++) {
    $side = $sides[$si];
    $sl = substr($side, 0, 1);

    for ($i = 2; $i <= 24; $i += 2) {
        echo ".m" . $sl . "-" . $i . " {\n";
        echo "  margin-" . $side . ": " . $i . "px\n";
        echo "}\n\n";

        echo ".p" . $sl . "-" . $i . " {\n";
        echo "  padding-" . $side . ": " . $i . "px\n";
        echo "}\n\n";
    }
}
?>

.flex {
    display: flex;
}

.flex-list {
    display: flex;
    width: 100%;
    background: red;
}

.background-warning {
    background: var(--warning) !important;
}

.background-danger {
    background: var(--danger) !important;
}

.background-success {
    background: var(--success) !important;
}

.visible {
    visibility: visible;
}

.hidden {
    visibility: hidden;
}