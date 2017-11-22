<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet;

use ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper\AbstractMapper as AbstractMapper;
use Symfony\Component\HttpFoundation\ParameterBag;

class Page
{
    private $patterns = [];
    private $mappers = [];

    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }

    public function addMapper(AbstractMapper $mapper, array $parameters = [])
    {
        $mapper->parameters->add($parameters);
        $this->mappers[] = $mapper;
    }

    public function match($url)
    {
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    public function map($url)
    {
        $data = [];
        foreach ($this->mappers as $mapper) {
            if ($this->match($url)) {
                $data = array_merge($data, $mapper->map($url));
            }
        }

        return $data;
    }
}
