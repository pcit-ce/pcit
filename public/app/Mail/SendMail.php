<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: SYSTEM
 * Date: 2018/8/9
 * Time: 10:54.
 */

namespace App\Mail;

class SendMail
{
    public static function handle(): void
    {
        if (!Env::get('CI_EMAIL_PASSWORD', false)) {
            Log::debug(__FILE__, __LINE__, 'mail settings not found, send Mail skip', [], Log::INFO);

            return;
        }

        $committer_email = Build::getCommitterEmail((int) $this->build_key_id);
        $committer_name = Build::getCommitterName((int) $this->build_key_id);

        $repo_full_name = Repo::getRepoFullName((int) $this->rid, $this->git_type);
        $build_status_changed = Build::buildStatusIsChanged((int) $this->rid, $this->branch);

        $email_list = [];
        $on_success = null;
        $on_failure = null;

        $config = (object) json_decode($this->config, true);

        $email = $config->notifications['email'] ?? null;
        if ($email) {
            // email 指令存在
            $recipients = $email['recipients'] ?? null;

            if ($recipients) {
                // recipients 指令存在
                $on_success = $email['on_success'] ?? null;
                $on_failure = $email['on_failure'] ?? null;
                \is_array($recipients) && $email_list = $recipients;
                \is_string($recipients) && $email_list = [$recipients];
            } else {
                // email 指令只包含 email 列表
                $email_list = $email;
            }
        }

        if (CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $this->build_status) {
            // 构建成功
            if ('never' === $on_success) {
                return;
            }
        } else {
            // 构建失败
            if ('never' === $on_failure) {
                return;
            }
        }

        if (!(null === $on_success && null === $on_failure && $build_status_changed)) {
            return;
        }

        $subject = ucfirst($this->build_status).' : '.$repo_full_name.'#'.
            $this->build_key_id.' ('.$this->branch.'-'.substr($this->commit_id, 0, 7).')';

        $body = ''.$subject;

        $address = $email_list;

        $address = array_merge($address, [$committer_email => $committer_name]);

        Mail::send(array_unique($address), $subject, $body);
    }
}
