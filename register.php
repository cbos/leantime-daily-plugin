<?php

use Leantime\Core\Events\EventDispatcher;
use Illuminate\Support\Facades\Log;
use Leantime\Plugins\Daily\Middleware\GetLanguageAssets;


EventDispatcher::add_filter_listener('leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures', function ($menuStructure) {

    $menuStructure['personal'][30] = [
        'type' => 'item',
        'module' => 'daily',
        'title' => 'Daily summary',
        'icon' => 'fa fa-fw fa-calendar-check',
        'tooltip' => 'Daily Summary',
        'href' => '/daily/settings',
        'active' => ['settings']
    ];

    return $menuStructure;

}, 10);

EventDispatcher::add_filter_listener('leantime.domain.menu.repositories.menu.getSectionMenuType.menuSections',
    function ($routes) {

        $sections = [
            'daily.settings' => 'personal'
        ];

        return array_merge($routes, $sections);
    },
);


EventDispatcher::add_filter_listener('leantime.*.availableWidgets',
    function ($availableWidgets) {
        $myWidgets = [];
        $myWidgets['mydaily'] = app()->make("Leantime\Domain\Widgets\Models\Widget", [
            'id' => 'mydaily',
            'name' => 'widgets.title.my_daily',
            'description' => 'widgets.descriptions.my_daily',
            'gridHeight' => 22,
            'gridWidth' => 8,
            'gridMinHeight' => 10,
            'gridMinWidth' => 2,
            'gridX' => 0,
            'gridY' => 43,
            'alwaysVisible' => false,
            'noTitle' => false,
            'widgetUrl' => BASE_URL . '/daily/settings',
            'fixed' => false,
        ]);
        return array_merge($availableWidgets, $myWidgets);
    });


//Register Language Assets
EventDispatcher::add_filter_listener(
    'leantime.*.plugins_middleware',
    fn(array $middleware) => array_merge($middleware, [GetLanguageAssets::class]),
);
