<?php
/**
 * Sidebar das páginas de ofertas e blog.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside class="sidebar" role="complementary">
	<?php
	if ( is_active_sidebar( 'sidebar-ofertas' ) ) {
		dynamic_sidebar( 'sidebar-ofertas' );
	} else {
		trowin_ad_slot( 'sidebar', 'Publicidade' );

		$categorias = trowin_get_categorias();
		if ( ! empty( $categorias ) ) {
			echo '<div class="widget"><h2>Categorias</h2><ul>';
			foreach ( $categorias as $categoria ) {
				echo '<li><a href="' . esc_url( home_url( '/ofertas/?categoria=' . $categoria->slug ) ) . '">';
				echo '<span>' . esc_html( get_term_meta( $categoria->term_id, '_trowin_icone', true ) ) . ' ' . esc_html( $categoria->name ) . '</span>';
				echo '<span aria-hidden="true">&rarr;</span></a></li>';
			}
			echo '</ul></div>';
		}
		?>

		<div class="widget widget-cta">
			<h2>Quer um alerta de preço?</h2>
			<p>Assine a newsletter e receba os cupons e quedas de preço direto no e-mail.</p>
			<a class="btn-primary btn-block" href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>">Assinar newsletter</a>
		</div>
		<?php
	}
	?>
</aside>
