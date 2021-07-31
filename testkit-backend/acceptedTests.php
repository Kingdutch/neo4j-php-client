<?php

declare(strict_types=1);

/*
 * This file is part of the Laudis Neo4j package.
 *
 * (c) Laudis technologies <http://laudis.tech>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'neo4j' => [
        'authentication' => [
            'TestAuthenticationBasic' => [
                'testSuccessOnProvideRealmWithBasicToken' => true,
                'testSuccessOnBasicToken' => true,
                'testErrorOnIncorrectCredentials' => true,
            ],
        ],
        'datatypes' => [
            'TestDataTypes' => [
                'test_should_echo_back' => true,
            ],
        ],
    ],
];
