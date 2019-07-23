<?php

$tokenConstants = array_filter(
    get_defined_constants(),
    function ($value, $key) {
        return substr($key, 0, 2) === 'T_';
    },
    ARRAY_FILTER_USE_BOTH
);

echo 'Token                          | Value' . PHP_EOL;
echo '------------------------------ | -----' . PHP_EOL;

$tokenConstantsByCode = [];

foreach ($tokenConstants as $key => $value) {
    $tokenConstantsByCode[$value] = $key;
}

/*
foreach ($tokenConstants as $tokenConstant => $value) {
    printf("%-30s | %s\n", $tokenConstant, $value);
}
*/

$source = <<<'EOF'
    <?php
    class some_name extends parent_name
    {
        private someVar = null;
    }
EOF;

$tokens = token_get_all($source);

$resTokens = [];
foreach ($tokens as $token) {
    if (isset($tokenConstantsByCode[$token[0]])) {
        $hToken =& $resTokens[];
        $hToken = $token;
        $hToken[0] = $tokenConstantsByCode[$token[0]] . '::' . $token[0];
    }
}

print_r($resTokens);

echo "\n";
