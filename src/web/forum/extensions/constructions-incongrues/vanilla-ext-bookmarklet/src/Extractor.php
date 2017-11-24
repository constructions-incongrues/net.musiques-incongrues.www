<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet;

class Extractor
{
    private $pages = [];

    public function addPage(Page $page)
    {
        $this->pages[] = $page;
    }

    public function extract($url)
    {
        $data = [];
        foreach ($this->pages as $page) {
            if ($page->match($url)) {
                $data = $page->map($url);
            }
        }

        return $data;
    }
}
