<?php

namespace Clevyr\LaravelBehatDusk\Console;

class Helpers
{
    /**
     * @return string[][][][][]
     */
    public static function defaultBehatConfig(): array
    {
        return [
            'default' => [
                'suites' => [
                    'default' => [
                        'contexts' => ['FeatureContext'],
                    ],
                ],
            ],
        ];
    }
}
