<?php

declare(strict_types=1);

namespace KhsCI\Service\Checks;

class RunData
{
    public $repo_full_name;
    public $name;
    public $branch;
    public $commit_id;
    public $details_url;
    public $external_id;
    public $status;
    public $started_at = null;
    public $completed_at = null;
    public $conclusion = null;
    public $title = null;
    public $summary = null;
    public $text = null;
    public $annotations = null;
    public $images = null;
    public $actions = null;
    public $check_run_id;

    public function __construct(string $repo_full_name,
                                string $name,
                                string $branch,
                                string $commit_id,
                                string $details_url,
                                string $external_id,
                                string $status,
                                int $started_at = null,
                                int $completed_at = null,
                                string $conclusion = null,
                                string $title = null,
                                string $summary = null,
                                string $text = null,
                                array $annotations = null,
                                array $images = null,
                                array $actions = null)
    {
        $this->repo_full_name = $repo_full_name;
        $this->name = $name;
        $this->branch = $branch;
        $this->commit_id = $commit_id;
        $this->details_url = $details_url;
        $this->external_id = $external_id;
        $this->status = $status;
        $this->started_at = $started_at;
        $this->completed_at = $completed_at;
        $this->conclusion = $conclusion;
        $this->title = $title;
        $this->summary = $summary;
        $this->text = $text;
        $this->annotations = $annotations;
        $this->images = $images;
        $this->actions = $actions;
    }

    /**
     * @param string $filename      Required. The name of the file to add an annotation to.
     * @param string $blog_href     Required. The file's full blob URL.
     * @param int    $start_line    Required. The start line of the annotation.
     * @param int    $end_line      Required. The end line of the annotation.
     * @param string $warning_level Required. The warning level of the annotation. Can be one of notice, warning, or
     *                              failure.
     * @param string $message       Required. A short description of the feedback for these lines of code. The maximum
     *                              size is 64 KB.
     * @param string $title         The title that represents the annotation. The maximum size is 255 characters.
     * @param string $raw_details   Details about this annotation. The maximum size is 64 KB.
     *
     * @return array
     */
    public static function createAnnotation(string $filename,
                                            string $blog_href,
                                            int $start_line,
                                            int $end_line,
                                            string $warning_level,
                                            string $message,
                                            string $title = null,
                                            string $raw_details = null)
    {
        return [
            'filename' => $filename,
            'blog_href' => $blog_href,
            'start_line' => $start_line,
            'end_line' => $end_line,
            'warning_level' => $warning_level,
            'message' => $message,
            'title' => $title,
            'raw_details' => $raw_details,
        ];
    }

    /**
     * @param string $alt       Required. The alternative text for the image.
     * @param string $image_url Required. The full URL of the image.
     * @param string $caption   a short image description
     *
     * @return array
     */
    public static function createImage(string $alt,
                                       string $image_url,
                                       string $caption)
    {
        return [
            'alt' => $alt,
            'image_url' => $image_url,
            'caption' => $caption,
        ];
    }

    /**
     * @param string $label
     * @param string $identifier
     * @param string $description
     *
     * @return array
     *
     * @see https://developer.github.com/changes/2018-05-23-request-actions-on-checks/
     */
    public static function createAction(string $label = 'Fix',
                                        string $identifier = 'fix_errors',
                                        string $description = 'Allow us to fix these errors for you')
    {
        return [
            'label' => $label,
            'identifier' => $identifier,
            'description' => $description,
        ];
    }
}
