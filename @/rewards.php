<?php

$rewards = [
    ["label" => "Gold Plan", "value" => 1, "bgColor" => "#45007b", "textColor" => "#fff"],
    ["label" => "Nothing", "value" => 1, "bgColor" => "#070d19", "textColor" => "#fff"],
    ["label" => "Home2 Plan", "value" => 1, "bgColor" => "#45007b", "textColor" => "#fff"],
    ["label" => "Nothing", "value" => 1, "bgColor" => "#070d19", "textColor" => "#fff"],
    ["label" => "Diamond Plan", "value" => 1, "bgColor" => "#45007b", "textColor" => "#fff"],
    ["label" => "Nothing", "value" => 1, "bgColor" => "#070d19", "textColor" => "#fff"],
    ["label" => "5$ balance", "value" => 1, "bgColor" => "#45007b", "textColor" => "#fff"],
    ["label" => "Nothing", "value" => 1, "bgColor" => "#070d19", "textColor" => "#fff"],
    ["label" => "10$ balance", "value" => 1, "bgColor" => "#45007b", "textColor" => "#fff"],
    ["label" => "Nothing", "value" => 1, "bgColor" => "#070d19", "textColor" => "#fff"],
    ["label" => "15$ balance", "value" => 1, "bgColor" => "#45007b", "textColor" => "#fff"],
    ["label" => "Nothing", "value" => 1, "bgColor" => "#070d19", "textColor" => "#fff"]
];

$jsonData = json_encode($rewards);

header('Content-Type: application/json');

echo $jsonData;
?>