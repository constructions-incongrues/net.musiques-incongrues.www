<?php

$PluginInfo['EventCalendar'] = [
    'Name' => 'Event Calendar',
    'Description' => 'Adds an event date field to new discussions so that they can be treated as events',
    'Version' => '0.4',
    'RequiredApplications' => ['Vanilla' => '>=2.1'],
    'SettingsUrl' => '/settings/eventcalendar',
    'RequiredPlugins' => false,
    'RequiredTheme' => false,
    'MobileFriendly' => true,
    'HasLocale' => true,
    'RegisterPermissions' => false,
    'RegisterPermissions' => [
        'Plugins.EventCalendar.Add',
        'Plugins.EventCalendar.Manage',
        'Plugins.EventCalendar.Notify',
        'Plugins.EventCalendar.View'
    ],
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/r_j',
    'License' => 'MIT'
];

/**
 * Plugin that adds a date field to new discussions.
 *
 * New discussions could be entered with an "event date". Plugin creates
 * additional views where such discussions are shown on a calendar.
 *
 * @package EventCalendar
 * @author Robin Jurinka
 * @license MIT
 */
class EventCalendarPlugin extends Gdn_Plugin {
    /**
     * Prepare needed system changes.
     *
     * Initiate db structure change and create a custom
     * route for calendar view.
     *
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function setup() {
        // Change db structure.
        $this->structure();

        // Set initial config settings.
        if (!c('EventCalendar.CustomRoute')) {
            saveToConfig('EventCalendar.CustomRoute', 'eventcalendar');
        }

        // Set custom route to plugin page.
        $router = Gdn::router();
        $pluginPage = 'vanilla/eventcalendar$1';
        $newRoute = '^'.c('EventCalendar.CustomRoute', 'eventcalendar').'(/.*)?$';
        if (!$router->matchRoute($newRoute)) {
            $router->setRoute($newRoute, $pluginPage, 'Internal');
        }
    }

    /**
     * Add event date field to Discussion table.
     *
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function structure() {
        Gdn::structure()->table('Discussion')
            ->column('EventCalendarDate', 'date', true)
            ->set(false, false);
    }

    /**
     * Reset custom route.
     *
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function onDisable() {
        Gdn::router()->deleteRoute('^'.c('EventCalendar.CustomRoute', 'eventcalendar').'(/.*)?$');
    }

    /**
     * Settings screen to choose categories and set custom url.
     *
     * @param object $sender SettingsController.
     * @return void.
     * @package EventCalendar
     * @since 0.3
     */
    public function settingsController_eventCalendar_create($sender) {
        $sender->permission('Garden.Settings.Manage');

        $sender->title(t('EventCalendar.SettingsTitle'));
        $sender->addSideMenu('dashboard/settings/plugins');
        $sender->description('Description', t('EventCalendar.SettingsDescription'));

        if ($sender->Form->authenticatedPostBack()) {
            $formPostValues = $sender->Form->formValues();
            // Serialize CategoryIDs
            $sender->Form->setFormValue(
                'EventCalendar.CategoryIDs',
                serialize($formPostValues['EventCalendar.CategoryIDs'])
            );

            // Set new route if needed
            $newUrl = $formPostValues['EventCalendar.CustomRoute'];
            $oldUrl = c('EventCalendar.CustomRoute');
            if ($oldUrl != $newUrl) {
                // Delete old custom route.
                $router = Gdn::router();
                $router->deleteRoute('^'.$oldUrl.'(/.*)?$');

                // Set new custom route.
                $pluginPage = 'vanilla/eventcalendar$1';
                $newRoute = '^'.$newUrl.'(/.*)?$';
                if (!$router->matchRoute($newRoute)) {
                    $router->setRoute(
                        $newRoute,
                        'vanilla/eventcalendar$1',
                        'Internal'
                    );
                }
            }
        }

        $categories = CategoryModel::categories();
        unset($categories[-1]);
        $configurationModule = new ConfigurationModule($sender);
        $configurationModule->initialize([
            'EventCalendar.CategoryIDs' => [
                'Control' => 'CheckBoxList',
                'LabelCode' => 'Categories',
                'Items' => $categories,
                'Description' => 'Please choose categories in which the creation of events should be allowed',
                'Options' => ['ValueField' => 'CategoryID', 'TextField' => 'Name']
            ],
            'EventCalendar.CustomRoute' => [
                'Control' => 'TextBox',
                'LabelCode' => 'Custom Url',
                'Description' => 'The event calendar will be accessible under that url'
            ]
        ]);

        $configurationModule->renderAll();
    }

