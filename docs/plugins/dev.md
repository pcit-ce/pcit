# 如何开发一个插件

新建一个文件夹，名称为插件名称。文件结构如下：

```
.
├── .env.example
├── composer.json
├── Dockerfile
├── index.php
└── README.md

```

在 `.env.example` 中列出环境变量，环境变量名称 **必须** 为大写且必须以 `PCIT_` 为前缀，例如 `PCIT_USERNAME`
