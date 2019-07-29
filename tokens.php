<?php
require __DIR__.'/vendor/autoload.php';

use BedMaker\Code\Rule\ClassMethodRule;

/*
print_r(get_defined_constants());
exit(0);

$tokenConstants = array_filter(
    get_defined_constants(),
    function ($value, $key) {
        return substr($key, 0, 2) === 'T_';
    },
    ARRAY_FILTER_USE_BOTH
);

echo 'Token                          | Value' . PHP_EOL;
echo '------------------------------ | -----' . PHP_EOL;
*/

$tokenConstants = get_defined_constants();
$tokenConstantsByCode = [];

foreach ($tokenConstants as $key => $value) {
    if (is_int($value)) {
        $tokenConstantsByCode[$value] = $key;
    }
}

/*
foreach ($tokenConstants as $tokenConstant => $value) {
    printf("%-30s | %s\n", $tokenConstant, $value);
}
*/

dd(preg_split('/<\?php/i', $source));

$tokens = token_get_all($source);

$resTokens = [];
foreach ($tokens as $token) {
    if (isset($tokenConstantsByCode[$token[0]])) {
        $hToken =& $resTokens[];
        $hToken = $token;
        $hToken[0] = $tokenConstantsByCode[$token[0]] . '::' . $token[0];
    } else {
        print_r($token);
    }
}

print_r($resTokens);

echo "\n";
