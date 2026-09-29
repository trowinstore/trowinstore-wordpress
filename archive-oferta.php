<?php
/**
 * Arquivo do CPT oferta (/ofertas/).
 *
 * @package TROWIN_STORE
 */

get_header();

$categoria = isset( $_GET['categoria'] ) ? sanitize_text_field( wp_unslash( $_GET['categoria'] ) ) : '';
$ordenar   = isset( $_GET['ordenar'] ) ? sanitize_text_field( wp_unslash( $_GET['ordenar'] ) ) : 'destaque';

$ofertas = trowin_get_ofertas(
	array(
		'categoria' => $categoria,
		'ordenar'   => $ordenar,
		'limite'    => 120,
	)
);
?>

<div class="container" style="padding:40px 20px">
	<?php echo trowin_breadcrumbs( array( array( 'label' => 'Ofertas' ) ) ); ?>

	<header class="page-hero" style="margin-top:16px">
		<p class="eyebrow">Catálogo de ofertas</p>
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php echo esc_html( (string) count( $ofertas ) ); ?> produtos monitorados nesta seleção, com preços verificados diariamente.</p>
	</header>

	<div class="layout-sidebar" style="margin-top:32px">
		<div>
			<?php if ( ! empty( $ofertas ) ) : ?>
				<div class="grid grid-3">
					<?php foreach ( $ofertas as $oferta ) { trowin_oferta_card( $oferta ); } ?>
				</div>
			<?php else : ?>
				<div class="card" style="padding:40px;text-align:center">
					<h3>Nenhuma oferta cadastrada</h3>
					<p class="product-summary">Cadastre a primeira em Ofertas &rsaquo; Adicionar nova.</p>
				</div>
			<?php endif; ?>

			<div style="margin-top:32px"><?php echo trowin_disclosure(); ?></div>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
