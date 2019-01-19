# PCIT Plugins

## 插件分为官方插件和自定义插件

### 官方插件

通过 `settings` 指令进行配置，必须配置 `provider` 指令。

### 自定义插件

通过 `environment` 指令进行配置。

## 如何开发一个插件

* Fork 本项目并克隆到本地

* 在 `plugins` 目录中新建一个文件夹，名称为插件名称。

```
.
├── .env.example
├── composer.json
├── Dockerfile
├── index.php
└── README.md

```

在 `.env.example` 中列出环境变量

## 插件列表

| Plugin                                                               | Author |
| :------------------------------------------------------------------- | :----- |
| [Tencent Cloud COS v4](https://github.com/tencentyun/cos-php-sdk-v4) | @PCIT  |
| [Tencent Cloud COS v5](https://github.com/tencentyun/cos-php-sdk-v5) | @PCIT  |
| [Email](https://github.com/PHPMailer/PHPMailer)                      | @PCIT  |
| [GitHub Pages](https://github.com/pcit-ce/pcit)                      | @PCIT  |
| [GitHub Release](https://github.com/pcit-ce/pcit)                    | @aktau |
