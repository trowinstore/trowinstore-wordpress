<?php
/**
 * TROWIN STORE — functions.php
 *
 * Tema de marketing de afiliados Amazon + AdSense.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TROWIN_VERSION', '1.0.0' );
define( 'TROWIN_DIR', get_template_directory() );
define( 'TROWIN_URI', get_template_directory_uri() );

require_once TROWIN_DIR . '/inc/afiliados.php';
require_once TROWIN_DIR . '/inc/anuncios.php';
require_once TROWIN_DIR . '/inc/componentes.php';
require_once TROWIN_DIR . '/inc/formularios.php';
require_once TROWIN_DIR . '/inc/post-types.php';

if ( is_admin() ) {
	require_once TROWIN_DIR . '/inc/metaboxes.php';
	require_once TROWIN_DIR . '/inc/conteudo-exemplo.php';
}

/** Suporte do tema + menus + sidebars. */
function trowin_setup() {
	load_theme_textdomain( 'trowinstore', TROWIN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 48, 'width' => 48, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	register_nav_menus(
		array(
			'principal' => 'Menu Principal',
			'rodape'    => 'Menu do Rodapé',
		)
	);

	register_sidebar(
		array(
			'name'          => 'Sidebar das Ofertas',
			'id'            => 'sidebar-ofertas',
			'description'   => 'Widgets exibidos na lateral das páginas de ofertas e blog.',
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => 'Rodapé',
			'id'            => 'rodape',
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'after_setup_theme', 'trowin_setup' );

/** CSS e JS do tema. */
function trowin_enqueue_assets() {
	wp_enqueue_style(
		'trowinstore-style',
		get_stylesheet_uri(),
		array(),
		TROWIN_VERSION
	);

	wp_enqueue_style(
		'trowinstore-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_script(
		'trowinstore-theme',
		TROWIN_URI . '/js/theme.js',
		array(),
		TROWIN_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'trowin_enqueue_assets' );

/** AdSense: script base no <head> + customizer. */
function trowin_adsense_script() {
	$client = trowin_adsense_client();

	if ( $client ) {
		echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr( $client ) . '" crossorigin="anonymous"></script>' . "\n";
	}
}
add_action( 'wp_head', 'trowin_adsense_script', 5 );
add_action( 'customize_register', 'trowin_anuncios_customizer' );

/** Processa newsletter/contato antes de renderizar. */
add_action( 'init', 'trowin_processar_formularios' );

/** Classes no <body> úteis para o CSS. */
function trowin_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'trowin-home';
	}
	if ( is_post_type_archive( 'oferta' ) || is_singular( 'oferta' ) ) {
		$classes[] = 'trowin-ofertas';
	}

	return $classes;
}
add_filter( 'body_class', 'trowin_body_classes' );

/** Tamanho do resumo automático. */
function trowin_excerpt_length( $length ) {
	return 26;
}
add_filter( 'excerpt_length', 'trowin_excerpt_length' );

/** Remove o emoji padrão "..." do resumo. */
function trowin_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'trowin_excerpt_more' );

/** Shortcode utilitário: [trowin_cta texto="Ver Oferta" asin="B0..." ] */
function trowin_cta_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'texto' => 'Ver Oferta',
			'asin'  => '',
			'busca' => '',
			'estilo' => 'primary',
		),
		$atts,
		'trowin_cta'
	);

	$url = $atts['asin'] ? trowin_affiliate_url( $atts['asin'] ) : trowin_affiliate_search_url( $atts['busca'] ? $atts['busca'] : 'ofertas tecnologia' );

	return '<a class="btn-' . esc_attr( $atts['estilo'] ) . ' btn-sm" href="' . esc_url( $url ) . '" target="_blank" rel="nofollow sponsored noopener">' . esc_html( $atts['texto'] ) . '</a>';
}
add_shortcode( 'trowin_cta', 'trowin_cta_shortcode' );

/** Shortcode: [trowin_ofertas limite="6" categoria="" ordenar="destaque"] */
function trowin_ofertas_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'limite'    => 6,
			'categoria' => '',
			'ordenar'   => 'destaque',
			'destaque'  => '',
			'colunas'   => 3,
		),
		$atts,
		'trowin_ofertas'
	);

	$ofertas = trowin_get_ofertas(
		array(
			'limite'    => (int) $atts['limite'],
			'categoria' => $atts['categoria'],
			'ordenar'   => $atts['ordenar'],
			'destaque'  => '1' === $atts['destaque'] ? true : null,
		)
	);

	if ( empty( $ofertas ) ) {
		return '<p>Nenhuma oferta disponível no momento.</p>';
	}

	ob_start();
	echo '<div class="grid grid-' . esc_attr( (int) $atts['colunas'] ) . '">';
	foreach ( $ofertas as $oferta ) {
		trowin_oferta_card( $oferta );
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'trowin_ofertas', 'trowin_ofertas_shortcode' );
