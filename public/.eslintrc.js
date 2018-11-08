module.exports = {
  env: {
    browser: true,
    node: true,
    es6: true,
    jquery: true
  },
  extends: 'eslint:recommended',
  parserOptions: {
    // ecmaVersion: 2015,
    ecmaVersion: 7,
    impliedStrict: true,
    sourceType: 'script'
  },
  rules: {
    indent: [
      'error',
      2,
      {
        SwitchCase: 1,
        FunctionDeclaration: {
          parameters: 2,
          body: 1
        },
        ObjectExpression: 1
      }
    ],
    'linebreak-style': ['error', 'unix'],
    quotes: ['error', 'single'],
    semi: ['error', 'always'],
    'no-console': 'off',
    strict: 2
  },
  plugins: []
};
