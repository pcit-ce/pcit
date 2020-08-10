<?php

//
// $ composer global require friendsofphp/php-cs-fixer
//
// $ php-cs-fixer fix
//
// @link https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = PhpCsFixer\Finder::create()
    ->exclude('/build')
    ->exclude('cache')
    ->exclude('vendor')
    ->exclude('node_modules')

    // remove when support attribute
    ->exclude('app/Http/Controllers')
    ->notPath('src/Framework/src/Support/Facades/Route.php')

    // ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
      '@Symfony' => true,
      '@Symfony:risky'=> true,
      '@PhpCsFixer' => true,
      'array_syntax' => array('syntax' => 'short'),
      'ordered_imports' => true,
      'declare_strict_types' => true, // @PHP70Migration:risky, @PHP71Migration:risky
      'ternary_to_null_coalescing' => true, // @PHP70Migration, @PHP71Migration
      'void_return' => true, // @PHP71Migration:risky
      'visibility_required' => true,
      'simplified_null_return' => true,
      'method_chaining_indentation' =>true,
      'phpdoc_no_empty_return' => true,
      'phpdoc_order' => true,
      'align_multiline_comment' => true,
      'array_indentation' => true,
      'return_assignment' => true,
      'phpdoc_add_missing_param_annotation' => true,
      'multiline_whitespace_before_semicolons' => true,
      'multiline_comment_opening_closing' => true,
      'compact_nullable_typehint' => true,
      'php_unit_method_casing' => false,
      'php_unit_internal_class' => false,
      'php_unit_ordered_covers' => false,
      'php_unit_test_class_requires_covers' => false,
      'escape_implicit_backslashes' => false,
      'explicit_indirect_variable'=> false,
      'explicit_string_variable' => false,
      'heredoc_to_nowdoc'=> false,
      'ordered_class_elements' => false,
      'simple_to_complex_string_variable' => false,
    ])
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
