<?php
class PCG_Admin_Menu {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_page' ], 90 );
    }

    public function add_admin_page() {
        $parent_slug = 'learndash-lms';

        add_submenu_page(
            $parent_slug,
            'Programa Politeia',
            'Programa Politeia',
            'manage_options',
            'pcg-programa',
            [ $this, 'render_page' ]
        );
    }

    public function render_page() {
        $programs_url    = admin_url( 'edit.php?post_type=course_program' );
        $new_program_url = admin_url( 'post-new.php?post_type=course_program' );
        ?>
        <div class="wrap">
            <h1>Programa Politeia</h1>
            <p>Bienvenido al panel de administración del plugin <strong>Politeia Course Group</strong>.</p>

            <ul>
                <li><a href="<?php echo esc_url( $programs_url ); ?>">Ver Programas Filosóficos</a></li>
                <li><a href="<?php echo esc_url( $new_program_url ); ?>">Agregar nuevo Programa</a></li>
            </ul>
        </div>
        <?php
    }
}
