<?php

namespace Voog;

class VoogApi
{
    public mixed $voogUrl;
    private mixed $token;

    public function __construct($voogUrl, $token)
    {
        $this->voogUrl = $voogUrl;
        $this->token = $token;
    }

    private function request($type, $uri, $headers = [], $params = [], $postData = false)
    {
        $curl = curl_init();
        $url = "{$this->voogUrl}/admin/api/{$uri}";

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => array_merge([
                'X-API-TOKEN: ' . $this->token,
                'Content-Type: application/json',
            ], $headers),
            CURLOPT_POSTFIELDS => $postData,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (isset($response->errors)) {
            throw new \Exception($response->message);
        }

        return $response;
    }

    public function getArticles()
    {
        $response = $this->request('GET', 'articles');

        return $response;
    }

    public function getImage($fileName)
    {
        $response = $this->request('GET', 'assets', [], [
            'type' => 'images',
            'name' => $fileName,
        ]);

        return $response;
    }

    public function postArticle($title, $content, $thumbnailId = null, $date = null)
    {
        $postbody = json_encode([
            'autosaved_title' => $title,
            'autosaved_body' => $content,
            'published' => true,
            'publishing' => true,
            'image_id' => $thumbnailId,
            'page_id' => 3338656,
            'created_at' => $date,
            'updated_at' => $date,
            'published_at' => $date,
        ]);

        $response = $this->request('POST', 'articles', [], [], $postbody);

        return $response;
    }
}
