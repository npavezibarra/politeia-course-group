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

        $program_id       = get_the_ID();
        $groups_meta      = get_post_meta( $program_id, 'politeia_program_groups', true );
        $program_price    = get_post_meta( $program_id, 'politeia_program_price', true );
        $program_summary  = get_post_meta( $program_id, 'politeia_program_summary', true );

        if ( empty( $groups_meta ) ) {
            $group_ids = array();
        } elseif ( is_array( $groups_meta ) ) {
            $group_ids = array_filter( array_map( 'intval', $groups_meta ) );
        } else {
            $group_ids = array_filter( array_map( 'intval', (array) maybe_unserialize( $groups_meta ) ) );
        }

        $group_ids   = array_unique( $group_ids );
        $group_ids   = array_filter( $group_ids );
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

        <main id="primary" class="site-main" role="main">
            <div class="pcg-program-wrap">
                <header id="politeia-program-header">
                    <h1 class="pcg-program-title"><?php the_title(); ?></h1>

                    <div class="pcg-header-content">
                        <?php if ( ! empty( $program_summary ) ) : ?>
                            <p class="pcg-header-summary"><?php echo esc_html( $program_summary ); ?></p>
                        <?php endif; ?>

                        <?php if ( $group_count > 0 || ! empty( $program_price ) ) : ?>
                            <div class="pcg-header-tags">
                                <?php if ( $group_count > 0 ) : ?>
                                    <span class="pcg-header-tag">
                                        <?php printf( _n( '%s Ramo', '%s Ramos', $group_count, 'politeia-course-group' ), number_format_i18n( $group_count ) ); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ( ! empty( $program_price ) ) : ?>
                                    <span class="pcg-header-tag pcg-header-tag--muted">
                                        <?php echo esc_html( $program_price ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>

                <section id="politeia-program-content" class="pcg-program-content">
                    <div class="pcg-program-description">
                        <h2 class="pcg-section-title"><?php esc_html_e( 'Descripción Programa', 'politeia-course-group' ); ?></h2>
                        <div class="pcg-card pcg-description-card" id="descripcion-texto">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <aside class="pcg-program-ramos">
                        <h2 class="pcg-section-title"><?php esc_html_e( 'Ramos', 'politeia-course-group' ); ?></h2>
                        <div id="ramos-list" class="pcg-card pcg-ramos-list">
                            <?php if ( ! empty( $group_posts ) ) : ?>
                                <?php foreach ( $group_posts as $group_post ) :
                                    $thumbnail_url = get_the_post_thumbnail_url( $group_post, 'medium' );
                                    ?>
                                    <article class="pcg-ramo">
                                        <a class="pcg-ramo-thumb" href="<?php echo esc_url( get_permalink( $group_post ) ); ?>">
                                            <?php if ( $thumbnail_url ) : ?>
                                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title( $group_post ) ); ?>" />
                                            <?php else : ?>
                                                <span class="pcg-ramo-thumb__placeholder"><?php esc_html_e( 'Sin imagen', 'politeia-course-group' ); ?></span>
                                            <?php endif; ?>
                                        </a>
                                        <div class="pcg-ramo-info">
                                            <h3 class="pcg-ramo-title">
                                                <a href="<?php echo esc_url( get_permalink( $group_post ) ); ?>"><?php echo esc_html( get_the_title( $group_post ) ); ?></a>
                                            </h3>
                                            <span class="pcg-ramo-meta">
                                                <?php
                                                $group_type_object = get_post_type_object( $group_post->post_type );
                                                echo esc_html( $group_type_object ? $group_type_object->labels->singular_name : '' );
                                                ?>
                                            </span>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="pcg-ramos-empty"><?php esc_html_e( 'No hay ramos asociados a este programa por el momento.', 'politeia-course-group' ); ?></p>
                            <?php endif; ?>
                        </div>
                    </aside>
                </section>

                <section id="politeia-program-teachers" class="pcg-program-teachers">
                    <h2 class="pcg-section-title"><?php esc_html_e( 'Profesores', 'politeia-course-group' ); ?></h2>
                    <div id="profesores-list" class="pcg-card pcg-teachers">
                        <?php if ( ! empty( $teachers ) ) : ?>
                            <?php foreach ( $teachers as $teacher ) :
                                $teacher_name  = isset( $teacher['name'] ) ? $teacher['name'] : '';
                                $teacher_role  = isset( $teacher['role'] ) ? $teacher['role'] : '';
                                $teacher_image = isset( $teacher['image'] ) ? $teacher['image'] : '';
                                ?>
                                <article class="pcg-teacher">
                                    <div class="pcg-teacher-avatar">
                                        <?php if ( ! empty( $teacher_image ) ) : ?>
                                            <img src="<?php echo esc_url( $teacher_image ); ?>" alt="<?php echo esc_attr( $teacher_name ); ?>" />
                                        <?php else : ?>
                                            <span class="pcg-teacher-initial">
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
                                    <div class="pcg-teacher-info">
                                        <h3 class="pcg-teacher-name"><?php echo esc_html( $teacher_name ); ?></h3>
                                        <?php if ( ! empty( $teacher_role ) ) : ?>
                                            <p class="pcg-teacher-role"><?php echo esc_html( $teacher_role ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="pcg-teachers-empty"><?php esc_html_e( 'Pronto anunciaremos a los profesores de este programa.', 'politeia-course-group' ); ?></p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>

        <?php
    }
}

get_footer();