    /**
     * Add menu entry for calendar to custom menu.
     *
     * @param object $sender GardenController.
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function base_render_before($sender) {
        if (checkPermission('Plugins.EventCalendar.View') && $sender->Menu) {
            $sender->Menu->addLink(
                t('EventCalendar'),
                t('Event Calendar'),
                c('EventCalendar.CustomRoute', 'eventcalendar')
            );
        }
    }

    /**
     * Add input fields to New Discussion view.
     *
     * Check for custom permission. Allows creation of events in current
     * and next year.
     * Datefield is prefilled with current date by eventcalendar.js.
     *
     * @param object $sender PostController.
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function postController_beforeBodyInput_handler($sender) {
        if (!checkPermission(['Plugins.EventCalendar.Add', 'Plugins.EventCalendar.Manage'])) {
            return;
        }

        $sender->addJsFile('eventcalendar.js', 'plugins/EventCalendar');
        $sender->addDefinition('EventCalendarCategoryIDs', json_encode(C('EventCalendar.CategoryIDs')));

        $categoryID = $sender->Discussion->CategoryID;

        // initially don't hide elements in allowed categories
        $cssClass = 'P EventCalendarInput';
        if (!in_array($categoryID, Gdn::config('EventCalendar.CategoryIDs'))) {
            $cssClass .= ' Hidden';
        }

        $year = date('Y');
        $yearRange = $year.'-'.($year + 1);
        $fields = explode(',', Gdn::translate('EventCalendar.DateOrder', 'month,day,year'));

        echo '<div class="', $cssClass, '">';
        echo $sender->Form->label('Event Date', 'EventCalendarDate');
        echo $sender->Form->date('EventCalendarDate', [
            'YearRange' => $yearRange,
            'Fields' => $fields
        ]);
        echo '</div>';
    }

    /**
     * Check permission and validate event date input.
     *
     * @param object $sender DiscussionModel.
     * @param mixed  $args   EventArguments.
     *
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function discussionModel_beforeSaveDiscussion_handler($sender, $args) {
        // Reset event date and return if wrong category or no right to add event.
        $session = Gdn::session();
        $categoryID = $args['FormPostValues']['CategoryID'];
        if (!in_array($categoryID, Gdn::config('EventCalendar.CategoryIDs')) || !$session->checkPermission(['Plugins.EventCalendar.Add', 'Plugins.EventCalendar.Manage'])) {
            $args['FormPostValues']['EventCalendarDate'] = '';
            return;
        }
        // Add custom validation text.
        $sender->Validation->applyRule(
            'EventCalendarDate',
            'Required',
            Gdn::translate('EventDate.Required', 'Please enter an event date')
        );
        $sender->Validation->applyRule(
            'EventCalendarDate',
            'Date',
            Gdn::translate('EventDate.IsDate', 'The event date you\'ve entered is invalid')
        );
    }

    /**
     * Return nicely formatted html for an event date.
     *
     * @param date    $eventDate   The date to format.
     * @param boolean $includeIcon Whether an icon should be included or not.
     *
     * @return string Formatted event date.
     * @package EventCalendar
     * @since 0.1
     */
    private function formatEventCalendarDate($eventDate = '0000-00-00', $includeIcon = true) {
        if (!$eventDate || !checkPermission(['Plugins.EventCalendar.View']) || !$eventDate || $eventDate == '0000-00-00') {
            return;
        }
        if ($includeIcon) {
            $icon = '<img src="'.smartAsset('/plugins/EventCalendar/design/images', true).'/eventcalendar.png" />';
        } else {
            $icon = '';
        }

        return sprintf(
            Gdn::translate('EventCalendar.DateMarkup', '<div class="EventCalendarDate">%2$s On %1$s</div>'),
            Gdn_Format::date($eventDate, Gdn::translate('EventCalendar.DateFormat', '%A, %e. %B %Y')),
            $icon
        );
    }

