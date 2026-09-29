<?php
/**
 * Índice do blog (home do blog / archive padrão).
 *
 * @package TROWIN_STORE
 */

get_header();
?>

<div class="container" style="padding:40px 20px">
	<?php echo trowin_breadcrumbs( array( array( 'label' => 'Blog' ) ) ); ?>

	<header class="page-hero" style="margin-top:16px">
		<p class="eyebrow">Blog TROWIN</p>
		<h1><?php echo esc_html( is_home() ? 'Guias, reviews e dicas de tecnologia' : get_the_archive_title() ); ?></h1>
		<p>Conteúdo escrito por quem testa. Sem jargão, sem promessa milagrosa e com links de afiliado sempre sinalizados.</p>
	</header>

	<div class="grid grid-2" style="margin-top:40px">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$categorias = get_the_category();
				$categoria  = ! empty( $categorias ) ? $categorias[0]->name : 'Blog';
				?>
				<article <?php post_class( 'card post-card' ); ?>>
					<a class="thumb" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large' ); ?>
						<?php else : ?>
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder.png' ); ?>" alt="" loading="lazy">
						<?php endif; ?>
						<span class="cat-flag"><?php echo esc_html( $categoria ); ?></span>
					</a>
					<div class="body">
						<div class="entry-meta">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?></time>
							<span>&bull;</span>
							<span><?php echo esc_html( (string) trowin_tempo_leitura( get_the_ID() ) ); ?> min de leitura</span>
						</div>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php the_excerpt(); ?></p>
						<div class="foot">
							<span class="product-summary">Por <?php the_author(); ?></span>
							<a class="btn-ghost btn-sm" href="<?php the_permalink(); ?>">Leia Mais &rarr;</a>
						</div>
					</div>
				</article>
				<?php
			endwhile;
		else :
			?>
			<div class="card" style="padding:40px;text-align:center">
				<h3>Nenhum post encontrado</h3>
				<p class="product-summary">Publique o primeiro conteúdo em Posts &rsaquo; Adicionar novo.</p>
			</div>
			<?php
		endif;
		?>
	</div>

	<?php
	$paginacao = paginate_links( array( 'type' => 'array' ) );
	if ( $paginacao ) {
		echo '<nav class="pagination" aria-label="Paginação">';
		foreach ( $paginacao as $link ) {
			echo wp_kses_post( $link );
		}
		echo '</nav>';
	}
	?>
</div>

<?php
trowin_newsletter_section();
get_footer();
