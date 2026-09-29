<?php
/**
 * Home do tema TROWIN STORE (single-page moderna).
 *
 * @package TROWIN_STORE
 */

get_header();

$hero = get_theme_mod( 'trowin_hero_imagem', 'https://images.pexels.com/photos/28448359/pexels-photo-28448359.jpeg?auto=compress&cs=tinysrgb&fit=crop&h=1200&w=2000' );
?>

<section class="hero">
	<div class="hero-media">
		<img src="<?php echo esc_url( $hero ); ?>" alt="Gadgets de tecnologia" width="2000" height="1200" fetchpriority="high">
	</div>
	<div class="hero-overlay"></div>
	<div class="hero-glow hero-glow-a"></div>
	<div class="hero-glow hero-glow-b"></div>

	<div class="hero-inner">
		<span class="hero-badge"><span class="hero-dot"></span> Curadoria de hoje &bull; <?php echo esc_html( gmdate_i18n( 'j \d\e F' ) ); ?></span>
		<h1>Encontre as Melhores Ofertas em <span class="gradient-text">Tecnologia</span></h1>
		<p class="lead">Gadgets, eletrônicos e produtos Amazon com preços imbatíveis &mdash; selecionados e verificados todos os dias pela equipe TROWIN STORE.</p>

		<div class="hero-actions">
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/ofertas/' ) ); ?>">Ver Ofertas &#128293;</a>
			<a class="btn-outline" href="<?php echo esc_url( trowin_affiliate_search_url( 'ofertas tecnologia' ) ); ?>" target="_blank" rel="nofollow sponsored noopener">Buscar na Amazon</a>
		</div>

		<dl class="hero-stats">
			<div><dt>1.200+</dt><dd>ofertas analisadas por mês</dd></div>
			<div><dt>R$ 4,2 mi</dt><dd>em economia gerada para leitores</dd></div>
			<div><dt>4,8/5</dt><dd>nota média dos reviews</dd></div>
		</dl>
	</div>
</section>

<?php trowin_ad_slot( 'home', 'Publicidade' ); ?>

<section class="section">
	<div class="container">
		<?php
		trowin_section_head(
			'Categorias',
			'Explore por categoria',
			'Dos fones com cancelamento de ruído aos acessórios de setup, navegue pelo que a gente realmente recomenda.',
			array( 'href' => home_url( '/ofertas/' ), 'label' => 'Ver todas as ofertas' )
		);

		$categorias = trowin_get_categorias();
		if ( ! empty( $categorias ) ) {
			echo '<div class="grid grid-3">';
			foreach ( $categorias as $categoria ) {
				trowin_categoria_card( $categoria );
			}
			echo '</div>';
		}
		?>
	</div>
</section>

<section class="section" style="padding-top:0">
	<div class="container">
		<?php
		trowin_section_head(
			'Ofertas em destaque',
			'Ofertas imperdíveis de hoje',
			'Preços monitorados diariamente. Quando o desconto aparece, ele vem para cá.',
			array( 'href' => home_url( '/ofertas/' ), 'label' => 'Explorar ofertas' )
		);

		$destaques = trowin_get_ofertas( array( 'destaque' => true, 'limite' => 6 ) );

		if ( empty( $destaques ) ) {
			$destaques = trowin_get_ofertas( array( 'limite' => 6 ) );
		}

		if ( ! empty( $destaques ) ) {
			echo '<div class="grid grid-3">';
			foreach ( $destaques as $oferta ) {
				trowin_oferta_card( $oferta );
			}
			echo '</div>';
		} else {
			echo '<p>Nenhuma oferta cadastrada ainda. Cadastre em Ofertas &rsaquo; Adicionar nova.</p>';
		}
		?>
	</div>
</section>

<?php trowin_ad_slot( 'mid', 'Publicidade' ); ?>

<section class="section" id="reviews">
	<div class="container">
		<?php
		trowin_section_head(
			'Reviews',
			'Análises honestas, sem enrolação',
			'Testamos no uso real e listamos prós e contras. Se o produto não vale, ele não entra na lista.',
			array( 'href' => home_url( '/blog/' ), 'label' => 'Ir para o blog' )
		);

		echo trowin_disclosure();

		$reviews = trowin_get_ofertas( array( 'review' => true, 'limite' => 3 ) );

		if ( ! empty( $reviews ) ) {
			echo '<div class="grid" style="margin-top:24px">';
			foreach ( $reviews as $oferta ) {
				trowin_review_item( $oferta );
			}
			echo '</div>';
		}
		?>
	</div>
</section>

<?php
trowin_newsletter_section();
get_footer();
