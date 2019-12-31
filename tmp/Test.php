<?php

print("A" . '_' . "B");

$MAP['A' . '_' . '1'] = "aa";
$MAP['A' . '_' . '2'] = "aaa";
$MAP['B' . '_' . '1'] = "bb";

var_dump($MAP);

foreach ($MAP as $key => $value) {
    print($key);
    print($value);
    print("\n");
}

$findKeys = array_keys($MAP, "aa");
if(count($findKeys) > 0) {
    $key = $findKeys[0];
}

$user_id = explode('_', $key)[0];
print($user_id . "\n");

unset($MAP[$key]);

var_dump($MAP);
?>