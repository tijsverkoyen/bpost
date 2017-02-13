<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->notPath('src/tijsverkoyen_classes.php')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules(
        array(
            '@PSR2' => true,
            'no_extra_consecutive_blank_lines' => true,
        )
    )
    ->setFinder($finder)
;
