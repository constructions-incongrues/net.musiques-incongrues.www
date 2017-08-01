<?php if (!defined('APPLICATION')) exit;

$PluginInfo['Timeago'] = array(
    'Name'        => "Timeago",
    'Description' => "Timeago adds automatically updating fuzzy timestamps (e.g. \"4 minutes ago\") throughout Garden.",
    'Version'     => '2.0.1',
    'PluginUrl'   => 'https://github.com/kasperisager/vanilla-timeago',
    'Author'      => "Kasper Kronborg Isager",
    'AuthorEmail' => 'kasperisager@gmail.com',
    'AuthorUrl'   => 'https://github.com/kasperisager',
    'License'     => 'MIT',
    'RequiredApplications' => array('Vanilla' => '2.1.x')
);

/**
 * Timeago Plugin
 *
 * @author    Kasper Kronborg Isager <kasperisager@gmail.com>
 * @copyright 2014 (c) Kasper Kronborg Isager
 * @license   MIT
 * @package   Timeago
 * @since     2.0.0
 */
class TimeagoPlugin extends Gdn_Plugin
{
    /**
     * Add Timeago to all controllers
     *
     * @since  2.0.0
     * @access public
     * @param  Gdn_Controller $sender
     */
    public function base_render_before($sender)
    {
        // Plugin definitions for use in Javascript
        $definitions = array(
            'locale' => array(
                'prefixAgo'     => t('timeago.prefixAgo',     null),
                'prefixFromNow' => t('timeago.prefixFromNow', null),
                'suffixAgo'     => t('timeago.suffixAgo',     'ago'),
                'suffixFromNow' => t('timeago.suffixFromNow', 'from now'),
                'seconds'       => t('timeago.seconds',       'less than a minute'),
                'minute'        => t('timeago.minute',        'about a minute'),
                'minutes'       => t('timeago.minutes',       '%d minutes'),
                'hour'          => t('timeago.hour',          'about an hour'),
                'hours'         => t('timeago.hours',         'about %d hours'),
                'day'           => t('timeago.day',           'a day'),
                'days'          => t('timeago.days',          '%d days'),
                'month'         => t('timeago.month',         'about a month'),
                'months'        => t('timeago.months',        '%d months'),
                'year'          => t('timeago.year',          'about a year'),
                'years'         => t('timeago.years',         '%d years'),
                'wordSeparator' => t('timeago.wordSeparator', ' '),
                'numbers'       => array()
            )
        );

        $sender->addDefinition('Timeago', json_encode($definitions));

        // Add required assets
        $sender->addJsFile($this->getResource('js/timeago.min.js', false, false));
    }
}
