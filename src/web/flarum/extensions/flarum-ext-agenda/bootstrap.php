<?php

use Flarum\Event\ConfigureClientView;
use Flarum\Event\PostWillBeSaved;
use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {
    $events->listen(PostWillBeSaved::class, function (PostWillBeSaved $event) {
        // do stuff before a post is saved
    });

    $events->listen(ConfigureClientView::class, function (ConfigureClientView $event) {
        if ($event->isForum()) {
            $event->addAssets(__DIR__.'/js/forum/dist/extension.js');
            $event->addBootstrapper('musiquesincongrues/flarum-ext-agenda/main');
        }
    });
};
