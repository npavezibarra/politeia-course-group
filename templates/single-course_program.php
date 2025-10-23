<?php get_header(); ?>
<main class="pcg-program">
<?php while ( have_posts() ) : the_post(); ?>
  <h1><?php the_title(); ?></h1>
  <div class="program-content"><?php the_content(); ?></div>

  <h3>Grupos Incluidos:</h3>
  <ul>
  <?php 
    $groups = get_field('related_groups');
    if ($groups) {
      foreach ($groups as $g) {
        echo '<li><a href="' . get_permalink($g) . '">' . get_the_title($g) . '</a></li>';
      }
    }
  ?>
  </ul>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
