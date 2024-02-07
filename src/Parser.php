<?php

namespace Voog;

use DateTime;

class Parser
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getTitle()
    {
        // Use regex to match the <h1> tag and extract its content
        if (preg_match('/<h1>(.*?)<\/h1>/s', $this->data['text'], $matches)) {
            return $this->removeHtml($matches[1]);
        }

        return null; // Return null if no <h1> tag is found
    }

    /**
     * @return DateTime|false
     */
    public function getDate()
    {
        // Create a DateTime object from the original date string
        return DateTime::createFromFormat('Y-m-d H:i:s', $this->data['time']);
    }

    public function getContent()
    {
        // Optionally, you could remove the title from the content if needed
        $contentWithoutTitle = preg_replace('/<h1>.*?<\/h1>/', '', $this->data['text'], 1);

        // Return the content, here assuming everything in "text" except the title is considered content
        return $contentWithoutTitle;
    }

    public function getFirstImageName()
    {
        preg_match('/<img.*?src="[^"]*\/([^\/"]+\.(jpg|JPG|jpeg|JPEG|png|PNG))"/s', $this->data['text'], $matches);

        if ($matches) {
            return ($matches[1]);
        }
    }

    public function getReplacedImageUrls()
    {
        $baseURL = "https://www.baltdefcol.org";
        $content = $this->getContent(); // Assuming this retrieves the HTML content

        // Replace <a> tags with href="/files/ or href="files/ with the correct full URL
        $contentWithFixedLinks = preg_replace_callback(
            '/<a href="(\/files|files)\/([^"]+)"/',
            function ($matches) use ($baseURL) {
                // Construct the full URL by appending the relative path to the base URL
                $fullUrl = $baseURL . '/' . ltrim($matches[2], '/');
                return '<a href="' . $fullUrl . '"';
            },
            $content
        );

        // Remove <img> tags with paths that navigate up directories
        $contentWithoutInvalidImages = preg_replace('/<img.*?src="(\.\.\/)+[^"]+\.(jpg|JPG|jpeg|JPEG|png|PNG)".*?>/s', '', $contentWithFixedLinks);

        // Further remove <img> tags specifically navigating up to the uploads directory
        $contentWithoutInvalidImages = preg_replace('/<img.*?src="(\.\.\/){4}uploads[^"]*".*?>/s', '', $contentWithoutInvalidImages);

        // Use preg_replace_callback to find and replace all valid image src attributes
        $modifiedContent = preg_replace_callback(
            '/<img.*?src="(\/[^"]+\.(jpg|JPG|jpeg|JPEG|png|PNG))"/s',
            function ($matches) use ($baseURL) {
                // Normalize the URL by ensuring no double slashes except for protocol://
                $normalizedSrc = preg_replace('/([^:])\/\//', '$1/', $matches[1]);
                // Prepend the base URL to the src and return the modified <img> tag
                $fullUrl = $baseURL . $normalizedSrc;
                return '<img src="' . $fullUrl . '"';
            },
            $contentWithoutInvalidImages
        );

        $modifiedContent = str_replace('pdf"">', 'pdf">', $modifiedContent);
        $modifiedContent = str_replace('.pdf""', '.pdf"', $modifiedContent);

        return $modifiedContent; // Return the modified content
    }

    public function getText()
    {
        return $this->data['text'];
    }

    private function removeHtml($string)
    {
        return strip_tags(trim($string));
    }
}