    /**
     * Add event date to discussion.
     *
     * @param object $sender DiscussionController.
     * @param object $args EventArguments.
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function discussionController_afterDiscussionTitle_handler($sender, $args) {
        echo $this->formatEventCalendarDate($args['Discussion']->EventCalendarDate);
    }

    /**
     * Add event date to discussions list.
     *
     * @param object $sender DiscussionsController.
     * @param object $args EventArguments.
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function discussionsController_afterDiscussionTitle_handler($sender, $args) {
        echo $this->formatEventCalendarDate($args['Discussion']->EventCalendarDate);
    }

    /**
     * Add event date to discussion in categories.
     *
     * @param object $sender CategoriesController.
     * @param object $args EventArguments.
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function categoriesController_afterDiscussionTitle_handler($sender, $args) {
        echo $this->formatEventCalendarDate($args['Discussion']->EventCalendarDate);
    }

    /**
     * Show calendar view.
     *
     * @param object $sender VanillaController.
     * @param array $args /Year/Month to show.
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */
    public function vanillaController_eventCalendar_create($sender, $args = []) {
        $sender->permission('Plugins.EventCalendar.View');

        $eventCalendarModel = new EventCalendarModel();

        // $sender->clearCssFiles();
        $sender->addCssFile('style.css');
        $sender->addCssFile('eventcalendar.css', 'plugins/EventCalendar');
        $sender->addJsFile('eventcalendar.js', 'plugins/EventCalendar');
        $sender->MasterView = 'default';
        $sender->addModule('NewDiscussionModule');
        $sender->addModule('CategoriesModule');
        $sender->addModule('BookmarkedModule');

        // only show current year +/- 1
        $year = (int)$args[0];
        $currentYear = date('Y');
        if ($year < $currentYear -1 || $year > $currentYear + 1) {
            $year = $currentYear;
        }
        // sanitize month
        $month = sprintf("%02s", (int)$args[1]);
        if ($month < 1 || $month > 12) {
            $month = date('m');
        }

        $monthFirst = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $monthFirst);
        $monthLast = mktime(0, 0, 0, $month, $daysInMonth, $year);
        $sender->canonicalUrl(url(Gdn::config('EventCalendar.CustomRoute', 'eventcalendar'), true));
        $sender->setData('Title', Gdn::translate('Event Calendar'));
        $sender->setData('Breadcrumbs', [[
            'Name' => Gdn::translate('Event Calendar'),
            'Url' => $sender->canonicalUrl()
        ]]);
        $sender->setData('Month', $month);
        $sender->setData('Year', $year);
        $sender->setData('MonthFirst', $monthFirst);
        $sender->setData('MonthLast', $monthLast);
        $sender->setData('PreviousMonth', date('Y', $monthFirst - 1).'/'.date('m', $monthFirst - 1));
        $sender->setData('NextMonth', date('Y', $monthLast + 86400).'/'.date('m', $monthLast + 86400));
        $sender->setData('DaysInMonth', $daysInMonth);
        
        $sender->setData('Events', $eventCalendarModel->get("{$year}-{$month}-01", "{$year}-{$month}-{$daysInMonth}"));
        $sender->setData('CanonicalUrl', $sender->canonicalUrl());
        $sender->setData('Title', Gdn_Format::date($monthFirst, t('Calendar for %B %Y')));

        $viewName = 'month';
        $sender->render($viewName, '', 'plugins/EventCalendar');
    }
}
