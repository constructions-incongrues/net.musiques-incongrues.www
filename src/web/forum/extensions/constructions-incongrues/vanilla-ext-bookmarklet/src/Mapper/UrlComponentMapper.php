<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper;

class UrlComponentMapper extends AbstractMapper
{
    public function mapUrl($url)
    {
        $attributes = array_keys($this->parameters->get('attributes'));
        $metadata = \array_fill_keys($attributes, null);

        $urlParts = \parse_url($url);
        foreach ($this->parameters->get('attributes') as $attribute => $spec) {
            if (\preg_match($spec['pattern'], $urlParts[$spec['component']], $matches) !== false) {
                $metadata[$attribute] = $matches[1];
            }
        }

        return $metadata;
    }
}
