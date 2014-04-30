<?php
/*
Plugin Name: Blacklist Auto Updater
Text Domain: blacklist_auto_updater
Domain Path: /lang
Description: Automatic updating of the <a href='options-discussion.php'>comment blacklist</a> in WordPress with antispam keys from <a href='https://github.com/splorp/wordpress-comment-blacklist' target='_blank'>GitHub</a>.
Author: Sergej M&uuml;ller
Author URI: http://wpcoder.de
License: GPLv2 or later
Version: 0.0.1
*/


/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/* Quit */
defined('ABSPATH') OR exit;


register_activation_hook(
    __FILE__,
    function() {
        add_site_option(
            'blacklist_keys__last_request',
            array(
                'time' => null,
                'etag' => null
            )
        );
    }
);
register_deactivation_hook(
    __FILE__,
    function() {
        delete_site_option(
            'blacklist_keys__last_request'
        );
        delete_transient(
            'blacklist_keys__last_touch'
        );
    }
);


add_filter(
    'plugin_row_meta',
    function($input, $file) {
        /* Only this plugin */
        if ( $file !== plugin_basename(__FILE__) ) {
            return $input;
        }

        /* Plugin options */
        $options = get_site_option(
            'blacklist_keys__last_request'
        );

        /* Get update time */
        if ( ! empty($options['time']) ) {
            $updated = sprintf(
                '%s %s',
                human_time_diff(
                    $options['time'],
                    current_time('timestamp')
                ),
                translate('ago', 'blacklist_auto_updater')
            );
        } else {
            $updated = translate('Never');
        }

        /* Plugin rows */
        return array_merge(
            $input,
            array(
                '<a href="https://flattr.com/submit/auto?user_id=sergej.mueller&url=https%3A%2F%2Fgithub.com%2Fsergejmueller%2Fwp-blacklist-updater" target="_blank">Flattr</a>',
                '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=5RDDW9FEHGLG6" target="_blank">PayPal</a>',
                sprintf(
                    '%s: %s',
                    translate('Last Update', 'blacklist_auto_updater'),
                    $updated
                )
            )
        );
    },
    10,
    2
);


add_action(
    'plugins_loaded',
    function() {
        if ( (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) OR (defined('DOING_AJAX') && DOING_AJAX) OR (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) {
            return;
        }

        if ( ! get_transient('blacklist_keys__last_touch') ) {
            get_blacklist_from_github();
        }
    }
);


function get_blacklist_from_github() {
    /* Simulate cron */
    set_transient(
        'blacklist_keys__last_touch',
        current_time('timestamp'),
        DAY_IN_SECONDS
    );

    /* Plugin options */
    $options = get_site_option(
        'blacklist_keys__last_request'
    );

    /* Request header */
    if ( ! empty($options['etag']) ) {
        $args = array(
            'headers' => array(
                'If-None-Match' => $options['etag']
            )
        );
    } else {
        $args = array();
    }

    /* Output debug infos */
    if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
        error_log('Get blacklist');
    }

    /* Start request */
    $response = wp_remote_get(
        'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
        $args
    );

    /* Exit on error */
    if ( is_wp_error($response) ) {
        return false;
    }

    /* Check response code */
    if ( wp_remote_retrieve_response_code($response) !== 200 ) {
        return;
    }

    /* Output debug infos */
    if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
        error_log('Update blacklist');
    }

    /* Update blacklist */
    update_option(
        'blacklist_keys',
        wp_remote_retrieve_body($response)
    );

    /* Update request infos */
    update_site_option(
        'blacklist_keys__last_request',
        array(
            'time' => current_time('timestamp'),
            'etag' => wp_remote_retrieve_header(
                $response,
                'etag'
            )
        )
    );
}