<?php

/**
 * @package launches
 * @version 0.3
 */
/*
Plugin Name: Rocket Launches
Plugin URI: https://github.com/rlane85/launch-table-wordpress
Description: Query spacelaunchnow.me and display results in a TablePress table
Author: Ryan Lane
Version: 0.3
Author URI: https://ahomeconnected.com
*/

require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';

use Flow\JSONPath\JSONPath;

function launch_script_enqueue()
{
    wp_register_script('datatocells', plugin_dir_url(__FILE__) . 'datatocells.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-button'), rand(0, 100), true);
};
add_action('wp_enqueue_scripts', 'launch_script_enqueue');
function launchdata($atts, $content = null)
{

    $nlSearch = 'Space Launch Complex FL';
    $nlQueryLimit = '30';
    $nlUrl = 'https://spacelaunchnow.me/api/3.3.0/launch/' . $atts['type'] . '?limit=' . $nlQueryLimit . '&search=' . $nlSearch;
    $nlResponse = wp_remote_get($nlUrl);
    $nlBody =  json_decode($nlResponse['body']);
    if (!function_exists('nlPath')) {
        function nlPath($metric, $nlBody)
        {
            return json_decode(json_encode((new JSONPath($nlBody))->find("$.results[*].$metric")));
        }
    }
    $nlNet = nlPath("net", $nlBody);
    $nlStart = nlPath("window_start", $nlBody);
    $nlEnd = nlPath("window_end", $nlBody);
    $nlStatus = nlPath("status.id", $nlBody);

    //formatting dates on each index for the items that return datetimes
    foreach ($nlNet as $key => $value) {
        $nlNet[$key] = date_i18n("g:ia n/j 'y ", strtotime($nlNet[$key]));
    }
    unset($value);
    foreach ($nlStart as $key => $value) {
        $nlStart[$key] = date_i18n("g:ia n/j ", strtotime($nlStart[$key]));
    }
    unset($value);
    foreach ($nlEnd as $key => $value) {
        $nlEnd[$key] = date_i18n("g:ia n/j ", strtotime($nlEnd[$key]));
    }
    unset($value);

    //assigning a color as string to each status id
    foreach ($nlStatus as $key => $value) {
        if ($nlStatus[$key] == 1) //"go"
        {
            $nlStatus[$key] = "lightblue";
        } else if ($nlStatus[$key] == 3) //"success"
        {
            $nlStatus[$key] = "lightgreen";
        } else if ($nlStatus[$key] == 6) //"in flight"
        {
            $nlStatus[$key] = "pink";
        } else {
            $nlStatus[$key] = "white";
        }
    }
    unset($value);
    global $launchData;

    $launchData = array(
        $nlNet,

        nlPath("rocket.configuration.launch_service_provider", $nlBody),

        nlPath("mission.name", $nlBody),
        nlPath("mission.type", $nlBody),
        nlPath("pad.name", $nlBody),
        nlPath("rocket.configuration.name", $nlBody),
        nlPath("rocket.spacecraft_stage.spacecraft.name", $nlBody),
        nlPath("rocket.spacecraft_stage.destination", $nlBody),

        //info for tooltips and styling at the end

        nlPath("slug", $nlBody),
        $nlStart,
        $nlEnd,
        nlPath("mission.description", $nlBody),
        nlPath("rocket.spacecraft_stage.spacecraft.description", $nlBody),
        nlPath("pad.wiki_url", $nlBody),
        $nlStatus,
        $atts

    );
    wp_enqueue_script('datatocells');

    if ($atts["type"] == "upcoming") {
        wp_localize_script('datatocells', 'launchdata', $launchData);
    } else {
        wp_localize_script('datatocells', 'prevlaunchdata', $launchData);
    }
}
add_shortcode('launch', 'launchdata');
