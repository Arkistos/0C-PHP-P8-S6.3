<?php

// https://cs.symfony.com/doc/rules/
$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'no_alternative_syntax' => ['fix_non_monolithic_code' => false],
        'echo_tag_syntax' => ['format' => 'short'],
    ])
;

return $config;
