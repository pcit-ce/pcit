module.exports = {
  env: {
    browser: true,
    node: true,
    es6: true,
    jquery: true,
  },
  extends: ['eslint:recommended', 'plugin:prettier/recommended'],
  parser: 'babel-eslint',
  parserOptions: {
    // ecmaVersion: 2015,
    ecmaVersion: 9,
    impliedStrict: true,
    sourceType: 'module',
  },
  rules: {
    indent: [
      'error',
      2,
      {
        SwitchCase: 1,
        FunctionDeclaration: {
          parameters: 2,
          body: 1,
        },
        ObjectExpression: 1,
      },
    ],
    'linebreak-style': ['error', 'unix'],
    quotes: ['error', 'single'],
    semi: ['error', 'always'],
    'no-console': 1,
    strict: 2,
    'prettier/prettier': ['error', { trailingComma: 'all' }],
  },
  plugins: [],
};
