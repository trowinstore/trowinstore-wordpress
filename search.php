<?php
/**
 * Resultados de busca.
 *
 * @package TROWIN_STORE
 */

get_header();

$termo = get_search_query();
?>

<div class="container" style="padding:40px 20px">
	<?php echo trowin_breadcrumbs( array( array( 'label' => 'Buscar' ) ) ); ?>

	<header class="page-hero" style="margin-top:16px">
		<p class="eyebrow">Busca</p>
		<h1>Resultados para &ldquo;<?php echo esc_html( $termo ); ?>&rdquo;</h1>
		<p><?php echo esc_html( (string) $GLOBALS['wp_query']->found_posts ); ?> resultado(s) encontrado(s) em posts &mdash; as ofertas correspondentes aparecem abaixo.</p>

		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="filter-bar" style="margin-top:24px">
			<input type="search" name="s" class="field" style="flex:1;min-width:220px" value="<?php echo esc_attr( $termo ); ?>" placeholder="Buscar ofertas, reviews, guias..." aria-label="Buscar">
			<button type="submit" class="btn-primary btn-sm">Buscar</button>
		</form>
	</header>

	<?php
	$ofertas = trowin_get_ofertas( array( 'busca' => $termo, 'limite' => 12 ) );

	if ( ! empty( $ofertas ) ) :
		?>
		<section style="margin-top:40px">
			<h2>Ofertas (<?php echo esc_html( (string) count( $ofertas ) ); ?>)</h2>
			<div class="grid grid-3">
				<?php foreach ( $ofertas as $oferta ) { trowin_oferta_card( $oferta ); } ?>
			</div>
		</section>
		<?php
	endif;
	?>

	<section style="margin-top:48px">
		<h2>Conteúdos</h2>

		<?php if ( have_posts() ) : ?>
			<div class="grid" style="gap:16px;margin-top:24px">
				<?php
				while ( have_posts() ) :
					the_post();
					$categorias = get_the_category();
					$categoria  = ! empty( $categorias ) ? $categorias[0]->name : 'Blog';
					?>
					<article class="card search-result">
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%;height:auto;border-radius:12px' ) ); ?></a>
						<?php endif; ?>
						<div>
							<span class="cat-flag" style="position:static;display:inline-block"><?php echo esc_html( $categoria ); ?></span>
							<h3 style="margin:8px 0 0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="product-summary"><?php the_excerpt(); ?></p>
							<a class="btn-ghost btn-sm" href="<?php the_permalink(); ?>">Leia Mais &rarr;</a>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>
		<?php else : ?>
			<div class="card" style="padding:40px;text-align:center;margin-top:24px">
				<h3>Nenhum conteúdo encontrado</h3>
				<p class="product-summary">Tente termos como &ldquo;fone&rdquo;, &ldquo;smartwatch&rdquo; ou &ldquo;setup&rdquo;.</p>
				<div class="cta-actions" style="justify-content:center;margin-top:24px">
					<a class="btn-primary btn-sm" href="<?php echo esc_url( home_url( '/ofertas/' ) ); ?>">Ver todas as ofertas</a>
					<a class="btn-ghost btn-sm" href="<?php echo esc_url( trowin_affiliate_search_url( $termo ? $termo : 'ofertas tecnologia' ) ); ?>" target="_blank" rel="nofollow sponsored noopener">Buscar na Amazon</a>
				</div>
			</div>
		<?php endif; ?>
	</section>
</div>

<?php
trowin_newsletter_section();
get_footer();
