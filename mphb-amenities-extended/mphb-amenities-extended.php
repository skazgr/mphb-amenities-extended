<?php
/**
 * Plugin Name: MPHB Amenities Extended
 * Description: Adds enhanced amenities display (icons, tree view) to MotoPress Hotel Booking accommodations.
 * Version: 1.0.0
 * Author: Marios Progoulakis
 * Author URI: https://github.com/skazgr
 * Text Domain: mphb-amenities-extended
 * Requires Plugins: motopress-hotel-booking
 * Requires at least: 5.8
 * Requires PHP: 7.2
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/gpl-3.0.html>.
 */

// Enqueue media uploader for amenity taxonomy
function enqueue_admin_scripts($hook_suffix) {
    $screen = get_current_screen();
    if (isset($screen->taxonomy) && $screen->taxonomy === 'mphb_room_type_facility') {
        wp_enqueue_media();
        wp_enqueue_script('admin-media-uploader',
            plugin_dir_url(__FILE__) . 'admin-media-uploader.js',
            array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

// Basic amenities shortcode
function custom_amenities_shortcode($atts) {
    $atts = shortcode_atts(array('limit' => 10), $atts, 'amenities');
    $post_id = get_the_ID();
    if (!$post_id) return '';
    $cache_key = 'custom_amenities_' . $post_id;
    $output = get_transient($cache_key);
    if (false === $output) {
        $terms = get_the_terms($post_id, 'mphb_room_type_facility');
        $output = '<div class="custom-amenities-container">';
        if (!empty($terms) && !is_wp_error($terms)) {
            $count = 0;
            foreach ($terms as $term) {
                if ($term->parent != 0 && $count < $atts['limit']) {
                    $image_id = get_term_meta($term->term_id, 'amenity-image-id', true);
                    $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                    $output .= '<div class="amenity-item">';
                    $output .= $image_url ? '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term->name) . '" class="amenity-image" />' : '';
                    $output .= '<span class="amenity-name">' . esc_html($term->name) . '</span>';
                    $output .= '</div>';
                    $count++;
                }
            }
        } else {
            $output .= '<div class="amenity-item">No amenities found.</div>';
        }
        $output .= '</div>';
        set_transient($cache_key, $output, 12 * HOUR_IN_SECONDS);
    }
    return $output;
}
add_shortcode('amenities', 'custom_amenities_shortcode');

// Recursive tree display of amenities
function build_amenities_tree($terms, $parent_id = 0, $depth = 0, $max_depth = 5) {
    if ($depth >= $max_depth) return '';
    $output = '';
    foreach ($terms as $term) {
        if ($term->parent == $parent_id) {
            $output .= '<div class="amenity-item">';
            if ($parent_id == 0) {
                $output .= '<strong>' . esc_html($term->name) . '</strong>';
            } else {
                $image_id = get_term_meta($term->term_id, 'amenity-image-id', true);
                $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                $output .= $image_url ? '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term->name) . '" class="amenity-image" />' : '';
                $output .= '<span>' . esc_html($term->name) . '</span>';
            }
            $output .= '</div>';
            $output .= build_amenities_tree($terms, $term->term_id, $depth + 1, $max_depth);
        }
    }
    return $output;
}

function custom_amenities_tree_shortcode($atts) {
    $post_id = get_the_ID();
    if (!$post_id) return '';
    $cache_key = 'custom_amenities_tree_' . $post_id;
    $output = get_transient($cache_key);
    if (false === $output) {
        $terms = get_the_terms($post_id, 'mphb_room_type_facility');
        if (empty($terms) || is_wp_error($terms)) return '<p>No amenities available.</p>';
        $output = '<div class="amenities-tree">' . build_amenities_tree($terms) . '</div>';
        set_transient($cache_key, $output, 12 * HOUR_IN_SECONDS);
    }
    return $output;
}
add_shortcode('amenities_tree', 'custom_amenities_tree_shortcode');

// Admin image upload
function add_amenity_image_field() {
    echo '<div class="form-field term-group"><label>' . __('Image') . '</label>';
    echo '<input type="hidden" id="amenity-image-id" name="amenity-image-id" value="">';
    echo '<div id="amenity-image-wrapper"></div><p>';
    echo '<input type="button" class="button" id="amenity-image-upload-button" value="' . __('Add Image') . '" />';
    echo '<input type="button" class="button" id="amenity-image-remove-button" value="' . __('Remove Image') . '" />';
    echo '</p></div>';
}
add_action('mphb_room_type_facility_add_form_fields', 'add_amenity_image_field', 10, 2);

function edit_amenity_image_field($term) {
    $image_id = get_term_meta($term->term_id, 'amenity-image-id', true);
    echo '<tr class="form-field"><th><label>' . __('Image') . '</label></th><td>';
    echo '<input type="hidden" id="amenity-image-id" name="amenity-image-id" value="' . esc_attr($image_id) . '">';
    echo '<div id="amenity-image-wrapper">';
    if ($image_id) echo wp_get_attachment_image($image_id, 'thumbnail');
    echo '</div><p>';
    echo '<input type="button" class="button" id="amenity-image-upload-button" value="' . __('Add Image') . '" />';
    echo '<input type="button" class="button" id="amenity-image-remove-button" value="' . __('Remove Image') . '" />';
    echo '</p></td></tr>';
}
add_action('mphb_room_type_facility_edit_form_fields', 'edit_amenity_image_field', 10, 2);

function save_amenity_image($term_id) {
    if (!empty($_POST['amenity-image-id'])) {
        update_term_meta($term_id, 'amenity-image-id', sanitize_text_field($_POST['amenity-image-id']));
    } else {
        delete_term_meta($term_id, 'amenity-image-id');
    }
}
add_action('created_mphb_room_type_facility', 'save_amenity_image', 10, 2);
add_action('edited_mphb_room_type_facility', 'save_amenity_image', 10, 2);

function add_amenity_thumbnail_column($columns) {
    $columns['amenity_thumbnail'] = __('Thumbnail');
    return $columns;
}
add_filter('manage_edit-mphb_room_type_facility_columns', 'add_amenity_thumbnail_column');

function display_amenity_thumbnail_column($content, $column_name, $term_id) {
    if ($column_name === 'amenity_thumbnail') {
        $image_id = get_term_meta($term_id, 'amenity-image-id', true);
        $url = wp_get_attachment_image_url($image_id, 'thumbnail');
        if ($url) $content = '<img src="' . esc_url($url) . '" width="50" height="50">';
    }
    return $content;
}
add_filter('manage_mphb_room_type_facility_custom_column', 'display_amenity_thumbnail_column', 10, 3);
