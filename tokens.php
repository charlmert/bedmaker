<?php
require __DIR__.'/vendor/autoload.php';

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
                
                select q.requests, c.*, b.name as branch_name, o.name as organisation_name, client.surname as client_surname,
                client.title as client_title, q.first_user, q.first_created_date, u.name||' '||u.surname as first_user_name
                from cc_card c
                left join client client on client.id = c.client_id
                left join organisation_branch b on b.id = c.organisation_branch_id
                left join organisation o on o.id = b.organisation_id
                left join(
                select c.cc_card_id, string_agg(t.rfq_number,',') as requests, min(t.organisation_user_id) as first_user,
                min(t.created_date) as first_created_date
                from tender t, cc_transaction_queue q, cc_transaction_card c
                where t.id = q.tender_id
                and q.id = c.cc_transaction_queue_id
                and t.status > 1
                and q.status > 1
                and t.classification = 'claimscard'
                and c.cc_card_id is not null
                and t.id is not null
                and q.id is not null
                group by c.cc_card_id
                ) q on q.cc_card_id = c.id
                left join organisation_user u on u.id = q.first_user
                where  b.primary_organisation_id = {$db->bind($primaryOrganisationId)}
                and ((c.status = 2 or c.status = 3) and c.status is not null)
                order by pin_mailer asc

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
