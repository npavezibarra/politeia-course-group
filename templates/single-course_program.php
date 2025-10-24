<?php
/**
 * Single template for Programa Politeia posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();

        $program_id    = get_the_ID();
        $groups_meta   = get_post_meta( $program_id, 'politeia_program_groups', true );
        $program_price = get_post_meta( $program_id, 'politeia_program_price', true );
        $program_summary = get_post_meta( $program_id, 'politeia_program_summary', true );

        if ( empty( $groups_meta ) ) {
            $group_ids = array();
        } elseif ( is_array( $groups_meta ) ) {
            $group_ids = array_filter( array_map( 'intval', $groups_meta ) );
        } else {
            $group_ids = array_filter( array_map( 'intval', (array) maybe_unserialize( $groups_meta ) ) );
        }

        $group_ids = array_unique( $group_ids );
        $group_ids = array_filter( $group_ids );

        $group_posts = array();

        if ( ! empty( $group_ids ) ) {
            $group_posts = get_posts(
                array(
                    'post_type'      => 'groups',
                    'post__in'       => $group_ids,
                    'posts_per_page' => -1,
                    'orderby'        => 'post__in',
                )
            );
        }

        $group_count = count( $group_posts );

        if ( empty( $program_summary ) ) {
            $program_summary = wp_strip_all_tags( get_the_excerpt() );
        }

        $teachers = apply_filters(
            'politeia_program_teachers_placeholder',
            array(
                array(
                    'name'  => __( 'Próximamente', 'politeia-course-group' ),
                    'role'  => __( 'Profesor/a', 'politeia-course-group' ),
                    'image' => '',
                ),
            ),
            $program_id
        );
        ?>

        <style>
            .card-shadow {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            }
            .bg-primary {
                background-color: #2563eb;
            }
            .bg-secondary {
                background-color: #f3f4f6;
            }
        </style>

        <main id="primary" class="site-main bb-grid" role="main">
            <div class="bb-grid max-w-6xl mx-auto bg-white rounded-xl shadow-2xl overflow-hidden my-6">
                <header id="politeia-program-header" class="bg-primary text-white p-6 md:p-8 flex flex-col items-start w-full rounded-t-xl">
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2"><?php the_title(); ?></h1>
                    <div class="flex flex-col items-start space-y-2 w-full">
                        <?php if ( ! empty( $program_summary ) ) : ?>
                            <p class="text-white text-base font-semibold opacity-100 ml-1 md:ml-0"><?php echo esc_html( $program_summary ); ?></p>
                        <?php endif; ?>
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full">
                            <?php if ( $group_count > 0 ) : ?>
                                <p class="text-white text-base font-semibold opacity-100 ml-1 md:ml-0">
                                    <?php printf( _n( '%s Ramo', '%s Ramos', $group_count, 'politeia-course-group' ), number_format_i18n( $group_count ) ); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $program_price ) ) : ?>
                                <p class="text-white text-base font-semibold opacity-90 ml-1 md:ml-0">
                                    <?php echo esc_html( $program_price ); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>

                <section id="politeia-program-content" class="p-6 md:p-8 lg:grid lg:grid-cols-3 lg:gap-8 border-b border-gray-200">
                    <div class="lg:col-span-2 mb-6 lg:mb-0">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><?php esc_html_e( 'Descripción Programa', 'politeia-course-group' ); ?></h2>
                        <div class="bg-secondary p-5 rounded-lg border border-gray-300 h-full min-h-[150px] card-shadow leading-relaxed">
                            <div id="descripcion-texto" class="text-gray-700 prose max-w-none">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><?php esc_html_e( 'Ramos', 'politeia-course-group' ); ?></h2>
                        <div id="ramos-list" class="bg-white p-5 rounded-lg border border-gray-300 card-shadow space-y-4">
                            <?php if ( ! empty( $group_posts ) ) : ?>
                                <?php foreach ( $group_posts as $group_post ) :
                                    $thumbnail_url = get_the_post_thumbnail_url( $group_post, 'medium' );
                                    ?>
                                    <article class="flex items-center space-x-4">
                                        <a class="block w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0" href="<?php echo esc_url( get_permalink( $group_post ) ); ?>">
                                            <?php if ( $thumbnail_url ) : ?>
                                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title( $group_post ) ); ?>" class="w-full h-full object-cover" />
                                            <?php else : ?>
                                                <span class="w-full h-full flex items-center justify-center text-sm text-gray-500 bg-gray-200"><?php esc_html_e( 'Sin imagen', 'politeia-course-group' ); ?></span>
                                            <?php endif; ?>
                                        </a>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="<?php echo esc_url( get_permalink( $group_post ) ); ?>" class="text-primary hover:underline"><?php echo esc_html( get_the_title( $group_post ) ); ?></a>
                                            </h3>
                                            <span class="text-sm text-gray-500">
                                                <?php
                                                $group_type_object = get_post_type_object( $group_post->post_type );
                                                echo esc_html( $group_type_object ? $group_type_object->labels->singular_name : '' );
                                                ?>
                                            </span>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="text-sm text-gray-500"><?php esc_html_e( 'No hay ramos asociados a este programa por el momento.', 'politeia-course-group' ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section id="politeia-program-teachers" class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2"><?php esc_html_e( 'Profesores', 'politeia-course-group' ); ?></h2>
                    <div id="profesores-list" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                        <?php if ( ! empty( $teachers ) ) : ?>
                            <?php foreach ( $teachers as $teacher ) :
                                $teacher_name  = isset( $teacher['name'] ) ? $teacher['name'] : '';
                                $teacher_role  = isset( $teacher['role'] ) ? $teacher['role'] : '';
                                $teacher_image = isset( $teacher['image'] ) ? $teacher['image'] : '';
                                ?>
                                <article class="flex flex-col items-center text-center bg-secondary rounded-lg p-4 border border-gray-200 card-shadow">
                                    <div class="w-20 h-20 rounded-full overflow-hidden bg-white mb-3 flex items-center justify-center">
                                        <?php if ( ! empty( $teacher_image ) ) : ?>
                                            <img src="<?php echo esc_url( $teacher_image ); ?>" alt="<?php echo esc_attr( $teacher_name ); ?>" class="w-full h-full object-cover" />
                                        <?php else : ?>
                                            <span class="text-2xl font-semibold text-primary">
                                                <?php
                                                $teacher_initial = '';
                                                if ( ! empty( $teacher_name ) ) {
                                                    if ( function_exists( 'mb_substr' ) ) {
                                                        $teacher_initial = mb_substr( $teacher_name, 0, 1 );
                                                    } else {
                                                        $teacher_initial = substr( $teacher_name, 0, 1 );
                                                    }
                                                }
                                                echo esc_html( $teacher_initial );
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo esc_html( $teacher_name ); ?></h3>
                                    <?php if ( ! empty( $teacher_role ) ) : ?>
                                        <p class="text-sm text-gray-600"><?php echo esc_html( $teacher_role ); ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-sm text-gray-500"><?php esc_html_e( 'Pronto anunciaremos a los profesores de este programa.', 'politeia-course-group' ); ?></p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>

        <?php
    }
}

get_footer();
