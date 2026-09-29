<?php
/**
 * Detalhe de uma oferta (CPT oferta).
 *
 * @package TROWIN_STORE
 */

get_header();

while ( have_posts() ) :
	the_post();

	$oferta    = trowin_get_oferta( get_the_ID() );
	$termos    = $oferta->termos;
	$termo     = ! empty( $termos ) ? $termos[0] : null;
	$desconto  = trowin_desconto( $oferta->preco, $oferta->preco_antigo );

	$trilha = array( array( 'label' => 'Ofertas', 'href' => home_url( '/ofertas/' ) ) );
	if ( $termo ) {
		$trilha[] = array( 'label' => $termo->name, 'href' => home_url( '/ofertas/?categoria=' . $termo->slug ) );
	}
	$trilha[] = array( 'label' => strlen( $oferta->titulo ) > 42 ? substr( $oferta->titulo, 0, 42 ) . '&hellip;' : $oferta->titulo );
	?>
	<div class="container" style="padding:40px 20px">
		<?php echo trowin_breadcrumbs( $trilha ); ?>

		<article class="layout-sidebar" style="margin-top:24px">
			<div>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="card" style="overflow:hidden">
						<?php the_post_thumbnail( 'large', array( 'style' => 'width:100%;height:auto;display:block' ) ); ?>
					</div>
				<?php elseif ( $oferta->imagem ) : ?>
					<div class="card" style="overflow:hidden">
						<img src="<?php echo esc_url( $oferta->imagem ); ?>" alt="<?php echo esc_attr( $oferta->titulo ); ?>" style="width:100%;height:auto">
					</div>
				<?php endif; ?>

				<h1 style="margin-top:32px"><?php the_title(); ?></h1>

				<div class="entry-meta">
					<?php echo trowin_stars( $oferta->avaliacao ); ?>
					<span><?php echo esc_html( number_format( $oferta->avaliacoes, 0, ',', '.' ) ); ?> avaliações de compradores</span>
					<?php if ( $termo ) : ?>
						<a class="review-tag" href="<?php echo esc_url( home_url( '/ofertas/?categoria=' . $termo->slug ) ); ?>"><?php echo esc_html( $termo->name ); ?></a>
					<?php endif; ?>
				</div>

				<div class="entry-content" style="margin-top:24px">
					<?php the_content(); ?>
				</div>

				<?php if ( ! empty( $oferta->pros ) || ! empty( $oferta->contras ) ) : ?>
					<div class="pros-cons">
						<?php if ( ! empty( $oferta->pros ) ) : ?>
							<div class="pros-box">
								<h3>Prós</h3>
								<ul>
									<?php foreach ( $oferta->pros as $pro ) : ?>
										<li><?php echo esc_html( $pro ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $oferta->contras ) ) : ?>
							<div class="cons-box">
								<h3>Contras</h3>
								<ul>
									<?php foreach ( $oferta->contras as $contra ) : ?>
										<li><?php echo esc_html( $contra ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div style="margin-top:32px"><?php echo trowin_disclosure(); ?></div>

				<?php trowin_ad_slot( 'article', 'Publicidade' ); ?>

				<?php
				$relacionadas = $termo ? trowin_get_ofertas( array( 'categoria' => $termo->slug, 'limite' => 4 ) ) : array();
				$relacionadas = array_filter(
					$relacionadas,
					function ( $item ) use ( $oferta ) {
						return $item->ID !== $oferta->ID;
					}
				);

				if ( ! empty( $relacionadas ) ) :
					?>
					<section style="margin-top:48px">
						<h2>Quem viu este produto também comparou</h2>
						<div class="grid grid-3">
							<?php foreach ( array_slice( $relacionadas, 0, 3 ) as $relacionada ) { trowin_oferta_card( $relacionada ); } ?>
						</div>
					</section>
					<?php
				endif;
				?>
			</div>

			<aside class="sidebar">
				<div class="widget">
					<p class="eyebrow" style="color:#00a6cc;font-weight:700;letter-spacing:.24em;text-transform:uppercase;font-size:12px;margin:0">Melhor preço hoje</p>
					<p style="font-family:Poppins,sans-serif;font-size:36px;font-weight:800;color:var(--ink);margin:12px 0 0"><?php echo esc_html( trowin_format_brl( $oferta->preco ) ); ?></p>
					<?php if ( $oferta->preco_antigo > $oferta->preco ) : ?>
						<p class="product-summary">De <span class="old"><?php echo esc_html( trowin_format_brl( $oferta->preco_antigo ) ); ?></span> por <strong style="color:#7a00b0"><?php echo (int) $desconto; ?>% menos</strong></p>
					<?php endif; ?>

					<?php if ( $oferta->cupom ) : ?>
						<div class="cupom-box">
							<span>Cupom disponível</span>
							<strong><?php echo esc_html( $oferta->cupom ); ?></strong>
						</div>
					<?php endif; ?>

					<a class="btn-primary btn-block" style="margin-top:24px" href="<?php echo esc_url( $oferta->afiliado ); ?>" target="_blank" rel="nofollow sponsored noopener">Ver Oferta na Amazon &#128293;</a>
					<a class="btn-ghost btn-block" style="margin-top:12px" href="<?php echo esc_url( home_url( '/ofertas/' ) ); ?>">Comparar outras ofertas</a>

					<ul class="check-list">
						<li>Preço verificado nas últimas 24 horas</li>
						<li>Envio para todo o Brasil</li>
						<li>Devolução em até 30 dias pela Amazon</li>
					</ul>

					<p class="legal-note">Os preços e a disponibilidade podem mudar a qualquer momento. Confira o valor final antes de finalizar a compra.</p>
				</div>
			</aside>
		</article>
	</div>
	<?php
endwhile;

get_footer();
