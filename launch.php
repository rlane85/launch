<?php

/**
 * @package launches
 * @version 0.1
 */
/*
Plugin Name: Rocket Launches
Plugin URI: https://github.com/rlane85/launch-table-wordpress
Description: Query spacelaunchnow.me and display results in a TablePress table
Author: Ryan Lane
Version: 0.1
Author URI: https://ahomeconnected.com
*/


    


require_once plugin_dir_path(__FILE__).'/vendor/autoload.php';
use Flow\JSONPath\JSONPath;
$nlSearch = 'Space Launch Complex FL';
$nlQueryLimit = '30';
$nlUrl = 'https://spacelaunchnow.me/api/3.3.0/launch/upcoming?limit='.$nlQueryLimit.'&search='.$nlSearch;  
$nlResponse = wp_remote_get($nlUrl);
$nlBody =  json_decode($nlResponse['body']);
function nlPath($metric, $nlBody) 
{
    return json_decode(json_encode((new JSONPath($nlBody))->find("$.results[*].$metric")));
}
$nlNet = nlPath("net", $nlBody);
$nlSlug = nlPath("slug", $nlBody);

foreach ($nlNet as $key => $value)
    {$nlNet[$key] = date_i18n("g:ia n/j ", strtotime($nlNet[$key]));}
unset($value);
$launchData = array
(
    $nlNet,                                                                 

    nlPath("rocket.configuration.launch_service_provider", $nlBody),
   
    nlPath("mission.name", $nlBody),
    nlPath("mission.type", $nlBody),
    nlPath("pad.name", $nlBody),
    nlPath("rocket.configuration.name", $nlBody),
    nlPath("rocket.spacecraft_stage.spacecraft.name", $nlBody),
    nlPath("rocket.spacecraft_stage.destination", $nlBody),

    nlPath("slug", $nlBody),

);

add_action('wp_enqueue_scripts', function (){
    global $launchData;
    wp_register_script('datatocells', plugin_dir_url(__FILE__).'datatocells.js', array('jquery'),null, true);
    wp_enqueue_script('datatocells');
    wp_localize_script('datatocells', 'launchData', $launchData);

});

