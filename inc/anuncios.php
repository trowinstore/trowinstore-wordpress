<?php
/**
 * Espaços publicitários (Google AdSense).
 *
 * Configure em: Aparência > Personalizar > TROWIN STORE: Anúncios
 * Ou via shortcode: [trowin_ad posicao="home"]
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Retorna o client id do AdSense (ca-pub-...). */
function trowin_adsense_client() {
	return trim( (string) get_theme_mod( 'trowin_adsense_client', '' ) );
}

/** Slots configurados no customizer. */
function trowin_adsense_slot( $posicao ) {
	$slots = array(
		'home'    => get_theme_mod( 'trowin_adsense_slot_home', '' ),
		'mid'     => get_theme_mod( 'trowin_adsense_slot_mid', '' ),
		'sidebar' => get_theme_mod( 'trowin_adsense_slot_sidebar', '' ),
		'article' => get_theme_mod( 'trowin_adsense_slot_article', '' ),
	);

	return isset( $slots[ $posicao ] ) ? trim( (string) $slots[ $posicao ] ) : '';
}

/**
 * Renderiza um bloco de anúncio responsivo.
 * Sem client/slot configurados exibe um placeholder discreto (nunca quebra o layout).
 */
function trowin_ad_slot( $posicao = 'home', $label = 'Publicidade' ) {
	$client = trowin_adsense_client();
	$slot   = trowin_adsense_slot( $posicao );

	echo '<aside class="ad-slot ad-slot--' . esc_attr( $posicao ) . '" aria-label="' . esc_attr( $label ) . '">';

	if ( $client && $slot ) {
		echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr( $client ) . '" crossorigin="anonymous"></script>';
		echo '<ins class="adsbygoogle" style="display:block" data-ad-client="' . esc_attr( $client ) . '" data-ad-slot="' . esc_attr( $slot ) . '" data-ad-format="auto" data-full-width-responsive="true"></ins>';
		echo '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
	} else {
		echo '<div class="ad-placeholder">' . esc_html( $label ) . '</div>';
	}

	echo '</aside>';
}

/** Shortcode [trowin_ad posicao="home"]. */
function trowin_ad_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'posicao' => 'home' ), $atts, 'trowin_ad' );

	ob_start();
	trowin_ad_slot( $atts['posicao'] );

	return ob_get_clean();
}
add_shortcode( 'trowin_ad', 'trowin_ad_shortcode' );

/** Registra as opções de anúncio no customizer. */
function trowin_anuncios_customizer( $wp_customize ) {
	$wp_customize->add_section(
		'trowin_anuncios',
		array(
			'title'    => 'TROWIN STORE: Anúncios',
			'priority' => 120,
		)
	);

	$campos = array(
		'trowin_adsense_client'      => 'AdSense Client ID (ca-pub-...)',
		'trowin_adsense_slot_home'   => 'Slot 1 - Após o hero (home)',
		'trowin_adsense_slot_mid'    => 'Slot 2 - Entre ofertas e reviews',
		'trowin_adsense_slot_sidebar' => 'Slot 3 - Sidebar / artigo',
		'trowin_adsense_slot_article' => 'Slot 4 - Dentro do artigo',
	);

	foreach ( $campos as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'trowin_anuncios',
				'type'    => 'text',
			)
		);
	}
}
