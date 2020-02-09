# PCIT 组件

## Framework

Web 框架

## git

## Kernel

PCIT 核心

## Log

## provider

## notification

## Runner

# 组件图示

```
                 ---------------------------------
                 |             PCIT              |      
                 |                               |      ----------------
                 | webhooks                      |  --> | notification |
------------     |  server                       |      ----------------
| provider | --> |                               |
------------     |                               |      -----------------
                 |                               |  --> | GitHub Checks |
                 |                               |      -----------------
------------     |                               |
|   git    | --> |            kernel             |
------------     |           Framework           |
                 |                               |
                 |                    plugins    |
                 |                               |
                 |  PCITD             OpenAPI    |
                 |-------------------------------| 
                      ↑↓            ↑         ↑
                  ----------      ------   -------
                  | runner |      | UI |   | CLI |
                  ----------      ------   -------
                      ↑
             ---------------------
             | Docker Kubernetes |
             ---------------------
```
