<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper;

use Dflydev\DotAccessData\Data;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractMapper
{
    public $parameters;

    public function __construct()
    {
        $this->parameters = new ParameterBag();
    }

    public function addParameters(array $parameters)
    {
        $this->parameters->add($parameters);
    }

    public function map($url)
    {
        $data = [];

        $data = array_merge($data, $this->mapUrl($url));
        $data = array_merge($data, $this->remap($data, $this->parameters->get('remap', [])));

        return $data;
    }

    private function remap(array $data, array $remap)
    {
        $dotted = new Data($data);
        foreach ($remap as $src => $dest) {
            $data[$dest] = $dotted->get($src);
        }
        return $data;
    }

    abstract protected function mapUrl($url);
}
