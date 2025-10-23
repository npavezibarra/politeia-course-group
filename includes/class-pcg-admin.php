<?php
class PCG_Admin_Menu {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    public function register_menu() {
        add_submenu_page(
            'edit.php?post_type=groups',
            'Programa Politeia',
            'Programa Politeia',
            'manage_options',
            'pcg-programa',
            [ $this, 'render_page' ]
        );
    }

    public function render_page() {
        $programs_url = admin_url( 'edit.php?post_type=course_program' );
        $new_program_url = admin_url( 'post-new.php?post_type=course_program' );
        ?>
        <div class="wrap">
            <h1>Programa Politeia</h1>
            <p><a href="<?php echo esc_url( $programs_url ); ?>">Ver Programas</a></p>
            <p><a href="<?php echo esc_url( $new_program_url ); ?>">Agregar Nuevo Programa</a></p>
        </div>
        <?php
    }
}
