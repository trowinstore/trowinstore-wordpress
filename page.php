<?php
/**
 * Página padrão (Sobre, Política, Termos, Disclosure...).
 *
 * @package TROWIN_STORE
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="container" style="padding:40px 20px">
		<?php echo trowin_breadcrumbs( array( array( 'label' => get_the_title() ) ) ); ?>

		<div class="page-legal" style="margin-top:24px">
			<p class="eyebrow" style="color:#00a6cc;font-weight:700;letter-spacing:.24em;text-transform:uppercase;font-size:12px;margin:0">Informações legais</p>
			<h1 style="margin-top:12px"><?php the_title(); ?></h1>
			<p class="product-summary">Última atualização: <?php echo esc_html( get_the_modified_date( 'j \d\e F \d\e Y' ) ); ?></p>

			<div class="entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="pagination">',
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php
			if ( function_exists( 'comments_template' ) ) {
				comments_template();
			}
			?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
