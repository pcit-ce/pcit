module.exports = {
  title: 'PCIT',
  description: '国内首个基于 GitHub Checks API 使用 PHP 编写的开源持续集成/持续部署 (CI/CD) 系统',
  head: [
    []
  ],
  themeConfig: {
    nav: [{
        text: '安装',
        link: '/install/',
      },
      {
        text: '用法',
        link: '/usage/',
      },
      {
        text: '插件',
        link: '/plugins/',
      },
      {
        text: '示例',
        link: '/examples/'
      },
      {
        text: 'API',
        link: '/api/',
      },
      {
        text: 'CLI',
        link: '/cli/',
      },
      {
        text: 'CHANGELOG',
        link: 'https://github.com/pcit-ce/pcit/blob/master/CHANGELOG.md',
      },
      {
        text: '官方主页',
        link: 'https://ci.khs1994.com'
      }
    ],
    sidebar: {
      '/install/': [
        'ce',
        'ee',
        'env',
      ],
      '/usage/': [
        'language',
        'clone',
        'workspace',
        'steps',
        'special-step',
        'services',
        'jobs',
        'cache',
        'branches',
        'image',
        'networks',
        'system',
        'platform',
        'skip',
        'system_env',
        'special-command.md',
        'toolkit',
      ],
      '/plugins/': [

      ],
      '/examples/': [
        'php',
        'node_js',
        'java',
      ],
      '/api/': [
        'getting-help',
        'getting-started',
        'authentication',
        'builds/',
        'builds/job',
        'builds/log',
        'builds/artifact',
        'orgs/',
        'repo/',
        'repo/activate',
        'repo/branches',
        'repo/caches',
        'repo/crons',
        'repo/env_vars',
        'repo/requests',
        'repo/settings',
        'repo/star',
        'repo/issues',
        'user/',
        'system/',
        'sdk',
      ],
      '/cli/': [
         'tencent-ai-cli/'
      ],
    },
    repo: 'pcit-ce/pcit',
    docsDir: 'docs',
    editLinks: true,
    docsBranch: 'master'
  }
}
