<?php

//
// $ composer global require friendsofphp/php-cs-fixer
//
// $ php-cs-fixer fix
//
// @link https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = PhpCsFixer\Finder::create()
    ->exclude('/build')
    ->exclude('.idea')
    ->exclude('.psalm')
    ->exclude('/frontend')
    ->exclude('cache')
    ->exclude('vendor')
    ->exclude('node_modules')

    // remove when support attribute
    ->notPath('app/Http/Controllers/Builds/BuildsController.php')
    //->exclude('src/Framework/src/Attributes')

    ->exclude('framework/storage')
    ->exclude('app/Http/Controllers/Test')

    // ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();
return $config->setRules([
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
      'phpdoc_order_by_value' => false,
      'php_unit_test_class_requires_covers' => false,
      'escape_implicit_backslashes' => false,
      'explicit_indirect_variable'=> false,
      'explicit_string_variable' => false,
      'heredoc_to_nowdoc'=> false,
      'ordered_class_elements' => false,
      'simple_to_complex_string_variable' => false,
      'phpdoc_to_comment' => false,
      'single_line_comment_style' => false,
    ])
    ->setCacheFile(__DIR__.'/cache/php_cs')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
