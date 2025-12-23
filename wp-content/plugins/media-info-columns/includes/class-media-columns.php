<?php
/**
 * Media Info Columns main class.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Media_Info_Columns {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'manage_media_columns', array( $this, 'add_columns' ) );
        add_action( 'manage_media_custom_column', array( $this, 'render_column' ), 10, 2 );
        add_filter( 'manage_upload_sortable_columns', array( $this, 'sortable_columns' ) );
        add_action( 'pre_get_posts', array( $this, 'handle_sorting_and_filtering' ) );
        add_action( 'restrict_manage_posts', array( $this, 'add_dimension_filter' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'add_attachment', 'mic_update_attachment_meta' );
        add_action( 'attachment_updated', array( $this, 'on_attachment_updated' ), 10, 3 );
        add_action( 'delete_attachment', array( $this, 'clear_dimensions_cache' ) );
    }

    /**
     * Add custom columns to the media library.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function add_columns( $columns ) {
        $columns['dimensions'] = __( 'Dimensions', 'media-info-columns' );
        $columns['filesize']   = __( 'File Size', 'media-info-columns' );
        return $columns;
    }

    /**
     * Render custom column content.
     *
     * @param string $column_name The column name.
     * @param int    $post_id     The attachment ID.
     */
    public function render_column( $column_name, $post_id ) {
        switch ( $column_name ) {
            case 'dimensions':
                $this->render_dimensions_column( $post_id );
                break;
            case 'filesize':
                $this->render_filesize_column( $post_id );
                break;
        }
    }

    /**
     * Render the dimensions column.
     *
     * @param int $post_id The attachment ID.
     */
    private function render_dimensions_column( $post_id ) {
        if ( ! wp_attachment_is_image( $post_id ) ) {
            echo '<span aria-hidden="true">—</span>';
            return;
        }

        $dimensions = get_post_meta( $post_id, '_dimensions', true );

        if ( empty( $dimensions ) ) {
            $metadata = wp_get_attachment_metadata( $post_id );
            if ( ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
                $dimensions = $metadata['width'] . 'x' . $metadata['height'];
            }
        }

        if ( ! empty( $dimensions ) ) {
            echo esc_html( $dimensions );
        } else {
            echo '<span aria-hidden="true">—</span>';
        }
    }

    /**
     * Render the file size column.
     *
     * @param int $post_id The attachment ID.
     */
    private function render_filesize_column( $post_id ) {
        $file = get_attached_file( $post_id );

        if ( $file && file_exists( $file ) ) {
            $size = filesize( $file );
            echo esc_html( size_format( $size, 2 ) );
        } else {
            echo '<span aria-hidden="true">—</span>';
        }
    }

    /**
     * Make the dimensions column sortable.
     *
     * @param array $columns Sortable columns.
     * @return array Modified sortable columns.
     */
    public function sortable_columns( $columns ) {
        $columns['dimensions'] = 'dimensions';
        return $columns;
    }

    /**
     * Handle sorting and filtering in pre_get_posts.
     *
     * @param WP_Query $query The query object.
     */
    public function handle_sorting_and_filtering( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || 'upload' !== $screen->id ) {
            return;
        }

        // Handle sorting by dimensions.
        if ( 'dimensions' === $query->get( 'orderby' ) ) {
            $query->set( 'meta_key', '_total_pixels' );
            $query->set( 'orderby', 'meta_value_num' );
        }

        // Handle filtering by dimensions.
        if ( ! empty( $_GET['mic_dimension'] ) ) {
            $dimension = sanitize_text_field( wp_unslash( $_GET['mic_dimension'] ) );
            $query->set( 'meta_key', '_dimensions' );
            $query->set( 'meta_value', $dimension );
        }
    }

    /**
     * Add the dimension filter dropdown.
     */
    public function add_dimension_filter() {
        $screen = get_current_screen();

        if ( ! $screen || 'upload' !== $screen->id ) {
            return;
        }

        $dimensions = $this->get_common_dimensions();

        if ( empty( $dimensions ) ) {
            return;
        }

        $selected = isset( $_GET['mic_dimension'] ) ? sanitize_text_field( wp_unslash( $_GET['mic_dimension'] ) ) : '';

        ?>
        <select name="mic_dimension" id="mic-dimension-filter">
            <option value=""><?php esc_html_e( 'All Dimensions', 'media-info-columns' ); ?></option>
            <?php foreach ( $dimensions as $dim ) : ?>
                <option value="<?php echo esc_attr( $dim['dimension'] ); ?>" <?php selected( $selected, $dim['dimension'] ); ?>>
                    <?php echo esc_html( sprintf( '%s (%d)', $dim['dimension'], $dim['count'] ) ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * Get the most common dimensions from the media library.
     *
     * @return array Array of dimensions with counts.
     */
    private function get_common_dimensions() {
        $cached = get_transient( 'mic_common_dimensions' );

        if ( false !== $cached ) {
            return $cached;
        }

        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT meta_value AS dimension, COUNT(*) AS count
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_dimensions'
            AND meta_value != ''
            GROUP BY meta_value
            ORDER BY count DESC
            LIMIT 10",
            ARRAY_A
        );

        if ( empty( $results ) ) {
            $results = array();
        }

        set_transient( 'mic_common_dimensions', $results, HOUR_IN_SECONDS );

        return $results;
    }

    /**
     * Handle attachment update.
     *
     * @param int     $post_id      The attachment ID.
     * @param WP_Post $post_after   Post object after update.
     * @param WP_Post $post_before  Post object before update.
     */
    public function on_attachment_updated( $post_id, $post_after, $post_before ) {
        mic_update_attachment_meta( $post_id );
        $this->clear_dimensions_cache();
    }

    /**
     * Clear the dimensions cache.
     */
    public function clear_dimensions_cache() {
        delete_transient( 'mic_common_dimensions' );
    }

    /**
     * Enqueue admin styles.
     *
     * @param string $hook The current admin page.
     */
    public function enqueue_admin_styles( $hook ) {
        if ( 'upload.php' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'media-info-columns-admin',
            MIC_PLUGIN_URL . 'assets/admin.css',
            array(),
            MIC_VERSION
        );
    }
}
