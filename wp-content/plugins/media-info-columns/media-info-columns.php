<?php
/**
 * Plugin Name: Media Info Columns
 * Plugin URI: https://rocket55.com
 * Description: Adds image dimensions and file size columns to the WordPress Media Library with sorting and filtering capabilities.
 * Version: 1.0.0
 * Author: Pierre R. Balian - Rocket55
 * Author URI: https://rocket55.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: media-info-columns
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MIC_VERSION', '1.0.0' );
define( 'MIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MIC_PLUGIN_DIR . 'includes/class-media-columns.php';

/**
 * Initialize the plugin.
 */
function mic_init() {
    new Media_Info_Columns();
}
add_action( 'plugins_loaded', 'mic_init' );

/**
 * Populate meta for existing media on activation.
 */
function mic_activate() {
    $attachments = get_posts( array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ) );

    foreach ( $attachments as $attachment_id ) {
        mic_update_attachment_meta( $attachment_id );
    }

    delete_transient( 'mic_common_dimensions' );
}
register_activation_hook( __FILE__, 'mic_activate' );

/**
 * Update attachment meta for dimensions.
 *
 * @param int $attachment_id The attachment ID.
 */
function mic_update_attachment_meta( $attachment_id ) {
    if ( ! wp_attachment_is_image( $attachment_id ) ) {
        update_post_meta( $attachment_id, '_total_pixels', 0 );
        update_post_meta( $attachment_id, '_dimensions', '' );
        return;
    }

    $metadata = wp_get_attachment_metadata( $attachment_id );

    if ( empty( $metadata['width'] ) || empty( $metadata['height'] ) ) {
        update_post_meta( $attachment_id, '_total_pixels', 0 );
        update_post_meta( $attachment_id, '_dimensions', '' );
        return;
    }

    $width  = (int) $metadata['width'];
    $height = (int) $metadata['height'];
    $pixels = $width * $height;

    update_post_meta( $attachment_id, '_total_pixels', $pixels );
    update_post_meta( $attachment_id, '_dimensions', $width . 'x' . $height );
}
