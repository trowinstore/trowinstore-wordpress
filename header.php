<?php
/**
 * Header do tema TROWIN STORE.
 *
 * @package TROWIN_STORE
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>
<a class="skip-link screen-reader-text" href="#conteudo">Pular para o conteúdo</a>

<header class="site-header" role="banner">
	<div class="topbar">
		<div class="container">
			<span>&#128293; Ofertas atualizadas hoje &bull; Cupons verificados pela equipe TROWIN STORE</span>
			<span>
				<span>Envio para todo o Brasil</span>
				<a href="mailto:suporte@trowinstore.com.br">suporte@trowinstore.com.br</a>
			</span>
		</div>
	</div>

	<div class="container header-main">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="brand-mark">T</span>
			<span>TROWIN <span class="gradient-text">STORE</span></span>
		</a>

		<?php
		if ( has_nav_menu( 'principal' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'container'      => 'nav',
					'container_class' => 'main-nav',
					'menu_class'     => '',
					'depth'          => 1,
				)
			);
		} else {
			trowin_menu_fallback();
		}
		?>

		<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="s-header">Buscar</label>
			<input type="search" id="s-header" name="s" placeholder="Buscar ofertas..." value="<?php echo esc_attr( get_search_query() ); ?>">
			<button type="submit" aria-label="Buscar">&#128269;</button>
		</form>

		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-mobile" aria-label="Abrir menu">&#9776;</button>
	</div>

	<div class="mobile-nav" id="menu-mobile">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="search-form">
				<input type="search" name="s" placeholder="Buscar ofertas..." aria-label="Buscar ofertas" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit" aria-label="Buscar">&#128269;</button>
			</div>
		</form>
		<?php
		$itens = array(
			'/'         => 'Home',
			'/ofertas/' => 'Ofertas',
			'/#reviews' => 'Reviews',
			'/blog/'    => 'Blog',
			'/contato/' => 'Contato',
		);
		foreach ( $itens as $url => $label ) {
			echo '<a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a>';
		}
		?>
	</div>
</header>

<main id="conteudo" role="main">
