<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PCG_Metaboxes {

    const PRICE_META_KEY   = 'politeia_program_price';
    const COURSES_META_KEY = 'politeia_program_courses';
    const AJAX_ACTION      = 'pcg_search_courses';
    const NONCE_FIELD      = 'pcg_program_details_nonce';

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register_metabox' ] );
        add_action( 'save_post', [ $this, 'save_metabox' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_search_courses' ] );
    }

    public function register_metabox() {
        add_meta_box(
            'pcg-program-details',
            __( 'Detalles del Programa Politeia', 'politeia-course-group' ),
            [ $this, 'render_metabox' ],
            'course_program',
            'normal',
            'default'
        );
    }

    public function render_metabox( $post ) {
        wp_nonce_field( 'pcg_program_details_action', self::NONCE_FIELD );

        $price   = get_post_meta( $post->ID, self::PRICE_META_KEY, true );
        $courses = $this->get_saved_courses( $post->ID );

        $course_ids = array_map( 'absint', array_keys( $courses ) );
        ?>
        <div class="pcg-program-field components-base-control">
            <label for="pcg-program-price"><strong><?php esc_html_e( 'Precio', 'politeia-course-group' ); ?></strong></label>
            <input
                type="number"
                id="pcg-program-price"
                name="<?php echo esc_attr( self::PRICE_META_KEY ); ?>"
                value="<?php echo esc_attr( $price ); ?>"
                class="widefat"
                step="0.01"
                min="0"
            />
        </div>

        <div class="pcg-program-field components-base-control">
            <label for="pcg-program-courses-input"><strong><?php esc_html_e( 'Cursos', 'politeia-course-group' ); ?></strong></label>
            <div class="pcg-courses-field">
                <div class="pcg-courses-tags tagchecklist" aria-live="polite">
                    <?php foreach ( $courses as $course_id => $course_title ) : ?>
                        <span class="pcg-course-tag" data-course-id="<?php echo esc_attr( $course_id ); ?>" data-course-title="<?php echo esc_attr( $course_title ); ?>">
                            <span class="pcg-course-tag__label"><?php echo esc_html( $course_title ); ?></span>
                            <button type="button" class="pcg-course-tag__remove" aria-label="<?php esc_attr_e( 'Eliminar curso', 'politeia-course-group' ); ?>">&times;</button>
                        </span>
                    <?php endforeach; ?>
                </div>
                <input
                    type="text"
                    id="pcg-program-courses-input"
                    class="pcg-courses-input"
                    placeholder="<?php esc_attr_e( 'Busca y selecciona cursos...', 'politeia-course-group' ); ?>"
                    autocomplete="off"
                />
                <input type="hidden" class="pcg-courses-hidden" name="<?php echo esc_attr( self::COURSES_META_KEY ); ?>" value='<?php echo esc_attr( wp_json_encode( $course_ids ) ); ?>' />
                <div class="pcg-courses-suggestions"></div>
            </div>
        </div>
        <?php
    }

    public function save_metabox( $post_id ) {
        if ( ! isset( $_POST[ self::NONCE_FIELD ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), 'pcg_program_details_action' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'course_program' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        } else {
            return;
        }

        if ( isset( $_POST[ self::PRICE_META_KEY ] ) ) {
            $price_raw = wp_unslash( $_POST[ self::PRICE_META_KEY ] );
            if ( '' === $price_raw ) {
                delete_post_meta( $post_id, self::PRICE_META_KEY );
            } else {
                $price = sanitize_text_field( $price_raw );
                update_post_meta( $post_id, self::PRICE_META_KEY, $price );
            }
        } else {
            delete_post_meta( $post_id, self::PRICE_META_KEY );
        }

        if ( isset( $_POST[ self::COURSES_META_KEY ] ) ) {
            $raw_courses = wp_unslash( $_POST[ self::COURSES_META_KEY ] );
            $decoded     = json_decode( $raw_courses, true );

            if ( is_array( $decoded ) ) {
                $sanitized = array_values( array_unique( array_map( 'absint', $decoded ) ) );
                if ( ! empty( $sanitized ) ) {
                    update_post_meta( $post_id, self::COURSES_META_KEY, wp_json_encode( $sanitized ) );
                } else {
                    delete_post_meta( $post_id, self::COURSES_META_KEY );
                }
            } else {
                delete_post_meta( $post_id, self::COURSES_META_KEY );
            }
        } else {
            delete_post_meta( $post_id, self::COURSES_META_KEY );
        }
    }

    public function enqueue_assets( $hook ) {
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || 'course_program' !== $screen->post_type ) {
            return;
        }

        wp_enqueue_style( 'pcg-metaboxes', PCG_URL . 'assets/css/pcg-metaboxes.css', [], '1.0.0' );
        wp_enqueue_script( 'pcg-courses-field', PCG_URL . 'assets/js/pcg-courses-field.js', [ 'jquery' ], '1.0.0', true );
        wp_localize_script( 'pcg-courses-field', 'pcgCoursesField', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'pcg_courses_search' ),
            'action'  => self::AJAX_ACTION,
            'labels'  => [
                'remove' => __( 'Eliminar curso', 'politeia-course-group' ),
            ],
        ] );
    }

    public function ajax_search_courses() {
        check_ajax_referer( 'pcg_courses_search', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'No tienes permisos suficientes.', 'politeia-course-group' ), 403 );
        }

        $query = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

        $courses_query = new WP_Query( [
            'post_type'      => 'sfwd-courses',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            's'              => $query,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ] );

        $results = [];

        if ( ! empty( $courses_query->posts ) ) {
            foreach ( $courses_query->posts as $course_id ) {
                $results[] = [
                    'id'    => $course_id,
                    'title' => get_the_title( $course_id ),
                ];
            }
        }

        wp_send_json_success( $results );
    }

    private function get_saved_courses( $post_id ) {
        $raw = get_post_meta( $post_id, self::COURSES_META_KEY, true );

        if ( empty( $raw ) ) {
            return [];
        }

        $decoded = json_decode( $raw, true );

        if ( empty( $decoded ) || ! is_array( $decoded ) ) {
            return [];
        }

        $decoded = array_map( 'absint', $decoded );
        $decoded = array_filter( $decoded );

        if ( empty( $decoded ) ) {
            return [];
        }

        $courses = get_posts( [
            'post_type'      => 'sfwd-courses',
            'post__in'       => $decoded,
            'posts_per_page' => -1,
            'orderby'        => 'post__in',
        ] );

        $formatted = [];

        foreach ( $courses as $course ) {
            $formatted[ $course->ID ] = $course->post_title;
        }

        return $formatted;
    }
}
