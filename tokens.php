<?php


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

$source = <<<'EOF'
    <?php
class CardBalanceReport extends som_extend implements some_impl1, some_impl2, some_impl3
{

}

$thingy = "$thing1";

EOF;

$source = <<<'EOF'
    <?php
        $query = "
                Select distinct Salary from Employee e1 where 2=Select count(distinct Salary) from Employee e2 where e1.salary<=e2.salary;
        ";

EOF;



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
