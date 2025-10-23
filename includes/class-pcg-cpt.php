<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PCG_CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt() {
        if ( post_type_exists( 'course_program' ) ) {
            return;
        }

        $labels = [
            'name'                     => __( 'Programas Filosóficos', 'politeia-course-group' ),
            'singular_name'            => __( 'Programa Filosófico', 'politeia-course-group' ),
            'add_new'                  => __( 'Agregar nuevo', 'politeia-course-group' ),
            'add_new_item'             => __( 'Agregar nuevo Programa Filosófico', 'politeia-course-group' ),
            'edit_item'                => __( 'Editar Programa Filosófico', 'politeia-course-group' ),
            'new_item'                 => __( 'Nuevo Programa Filosófico', 'politeia-course-group' ),
            'view_item'                => __( 'Ver Programa Filosófico', 'politeia-course-group' ),
            'view_items'               => __( 'Ver Programas Filosóficos', 'politeia-course-group' ),
            'search_items'             => __( 'Buscar Programas Filosóficos', 'politeia-course-group' ),
            'not_found'                => __( 'No se encontraron Programas Filosóficos', 'politeia-course-group' ),
            'not_found_in_trash'       => __( 'No hay Programas Filosóficos en la papelera', 'politeia-course-group' ),
            'all_items'                => __( 'Todos los Programas Filosóficos', 'politeia-course-group' ),
            'archives'                 => __( 'Archivo de Programas Filosóficos', 'politeia-course-group' ),
            'attributes'               => __( 'Atributos del Programa Filosófico', 'politeia-course-group' ),
            'insert_into_item'         => __( 'Insertar en Programa Filosófico', 'politeia-course-group' ),
            'uploaded_to_this_item'    => __( 'Subido a este Programa Filosófico', 'politeia-course-group' ),
            'featured_image'           => __( 'Imagen destacada', 'politeia-course-group' ),
            'set_featured_image'       => __( 'Establecer imagen destacada', 'politeia-course-group' ),
            'remove_featured_image'    => __( 'Eliminar imagen destacada', 'politeia-course-group' ),
            'use_featured_image'       => __( 'Usar como imagen destacada', 'politeia-course-group' ),
            'filter_items_list'        => __( 'Filtrar lista de Programas Filosóficos', 'politeia-course-group' ),
            'items_list'               => __( 'Lista de Programas Filosóficos', 'politeia-course-group' ),
            'items_list_navigation'    => __( 'Navegación de lista de Programas Filosóficos', 'politeia-course-group' ),
            'name_admin_bar'           => __( 'Programa Filosófico', 'politeia-course-group' ),
            'item_published'           => __( 'Programa Filosófico publicado.', 'politeia-course-group' ),
            'item_updated'             => __( 'Programa Filosófico actualizado.', 'politeia-course-group' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'show_in_rest'       => true,
            'show_in_menu'       => false,
            'supports'           => [ 'title', 'editor', 'thumbnail' ],
            'has_archive'        => false,
            'rewrite'            => [ 'slug' => 'programa-filosofico' ],
        ];

        register_post_type( 'course_program', $args );
    }
}
