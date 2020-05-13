# PCIT Plugin -- Demo

[![Docker Stars](https://img.shields.io/docker/stars/pcit/demo.svg)](https://hub.docker.com/r/pcit/demo) [![Docker Pulls](https://img.shields.io/docker/pulls/pcit/demo.svg)](https://hub.docker.com/r/pcit/demo)

PCIT 插件与 [Actions](https://github.com/actions) 兼容。

```diff
steps:
  plugin:
-    image: pcit/demo
+  - uses: docker://pcit/demo

-    settings:
-      var: 'var'
-      var_array:
-      - a
-      - b
-      var_obj:
-        k1: v1
-        k2: v2
+    with:
+      var: 'var'
+      var_array: a,b
+      var_obj: '{"k1":"v1","k2":"v2"}'
```
