<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper;

class UrlMapper extends AbstractMapper
{
    public function mapUrl($url)
    {
        $attributes = $this->parameters->get('attributes');
        $metadata = \array_fill_keys($attributes, null);
        $urlParts = \parse_url($url);
        array_walk($attributes, function ($attribute) use (&$metadata, $urlParts) {
            if (\preg_match($this->parameters->get('pattern'), $urlParts[$this->parameters->get('component')], $matches) !== false) {
                $metadata[$attribute] = $matches[1];
            }
        });

        return $metadata;
    }
}
