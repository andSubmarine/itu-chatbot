<?php

return PhpCsFixer\Config::create()
->setFinder(PhpCsFixer\Finder::create()->in(__DIR__.'/app'))
->setUsingCache(false)
->setIndent('    ')
->setRules([
    '@PSR2' => true,
    'align_multiline_comment' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'no_blank_lines_before_namespace' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_null_property_initialization' => true,
    'no_short_echo_tag' => true,
    'no_unused_imports' => true,
    'ordered_imports' => true,
    'single_quote' => true,
    'ternary_operator_spaces' => true,
    'ternary_to_null_coalescing' => true,
    'trailing_comma_in_multiline_array' => true,
]);
