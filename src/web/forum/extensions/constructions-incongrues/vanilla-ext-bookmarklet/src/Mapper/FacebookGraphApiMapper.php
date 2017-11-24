<?php

namespace ConstructionsIncongrues\Vanilla\Extension\Bookmarklet\Mapper;

use Dflydev\DotAccessData\Data;

class FacebookGraphApiMapper extends AbstractMapper
{
    public function __construct($id, $secret)
    {
        parent::__construct();
        $this->parameters->set('id', $id);
        $this->parameters->set('secret', $secret);
    }

    protected function mapUrl($url)
    {
        $data = [];

        // Get a Facebook Graph API token
        $fb = new \Facebook\Facebook([
            'app_id'               => $this->parameters->get('id'),
            'app_secret'           => $this->parameters->get('secret')
        ]);
        $app = $fb->getApp();
        $token = $app->getAccessToken();

        // Get Facebook event id from URL
        if (\preg_match($this->parameters->get('pattern'), $url, $matches) !== false) {
            $method = sprintf('getGraph%s', ucfirst($this->parameters->get('type', 'node')));
            $response = $fb->get(
                sprintf('/%s?fields=%s', $matches[1], implode(',', $this->parameters->get('fields'))),
                $token
            );
            $node = call_user_func([$response, $method]);
            $data = $node->asArray();
        }

        return $data;
    }
}
