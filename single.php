<?php
/**
 * Post do blog.
 *
 * @package TROWIN_STORE
 */

get_header();

while ( have_posts() ) :
	the_post();

	$categorias = get_the_category();
	$categoria  = ! empty( $categorias ) ? $categorias[0] : null;
	?>
	<div class="container" style="padding:40px 20px">
		<?php
		echo trowin_breadcrumbs(
			array(
				array( 'label' => 'Blog', 'href' => home_url( '/blog/' ) ),
				array( 'label' => $categoria ? $categoria->name : 'Post' ),
			)
		);
		?>

		<article class="layout-sidebar" style="margin-top:24px">
			<div>
				<p class="eyebrow" style="color:#00a6cc;font-weight:700;letter-spacing:.24em;text-transform:uppercase;font-size:12px;margin:0">
					<?php echo $categoria ? esc_html( $categoria->name ) : 'Blog'; ?>
				</p>
				<h1 style="margin-top:12px"><?php the_title(); ?></h1>

				<div class="entry-meta">
					<span style="font-weight:600;color:var(--ink)"><?php the_author(); ?></span>
					<span>&bull;</span>
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?></time>
					<span>&bull;</span>
					<span><?php echo esc_html( (string) trowin_tempo_leitura( get_the_ID() ) ); ?> min de leitura</span>
				</div>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="card" style="overflow:hidden;margin-top:24px">
						<?php the_post_thumbnail( 'large', array( 'style' => 'width:100%;height:auto' ) ); ?>
					</div>
				<?php endif; ?>

				<div style="margin-top:24px"><?php echo trowin_disclosure( true ); ?></div>

				<div class="entry-content" style="margin-top:24px">
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
				trowin_ad_slot( 'article', 'Publicidade' );

				if ( function_exists( 'comments_template' ) ) {
					comments_template();
				}
				?>

				<div class="cta-box">
					<h2>Pronto para economizar?</h2>
					<p>Veja as ofertas selecionadas hoje na nossa curadoria de tecnologia &mdash; atualizada todos os dias.</p>
					<div class="cta-actions">
						<a class="btn-primary" href="<?php echo esc_url( home_url( '/ofertas/' ) ); ?>">Ver Ofertas &#128293;</a>
						<a class="btn-outline" href="<?php echo esc_url( trowin_affiliate_search_url( 'ofertas tecnologia' ) ); ?>" target="_blank" rel="nofollow sponsored noopener">Buscar na Amazon</a>
					</div>
				</div>
			</div>

			<aside class="sidebar">
				<div class="widget">
					<h2>Este post contém links de afiliado</h2>
					<p>Comprando pelos nossos links você nos ajuda a manter o site no ar, sem pagar nada a mais por isso.</p>
					<a class="btn-ghost btn-block" style="margin-top:16px" href="<?php echo esc_url( home_url( '/disclosure/' ) ); ?>">Entenda como funciona</a>
				</div>

				<div class="widget">
					<h2>Últimos posts</h2>
					<ul>
						<?php
						$recentes = get_posts( array( 'numberposts' => 5, 'exclude' => get_the_ID() ) );
						foreach ( $recentes as $recente ) {
							echo '<li><a href="' . esc_url( get_permalink( $recente->ID ) ) . '">';
							echo '<span>' . esc_html( get_the_title( $recente->ID ) ) . '</span><span aria-hidden="true">&rarr;</span></a></li>';
						}
						?>
					</ul>
				</div>

				<?php trowin_ad_slot( 'sidebar', 'Publicidade' ); ?>
			</aside>
		</article>
	</div>
	<?php
endwhile;

get_footer();
