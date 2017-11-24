<?php
/*
Extension Name: constructions-incongrues.net/vanilla-ext-bookmarklet
Extension Url:
Description:
Version:
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;

// Setup services container
$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/config'));
$loader->load('services.yml');

// Create request. When created in services.yml it is completely empty
$request = Request::createFromGlobals();

// TODO : The best way ?
$Context->ServicesContainer = $container;
$Context->HttpRequest = $request;

// Setup delegates
$Context->AddToDelegate('DiscussionForm', 'PostLoadData', 'MiBookmarklet_PopulateDiscussionFromQueryString');
$Context->AddToDelegate('DiscussionForm', 'DiscussionForm_PreCommentRender', 'MiBookmarklet_PopulateCommentFromQueryString');
$Context->AddToDelegate('Panel', 'PostStartButtonRender', 'MiBookmarklet_DisplayBookmarkletLink');

function MiBookmarklet_PopulateDiscussionFromQueryString(&$DiscussionForm)
{
    if ($DiscussionForm->Discussion->DiscussionID) {
        return;
    }

    $DiscussionForm->Discussion->Name = _MiBookmarklet_Truncate(
        _MiBookmarklet_CleanTitle(
            ForceIncomingString('title', null),
            _MiBookmarklet_GetDomain(ForceIncomingString('url', null))
        ),
        100,
        '...'
    );
}

function MiBookmarklet_PopulateCommentFromQueryString(&$DiscussionForm)
{
    if ($DiscussionForm->Discussion->DiscussionID) {
        return;
    }

    /** @var Container */
    $container = $DiscussionForm->Context->ServicesContainer;

    /** @var Request */
    $request = $DiscussionForm->Context->HttpRequest;

    // Link attributes
    $metadata = [
        'description' => trim(strip_tags($request->query->get('description'))),
        'image'       => trim($request->query->filter('image', null, FILTER_SANITIZE_URL)),
        'url'         => trim($request->query->filter('url', null, FILTER_SANITIZE_URL)),
    ];

    // Instanciate page extractor
    $extractor = $container->get('constructions-incongrues.vanilla.extension.bookmarklet.extractor');
    $metadata = array_merge($metadata, $extractor->extract($metadata['url']));

    // Add metadata to $_GET (ugly, but convenient for working with legacy extensions)
    $_GET = array_merge($_GET, $metadata);

    // Format comment body
    $format = 'default';
    if (isset($metadata['format'])) {
        $format = $metadata['format'];
    }
    $body = call_user_func(sprintf('MiBookmarklet_format%s', ucfirst($format)), $metadata);

    // Set comment body
    $DiscussionForm->Discussion->Comment->Body = $body;
}

function MiBookmarklet_formatDefault(array $metadata)
{
    $body = '';
    if ($metadata['description'] !== '') {
        $body .= sprintf("[quote]\n%s\n[/quote]\n\n", $metadata['description']);
    }

    if ($metadata['image'] !== '') {
        $body .= sprintf("[img]%s[/img]\n\n", $metadata['image']);
    }

    if ($metadata['url'] !== '') {
        $body .= sprintf("via %s\n\n", filter_var(ForceIncomingString('url', ''), FILTER_SANITIZE_URL));
    }

    return $body;
}

function MiBookmarklet_formatEvent(array $metadata)
{
    $body = '';
    if ($metadata['description'] !== '') {
        $body .= sprintf("[quote]\n%s\n[/quote]\n\n", $metadata['description']);
    }

    if ($metadata['image'] !== '') {
        $body .= sprintf("[img]%s[/img]\n\n", $metadata['image']);
    }

    if ($metadata['url'] !== '') {
        $body .= sprintf("via %s\n\n", filter_var(ForceIncomingString('url', ''), FILTER_SANITIZE_URL));
    }

    return $body;
}

function MiBookmarklet_formatRelease(array $metadata)
{
    $body = '';
    if ($metadata['url'] !== '') {
        $body .= sprintf("%s\n\n", filter_var(ForceIncomingString('url', ''), FILTER_SANITIZE_URL));
    }

    if (isset($metadata['VanillaReleases_downloadlink'])) {
        $body .= sprintf("%s\n\n", $metadata['VanillaReleases_downloadlink']);
    }

    if ($metadata['description'] !== '') {
        $body .= sprintf("[quote]\n%s\n[/quote]\n\n", $metadata['description']);
    }

    if ($metadata['image'] !== '') {
        $body .= sprintf("[img]%s[/img]\n\n", $metadata['image']);
    }


    return $body;
}

function MiBookmarklet_DisplayBookmarkletLink()
{
    $tpl_button = '<style>h1.bookmarklet:hover { box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1); transition: all 200ms ease-out; }</style><h1 style="text-align:center;" class="bookmarklet"><a href="%s" title="%s" onclick="return false;">%s</a><a href="%s" title="%s">En savoir plus</a></h1><p></p>';
    echo sprintf(
        $tpl_button,
        // Bookmarklet compiler : https://mrcoles.com/bookmarklet/
        trim(file_get_contents(__DIR__.'/assets/bookmarklet.compiled.js')),
        "Ce bookmarklet permet de capturer des liens partout sur Internet et de les rapatrier sur le forum.\n\nIl suffit de le glisser dans la barre de favoris de votre navigateur.\n\nCliquez sur le lien ci-dessous pour une explication plus détaillée.",
        '🍍 Ananas It !',
        '#',
        // 'Tout savoir sur le Bookmarklet Incongru'
        'Bientôt !'
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
