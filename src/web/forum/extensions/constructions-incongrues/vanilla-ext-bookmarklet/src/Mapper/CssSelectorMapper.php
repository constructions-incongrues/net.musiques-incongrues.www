<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper;

use Goutte\Client;

class CssSelectorMapper extends AbstractMapper
{
    public function mapUrl($url)
    {
        // Create array with null values for each attribute
        $data = array_fill_keys(array_keys($this->parameters->get('attributes')), null);

        // Extract attribute data from css selectors
        $client = new Client();
        $crawler = $client->request('GET', $url);
        foreach ($this->parameters->get('attributes') as $attribute => $selector) {
            $data[$attribute] = $crawler->filter($selector)->extract('_text')[0];
        }

        return $data;
    }
}
