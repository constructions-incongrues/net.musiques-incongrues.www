<?php
/*
Extension Name: MiBookmarklet
Extension Url:
Description:
Version:
Author: Constructions Incongrues
Author Url:
*/

$Context->AddToDelegate('DiscussionForm', 'PostLoadData', 'MiBookmarklet_PopulateDiscussionFromQueryString');
$Context->AddToDelegate('DiscussionForm', 'DiscussionForm_PreCommentRender', 'MiBookmarklet_PopulateRequestFromQueryString');
$Context->AddToDelegate('DiscussionForm', 'DiscussionForm_PreCommentRender', 'MiBookmarklet_PopulateCommentFromQueryString');
$Context->AddToDelegate("Panel", "PostStartButtonRender", 'MiBookmarklet_DisplayBookmarkletLink');

// curl --header 'Authorization: Bearer EAACEdEose0cBADkG4q99mKoVcHI9YjVZBsS6GnWZAG7ZBrzVsLEuhhMZB9nFJYZCA0HCaK4xDOZBZAd5z1ZBSTDGkY2HFOfivmNrrZCxAAsrCflgReHkZAIwxOZBSXZAULEs8gVT02Sv7KNwaKJqDw8GWyvfOrCRb5d1NXAwLAdUWIVJCIHZCoxxaQpZBQ0XM2ZA7HPARomOBTaTzM6qQZDZD' 'https://graph.facebook.com/search?q=Int%C3%A9grale%20Henri-Georges%20Clouzot%20au%20Louxor&type=event' | jq .

$Context->Configuration['MiBookmarklet'] = [
    'DomainMap' => [
        'archive.org'          => 'release',
        'bandcamp.com'         => 'release',
        'facebook.com'         => 'event',
        'freemusicarchive.org' => 'release',
        'mixcloud.com'         => 'release',
        'soundcloud.com'       => 'release',
        'youtube.com'          => 'release',
    ],
    // 'FacebookToken' => 'EAACEdEose0cBAHNthvHhl0BddwL6aYrMxZCONTZB2ZCTVUPd8wYMtxa0uTsaerEQ7j9Pu2S9mI8sCvFf0ZAdVqKy8WmVrUpoM7i6ZCT5YxgOGBUVDknSJXPou4ndVDatBg5wiKZBXtZCdEfxK8sLQpnACJ11rKytcpbeWwMZBDGWVAJd8iC54jVRIUAOV6tuv2jCzzN9wJ8ZAqwZDZD'
    'FacebookToken' => 'EAAQCkCAZAeAABAL2785dZAOt3RVdxEiLKrf1z83arxgGwZA2037QvdgEmPRqpGUXl8japuVMtqrvXhjXn2rMXAvLYYRm62UcWahKJfBgwcb6F3rcEOWyPuZCtFFNuYTBehsDcZCK4bewOMLKN0ptoXEZBini8JUjpMAtTYut2DswZDZD'
];

function MiBookmarklet_PopulateDiscussionFromQueryString(&$DiscussionForm)
{
    $DiscussionForm->Discussion->Name = _MiBookmarklet_Truncate(
        _MiBookmarklet_CleanTitle(
            ForceIncomingString('Title'),
            _MiBookmarklet_GetDomain(ForceIncomingString('Via'))
        ),
        100,
        '...'
    );
}

function MiBookmarklet_PopulateCommentFromQueryString(&$DiscussionForm)
{
    // Comment body
    $body = '';

    // Link attributes
    $description = trim(strip_tags(ForceIncomingString('Description', '')));
    $url = trim(filter_var(ForceIncomingString('Via', ''), FILTER_SANITIZE_URL));
    $image = trim(filter_var(ForceIncomingString('Image', ''), FILTER_SANITIZE_URL));

    // Get url domain
    $domain = _MiBookmarklet_GetDomain($url);

    // Format according to strategy
    $formatFunction = 'MiBookmarklet_formatDefault';
    if (array_key_exists($domain, $DiscussionForm->Context->Configuration['MiBookmarklet']['DomainMap'])) {
        $formatFunction = sprintf(
            'MiBookmarklet_format%s',
            ucfirst($DiscussionForm->Context->Configuration['MiBookmarklet']['DomainMap'][$domain])
        );
    }
    $body = call_user_func($formatFunction, $url, $description, $image);

    // Set comment body
    $DiscussionForm->Discussion->Comment->Body = $body;
}

