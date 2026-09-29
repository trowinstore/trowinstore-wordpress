<?php
/**
 * Tipos de conteúdo: oferta (CPT), categoria_oferta (taxonomia) e
 * CPTs privados para inscritos e mensagens.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function trowin_register_post_types() {
	register_post_type(
		'oferta',
		array(
			'labels'       => array(
				'name'          => 'Ofertas',
				'singular_name' => 'Oferta',
				'add_new'       => 'Adicionar nova',
				'add_new_item'  => 'Adicionar nova oferta',
				'edit_item'     => 'Editar oferta',
				'all_items'     => 'Todas as ofertas',
				'menu_name'     => 'Ofertas',
			),
			'public'       => true,
			'has_archive'  => 'ofertas',
			'rewrite'      => array( 'slug' => 'oferta', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
			'menu_icon'    => 'dashicons-tag',
			'show_in_rest' => true,
			'taxonomies'   => array( 'categoria_oferta' ),
		)
	);

	register_taxonomy(
		'categoria_oferta',
		array( 'oferta' ),
		array(
			'labels'       => array(
				'name'          => 'Categorias de oferta',
				'singular_name' => 'Categoria',
				'all_items'     => 'Todas as categorias',
			),
			'public'       => true,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => 'categoria-oferta' ),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'inscrito',
		array(
			'labels'      => array(
				'name'          => 'Inscritos',
				'singular_name' => 'Inscrito',
			),
			'public'      => false,
			'has_archive' => false,
			'supports'    => array( 'title' ),
			'menu_icon'   => 'dashicons-email-alt',
		)
	);

	register_post_type(
		'mensagem',
		array(
			'labels'      => array(
				'name'          => 'Mensagens',
				'singular_name' => 'Mensagem',
			),
			'public'      => false,
			'has_archive' => false,
			'supports'    => array( 'title', 'editor' ),
			'menu_icon'   => 'dashicons-format-chat',
		)
	);
}
add_action( 'init', 'trowin_register_post_types' );
