<?php
return [
    'rules' => [
        'file' => [
            'name' => [
                'case' => 'studly',
             ],
             'rename' => 'class'
        ],
        'class' => [
            'name' => [
                'case' => 'studly',
                'rename' => 'class',
            ],
            'namespace' => 'ClaimsCardSoap\\Services'
        ],
        'function' => [
            'case' => 'camel',
        ],
        'variable' => [
            'case' => 'camel'
        ],
    ],
];
