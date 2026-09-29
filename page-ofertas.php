<?php
/**
 * Template Name: Página de Ofertas
 * Template Post Type: page
 *
 * Lista de ofertas com filtros por categoria, ordenação e visão grade/lista.
 *
 * @package TROWIN_STORE
 */

get_header();

$categoria = isset( $_GET['categoria'] ) ? sanitize_text_field( wp_unslash( $_GET['categoria'] ) ) : '';
$ordenar   = isset( $_GET['ordenar'] ) ? sanitize_text_field( wp_unslash( $_GET['ordenar'] ) ) : 'destaque';
$busca     = isset( $_GET['busca'] ) ? sanitize_text_field( wp_unslash( $_GET['busca'] ) ) : '';
$vista     = isset( $_GET['v'] ) ? sanitize_text_field( wp_unslash( $_GET['v'] ) ) : 'grade';

$ordenar_opcoes = array(
	'destaque'    => 'Destaques',
	'menor-preco' => 'Menor preço',
	'maior-preco' => 'Maior preço',
	'avaliacao'   => 'Melhor avaliados',
	'desconto'    => 'Maior desconto',
);

if ( ! isset( $ordenar_opcoes[ $ordenar ] ) ) {
	$ordenar = 'destaque';
}

$ofertas = trowin_get_ofertas(
	array(
		'categoria' => $categoria,
		'ordenar'   => $ordenar,
		'busca'     => $busca,
		'limite'    => 120,
	)
);

$categorias = trowin_get_categorias();
$base_url   = home_url( '/ofertas/' );

if ( ! function_exists( 'trowin_url_filtro' ) ) :
	/** Monta a URL mantendo os filtros ativos. */
	function trowin_url_filtro( $base_url, $categoria, $ordenar, $busca, $vista ) {
		$params = array();

		if ( $categoria ) {
			$params['categoria'] = $categoria;
		}
		if ( 'destaque' !== $ordenar ) {
			$params['ordenar'] = $ordenar;
		}
		if ( $busca ) {
			$params['busca'] = $busca;
		}
		if ( 'grade' !== $vista ) {
			$params['v'] = $vista;
		}

		return $params ? $base_url . '?' . http_build_query( $params ) : $base_url;
	}
endif;
?>

<div class="container" style="padding:40px 20px">
	<?php
	echo trowin_breadcrumbs( array( array( 'label' => 'Ofertas' ) ) );
	?>

	<header class="page-hero" style="margin-top:16px">
		<p class="eyebrow">Catálogo de ofertas</p>
		<h1>Ofertas de tecnologia verificadas hoje</h1>
		<p>São <?php echo esc_html( (string) count( $ofertas ) ); ?> produtos nesta seleção. Use os filtros para encontrar exatamente o que você precisa.</p>
	</header>

	<div class="layout-sidebar" style="margin-top:32px">
		<div>
			<form method="get" action="<?php echo esc_url( $base_url ); ?>" class="filter-bar" role="search">
				<input type="search" name="busca" class="field" style="flex:1;min-width:220px" placeholder="Buscar por produto..." value="<?php echo esc_attr( $busca ); ?>" aria-label="Buscar produto">
				<input type="hidden" name="categoria" value="<?php echo esc_attr( $categoria ); ?>">
				<input type="hidden" name="ordenar" value="<?php echo esc_attr( $ordenar ); ?>">
				<button type="submit" class="btn-primary btn-sm">Buscar</button>
			</form>

			<div class="filter-bar">
				<a class="<?php echo ( '' === $categoria ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( trowin_url_filtro( $base_url, '', $ordenar, $busca, $vista ) ); ?>">Todas</a>
				<?php
				foreach ( $categorias as $termo ) {
					$ativo = ( $termo->slug === $categoria ) ? 'is-active' : '';
					$icone = get_term_meta( $termo->term_id, '_trowin_icone', true );
					echo '<a class="' . esc_attr( $ativo ) . '" href="' . esc_url( trowin_url_filtro( $base_url, $termo->slug, $ordenar, $busca, $vista ) ) . '">';
					echo esc_html( $icone . ' ' . $termo->name ) . '</a>';
				}
				?>
			</div>

			<div class="sort-bar">
				<div class="sort-links">
					<span style="font-weight:600;color:var(--muted)">Ordenar por:</span>
					<?php
					foreach ( $ordenar_opcoes as $chave => $label ) {
						$ativo = ( $chave === $ordenar ) ? 'is-active' : '';
						echo '<a class="' . esc_attr( $ativo ) . '" href="' . esc_url( trowin_url_filtro( $base_url, $categoria, $chave, $busca, $vista ) ) . '">' . esc_html( $label ) . '</a>';
					}
					?>
				</div>

				<div class="view-switch">
					<a class="<?php echo ( 'grade' === $vista ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( trowin_url_filtro( $base_url, $categoria, $ordenar, $busca, 'grade' ) ); ?>">Grade</a>
					<a class="<?php echo ( 'lista' === $vista ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( trowin_url_filtro( $base_url, $categoria, $ordenar, $busca, 'lista' ) ); ?>">Lista</a>
				</div>
			</div>

			<?php if ( empty( $ofertas ) ) : ?>
				<div class="card" style="padding:40px;text-align:center">
					<h3>Nenhum produto encontrado</h3>
					<p class="product-summary">Tente outra palavra-chave ou remova os filtros aplicados.</p>
					<a class="btn-primary btn-sm" href="<?php echo esc_url( $base_url ); ?>">Limpar filtros</a>
				</div>
			<?php elseif ( 'lista' === $vista ) : ?>
				<table class="deals-table">
					<thead>
						<tr>
							<th>Produto</th>
							<th>Avaliação</th>
							<th>Preço</th>
							<th style="text-align:right">Oferta</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $ofertas as $oferta ) { trowin_oferta_linha( $oferta ); } ?>
					</tbody>
				</table>
			<?php else : ?>
				<div class="grid grid-3">
					<?php foreach ( $ofertas as $oferta ) { trowin_oferta_card( $oferta ); } ?>
				</div>
			<?php endif; ?>

			<div style="margin-top:32px"><?php echo trowin_disclosure(); ?></div>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
