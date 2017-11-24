<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper;

class ForceValuesMapper extends AbstractMapper
{
    public function mapUrl($url)
    {
        return $this->parameters->get('values');
    }
}