function MiBookmarklet_PopulateRequestFromQueryString(&$DiscussionForm)
{
    $url = trim(filter_var(ForceIncomingString('Via', ''), FILTER_SANITIZE_URL));
    $domain = _MiBookmarklet_GetDomain($url);

    if (array_key_exists($domain, $DiscussionForm->Context->Configuration['MiBookmarklet']['DomainMap'])
        && $DiscussionForm->Context->Configuration['MiBookmarklet']['DomainMap'][$domain] == 'event') {
        $_GET['VanillaEvents_isevent'] = 'on';
        $_GET['is_event'] = true;
        $_GET['CategoryID'] = 5;

        if ($domain == 'facebook.com') {
            $graphUrl = sprintf(
                'https://graph.facebook.com/search?type=event&q=%s',
                rawurlencode(
                    _MiBookmarklet_CleanTitle(ForceIncomingString('Title'),
                    _MiBookmarklet_GetDomain(ForceIncomingString('Via')))
                )
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $graphUrl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                sprintf(
                    'Authorization: Bearer %s',
                    $DiscussionForm->Context->Configuration['MiBookmarklet']['FacebookToken']
                )
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $response = json_decode($response, true);
            if ($response && isset($response['data']) && count($response['data'])) {
                // Location
                $_GET['VanillaEvents_city'] = $response['data'][0]['place']['city'];
                $_GET['VanillaEvents_country'] = $response['data'][0]['place']['country'];

                // Start date
                $date = DateTime::createFromFormat(DateTime::ATOM, $response['data'][0]['start_time']);
                $_GET['VanillaEvents_date'] = $date->format('d/m/Y');

                // Description
                if (!ForceIncomingString('Description', false)) {
                    $_GET['Description'] = $response['data'][0]['description'];
                }

                // Image
                if (!ForceIncomingString('Image', false)) {
                    $graphUrl = sprintf(
                        'https://graph.facebook.com/v2.11/%s?fields=cover',
                        $response['data'][0]['id']
                    );
                    curl_setopt($curl, CURLOPT_URL, $graphUrl);
                    $response = curl_exec($curl);
                    $response = json_decode($response, true);
                    $_GET['Image'] = $response['cover']['source'];
                }
            }
            curl_close($curl);
        }
    }

    if (array_key_exists($domain, $DiscussionForm->Context->Configuration['MiBookmarklet']['DomainMap'])
        && $DiscussionForm->Context->Configuration['MiBookmarklet']['DomainMap'][$domain] == 'release') {
        $_GET['is_release'] = true;
    }

}

function MiBookmarklet_formatDefault($url, $description, $image)
{
    $body = '';
    if ($description !== '') {
        $body .= sprintf("[quote]\n%s\n[/quote]\n\n", $description);
    }

    if ($image !== '') {
        $body .= sprintf("[img]%s[/img]\n\n", $image);
    }

    if ($url !== '') {
        $body .= sprintf("via %s\n\n", filter_var(ForceIncomingString('Via', ''), FILTER_SANITIZE_URL));
    }

    return $body;
}

function MiBookmarklet_formatEvent($url, $description, $image)
{
    $body = '';
    if ($description !== '') {
        $body .= sprintf("[quote]\n%s\n[/quote]\n\n", $description);
    }

    if ($image !== '') {
        $body .= sprintf("[img]%s[/img]\n\n", $image);
    }

    if ($url !== '') {
        $body .= sprintf("via %s\n\n", filter_var(ForceIncomingString('Via', ''), FILTER_SANITIZE_URL));
    }

    return $body;
}

function MiBookmarklet_formatRelease($url, $description, $image)
{
    $body = '';
    if ($url !== '') {
        $body .= sprintf("%s\n\n", filter_var(ForceIncomingString('Via', ''), FILTER_SANITIZE_URL));
    }

    if ($description !== '') {
        $body .= sprintf("[quote]\n%s\n[/quote]\n\n", $description);
    }

    if ($image !== '') {
        $body .= sprintf("[img]%s[/img]\n\n", $image);
    }


    return $body;
}

function MiBookmarklet_DisplayBookmarkletLink()
{
    $tpl_button = '<style>h1.bookmarklet:hover { box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1); transition: all 200ms ease-out; }</style><h1 style="text-align:center;" class="bookmarklet"><a href="%s" title="%s" onclick="return false;">%s</a><a href="%s" title="%s">En savoir plus</a></h1><p></p>';
    echo sprintf(
        $tpl_button,
        trim(file_get_contents(__DIR__.'/bookmarklet.js')),
        "Ce bookmarklet permet de capturer des liens partout sur Internet et de les rapatrier sur le forum.\n\nIl suffit de le glisser dans la barre de favoris de votre navigateur.\n\nCliquez sur le lien ci-dessous pour une explication plus détaillée.",
        '⤤ Ananas It !',
        '#TODO',
        'Tout savoir sur le Bookmarklet Incongru'
    );
}

function _MiBookmarklet_GetDomain($url)
{
    $domain = parse_url($url, PHP_URL_HOST);
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $matches)) {
        return $matches['domain'];
    }
}

function _MiBookmarklet_CleanTitle($title, $domain)
{
    // Specific code for Facebook to remove the prefix when user has unread notifications
    if ($domain  == 'facebook.com') {
        if (preg_match('/^\(\d+\) ?(.+)$/', $title, $matches)) {
            $title = $matches[1];
        }
    }

    // Strip tags
    $title = strip_tags($title);

    // Remove platform specific SEO text from title
    $platformNoise = [
        ' | Écoute gratuite sur SoundCloud',
        ' | Mixcloud',
        'Free Music Archive: '
    ];
    $title = str_replace($platformNoise, '', $title);

    return $title;
}

function _MiBookmarklet_Truncate($string, $max_length = 30, $replacement = '', $trunc_at_space = false)
{
    $max_length -= strlen($replacement);
    $string_length = strlen($string);

    if ($string_length <= $max_length) {
        return $string;
    }

    if ($trunc_at_space && ($space_position = strrpos($string, ' ', $max_length-$string_length))) {
        $max_length = $space_position;
    }

    return substr_replace($string, $replacement, $max_length);
}
