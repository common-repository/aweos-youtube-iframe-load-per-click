<?php
/**
 * AWEOS YouTube iframe Load per Click
 *
 * @author      AWEOS GmbH
 * @copyright   2018 AWEOS GmbH. All rights reserved.
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: AWEOS YouTube iframe Load per Click
 * Plugin URI:  -
 * Description: YouTube can't be used directly anymore, this plugin asks for the users permission.
 * Version:     1.0.4
 * Author:      AWEOS GmbH
 * Author URI:  https://aweos.de
 * Text Domain: aweos-youtube-privacy-domain
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt

YouTube can't be used directly anymore, this plugin asks for the users permission.
This plugin helps with the GDPR for your website.

*/

use Zend\Dom\Query;

if (!defined('ABSPATH')) {
    exit;
}

require_once 'admin-menu.php';
require_once 'vendor/autoload.php';

# search and replace

function awyt_clean_youtube_iframes($html)
{
    return preg_replace_callback('/<iframe.*\/iframe>/Usi', function($m) {
        $html = $m[0];

        if (strpos($html, 'youtube.com/embed') === false) {
            return $html;
        }

        $query = new Query($html);
        $nodes = $query->execute('iframe');

        foreach ($nodes as $node) {
            $attributes = [];

            foreach ($node->attributes as $key => $value) {
                $attributes[$key] = $value->value;
            }

            // video width and height attr's to style CSS

            $width = "";
            $height = "";

            if (array_key_exists('width', $attributes)) {
                if (array_key_exists('style', $attributes)) {
                    if (strpos($attributes['width'], '%') !== false) {
                        $suffix = '';
                    } else if (strpos($attributes['width'], 'px') !== false) {
                        $suffix = '';
                    } else if (strpos($attributes['width'], 'pt') !== false) {
                        $suffix = '';
                    } else {
                        $suffix = 'px';
                    }

                    $width .= "; width:" . $attributes['width'] . $suffix . ";";
                } else {
                    $width = "width:" . $attributes['width'] . $suffix . ";";
                }
            }

            if (array_key_exists('height', $attributes)) {
                if (strpos($attributes['height'], '%') !== false) {
                    $suffix = '';
                } else if (strpos($attributes['height'], 'px') !== false) {
                    $suffix = '';
                } else if (strpos($attributes['height'], 'pt') !== false) {
                    $suffix = '';
                } else {
                    $suffix = 'px';
                }

                if (array_key_exists('style', $attributes)) {
                    $height .= "; height:" . $attributes['height'] . $suffix . ";";
                } else {
                    $height = "height:" . $attributes['height'] . $suffix . ";";
                }
            }


            if (array_key_exists('style', $attributes)) {
                $attributes['style'] .= "$width$height";
            } else {
                $attributes['style'] = "$width$height";
            }

            if (array_key_exists('src', $attributes)) {
                $attributes['data-src'] = $attributes['src'];
            }

            if (array_key_exists('class', $attributes)) {
                $attributes['class'] .= ' awyt-video';
            } else {
                $attributes['class'] = 'awyt-video';
            }

            $attributes = array_diff_key($attributes, ['width' => '', 'height' => '', 'src' => '']);

            $attributesAsString = array_reduce(array_keys($attributes), function ($carry, $item) use ($attributes) {
                return $carry . $item.'="'.$attributes[$item].'" ';
            }, '');

            $html = '<div ' . $attributesAsString . '></div>';
            return $html;
        }

        return $html;
    }, $html);
}

function awyt_exclude($html)
{
    ob_start("awyt_clean_youtube_iframes");
}

add_action('wp_loaded', 'awyt_exclude');

# include jQuery to show a styled msg to the user

function awyt_enqueue()
{
    require_once 'lang/texts.php';
    wp_enqueue_script('jquery');
    wp_enqueue_script('awyt_script', plugin_dir_url(__FILE__) . "script.js", NULL, "5.1");
    wp_enqueue_style('awyt_style', plugin_dir_url(__FILE__) . "style.css", NULL, "5.2");
}

add_action("wp_enqueue_scripts", "awyt_enqueue");
