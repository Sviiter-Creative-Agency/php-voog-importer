<?php

namespace Voog;

use Exception;

class Migrate
{
    private $parser;
    private $api;

    public function __construct($data)
    {
        $this->parser = new Parser($data);
        $this->api = new VoogApi(VOOG_URL, VOOG_API);
    }

    public function doMigrate()
    {
        if (!$this->parser->getTitle()) {
            throw new Exception("Data ID #{$this->parser->getId()} does not have the title, skipped.");
        }

        /** @var DateTime $date */
        $date = $this->parser->getDate();
        $year = (int)$date->format('Y');

        // Check if the year is not 2021, 2022, or 2023
        if ($year < 2021 || $year > 2023) {
            throw new Exception("Data ID #{$this->parser->getId()} - {$this->parser->getDate()->format('Y-m-d')} is old data, skipped.");
        }
        
        $firstImageFileName = $this->parser->getFirstImageName();
        $voogAssetId = false;

        if ($firstImageFileName) {
            $voogAssetId = $this->api->getImage($firstImageFileName);

            if ($voogAssetId) {
                $voogAssetId = $voogAssetId[0]->id;
            }
        }

        $article = $this->api->postArticle(
            $this->parser->getTitle(),
            $this->parser->getReplacedImageUrls(),
            $voogAssetId,
            $this->parser->getDate()->format('d.m.Y')
        );

        return $article;
    }

    public function getParser()
    {
        return $this->parser;
    }
}
