workflow "Sync Git" {
  on = "push"
  resolves = ["sync-plugin-docker",
    "sync-plugin-git",
    "sync-plugin-pages",
    "sync-plugin-storage-s3",
    "sync-plugin-storage-tencent-cos-v4",
    "sync-plugin-storage-tencent-cos-v5"
    ]
}

action "sync-plugin-docker" {
  uses = "docker://pcit/pages"
  secrets = ["PCIT_GIT_TOKEN"]
  env = {
    PCIT_USERNAME = "khs1994"
    PCIT_EMAIL = "khs1994@khs1994.com"
    PCIT_TARGET_BRANCH = "master"
    PCIT_GIT_URL = "github.com/pcit-ce/plugin-docker"
    PCIT_LOCAL_DIR = "plugins/docker"
    PCIT_KEEP_HISTORY = "1"
    PCIT_MESSAGE = "Sync from pcit-ce/pcit by PCIT"
  }
}

action "sync-plugin-git" {
  uses = "docker://pcit/pages"
  secrets = ["PCIT_GIT_TOKEN"]
  env = {
    PCIT_USERNAME = "khs1994"
    PCIT_EMAIL = "khs1994@khs1994.com"
    PCIT_TARGET_BRANCH = "master"
    PCIT_GIT_URL = "github.com/pcit-ce/plugin-git"
    PCIT_LOCAL_DIR = "plugins/git"
    PCIT_KEEP_HISTORY = "1"
    PCIT_MESSAGE = "Sync from pcit-ce/pcit by PCIT"
  }
}

action "sync-plugin-pages" {
  uses = "docker://pcit/pages"
  secrets = ["PCIT_GIT_TOKEN"]
  env = {
    PCIT_USERNAME = "khs1994"
    PCIT_EMAIL = "khs1994@khs1994.com"
    PCIT_TARGET_BRANCH = "master"
    PCIT_GIT_URL = "github.com/pcit-ce/plugin-pages"
    PCIT_LOCAL_DIR = "plugins/pages"
    PCIT_KEEP_HISTORY = "1"
    PCIT_MESSAGE = "Sync from pcit-ce/pcit by PCIT"
  }
}

action "sync-plugin-storage-s3" {
  uses = "docker://pcit/pages"
  secrets = ["PCIT_GIT_TOKEN"]
  env = {
    PCIT_USERNAME = "khs1994"
    PCIT_EMAIL = "khs1994@khs1994.com"
    PCIT_TARGET_BRANCH = "master"
    PCIT_GIT_URL = "github.com/pcit-ce/plugin-s3"
    PCIT_LOCAL_DIR = "plugins/storage/s3"
    PCIT_KEEP_HISTORY = "1"
    PCIT_MESSAGE = "Sync from pcit-ce/pcit by PCIT"
  }
}

action "sync-plugin-storage-tencent-cos-v4" {
  uses = "docker://pcit/pages"
  secrets = ["PCIT_GIT_TOKEN"]
  env = {
    PCIT_USERNAME = "khs1994"
    PCIT_EMAIL = "khs1994@khs1994.com"
    PCIT_TARGET_BRANCH = "master"
    PCIT_GIT_URL = "github.com/pcit-ce/plugin-tencent-cos-v4"
    PCIT_LOCAL_DIR = "plugins/storage/tencent_cos_v4"
    PCIT_KEEP_HISTORY = "1"
    PCIT_MESSAGE = "Sync from pcit-ce/pcit by PCIT"
  }
}

action "sync-plugin-storage-tencent-cos-v5" {
  uses = "docker://pcit/pages"
  secrets = ["PCIT_GIT_TOKEN"]
  env = {
    PCIT_USERNAME = "khs1994"
    PCIT_EMAIL = "khs1994@khs1994.com"
    PCIT_TARGET_BRANCH = "master"
    PCIT_GIT_URL = "github.com/pcit-ce/plugin-tencent-cos-v5"
    PCIT_LOCAL_DIR = "plugins/storage/tencent_cos_v5"
    PCIT_KEEP_HISTORY = "1"
    PCIT_MESSAGE = "Sync from pcit-ce/pcit by PCIT"
  }
}
