<?php
/**
 * Helpers de afiliado Amazon, formatação e consulta de ofertas.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TROWIN_AMAZON_TAG', 'tstore0db2006-20' );
define( 'TROWIN_AMAZON_DOMAIN', 'www.amazon.com.br' );
define( 'TROWIN_UTM_SOURCE', 'ofertasamazon' );
define( 'TROWIN_UTM_MEDIUM', 'affiliate' );
define( 'TROWIN_UTM_CAMPAIGN', 'produtos-amazon' );

/** Monta o link de afiliado Amazon com a tag da loja + UTMs. */
function trowin_affiliate_url( $asin, $extra = array() ) {
	$params = array(
		'tag'          => TROWIN_AMAZON_TAG,
		'linkCode'     => 'ogi',
		'language'     => 'pt_BR',
		'utm_source'   => TROWIN_UTM_SOURCE,
		'utm_medium'   => TROWIN_UTM_MEDIUM,
		'utm_campaign' => TROWIN_UTM_CAMPAIGN,
	);

	if ( is_array( $extra ) ) {
		foreach ( $extra as $key => $value ) {
			if ( '' !== $value && null !== $value ) {
				$params[ $key ] = $value;
			}
		}
	}

	return 'https://' . TROWIN_AMAZON_DOMAIN . '/dp/' . rawurlencode( $asin ) . '/?' . http_build_query( $params, '', '&amp;' );
}

/** Link de busca na Amazon já monetizado. */
function trowin_affiliate_search_url( $keywords ) {
	$params = array(
		'k'            => $keywords,
		'tag'          => TROWIN_AMAZON_TAG,
		'utm_source'   => TROWIN_UTM_SOURCE,
		'utm_medium'   => TROWIN_UTM_MEDIUM,
		'utm_campaign' => TROWIN_UTM_CAMPAIGN,
	);

	return 'https://' . TROWIN_AMAZON_DOMAIN . '/s?' . http_build_query( $params, '', '&amp;' );
}

/** Preço em centavos -> R$ 1.234,56 */
function trowin_format_brl( $cents ) {
	$valor = (float) $cents / 100;
	return 'R$ ' . number_format( $valor, 2, ',', '.' );
}

/** Percentual de desconto entre preço atual e preço de tabela. */
function trowin_desconto( $preco, $preco_antigo ) {
	$preco        = (int) $preco;
	$preco_antigo = (int) $preco_antigo;

	if ( $preco_antigo <= 0 || $preco_antigo <= $preco ) {
		return 0;
	}

	return (int) round( ( ( $preco_antigo - $preco ) / $preco_antigo ) * 100 );
}

/** Normaliza um post do CPT "oferta" em um objeto pronto para os templates. */
function trowin_get_oferta( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$post    = get_post( $post_id );

	$oferta             = new stdClass();
	$oferta->ID         = (int) $post_id;
	$oferta->titulo     = get_the_title( $post_id );
	$oferta->slug       = $post ? $post->post_name : '';
	$oferta->link       = get_permalink( $post_id );
	$oferta->descricao  = $post ? $post->post_content : '';
	$oferta->imagem     = get_the_post_thumbnail_url( $post_id, 'large' );
	if ( ! $oferta->imagem ) {
		// Fallback: URL externa salva pela importação de conteúdo de exemplo.
		$oferta->imagem = (string) get_post_meta( $post_id, '_trowin_imagem', true );
	}
	$oferta->resumo     = (string) get_post_meta( $post_id, '_trowin_resumo', true );
	$oferta->asin       = (string) get_post_meta( $post_id, '_trowin_asin', true );
	$oferta->preco      = (int) get_post_meta( $post_id, '_trowin_preco', true );
	$oferta->preco_antigo = (int) get_post_meta( $post_id, '_trowin_preco_antigo', true );
	$oferta->avaliacao  = (float) get_post_meta( $post_id, '_trowin_avaliacao', true );
	$oferta->avaliacoes = (int) get_post_meta( $post_id, '_trowin_avaliacoes', true );
	$oferta->badge      = (string) get_post_meta( $post_id, '_trowin_badge', true );
	$oferta->cupom      = (string) get_post_meta( $post_id, '_trowin_cupom', true );
	$oferta->destaque   = '1' === (string) get_post_meta( $post_id, '_trowin_destaque', true );
	$oferta->review     = '1' === (string) get_post_meta( $post_id, '_trowin_review', true );
	$oferta->pros       = trowin_explode_lista( get_post_meta( $post_id, '_trowin_pros', true ) );
	$oferta->contras    = trowin_explode_lista( get_post_meta( $post_id, '_trowin_contras', true ) );
	$oferta->termos     = trowin_get_termos_oferta( $post_id );

	if ( '' === $oferta->resumo && $post ) {
		$oferta->resumo = wp_trim_words( wp_strip_all_tags( $post->post_content ), 26, '...' );
	}

	if ( ! $oferta->avaliacao ) {
		$oferta->avaliacao = 5.0;
	}

	$oferta->desconto = trowin_desconto( $oferta->preco, $oferta->preco_antigo );
	$oferta->afiliado = trowin_affiliate_url( $oferta->asin, array( 'utm_content' => 'produto-' . $oferta->slug ) );

	return $oferta;
}

/** Converte "a|b|c" em array. */
function trowin_explode_lista( $valor ) {
	$valor = (string) $valor;

	if ( '' === trim( $valor ) ) {
		return array();
	}

	$itens = array_map( 'trim', explode( '|', $valor ) );

	return array_values( array_filter( $itens ) );
}

/** Categorias (taxonomia categoria_oferta) de uma oferta. */
function trowin_get_termos_oferta( $post_id ) {
	$termos = get_the_terms( $post_id, 'categoria_oferta' );

	return ( is_array( $termos ) && ! empty( $termos ) ) ? $termos : array();
}

/** Todas as categorias de oferta. */
function trowin_get_categorias() {
	$termos = get_terms(
		array(
			'taxonomy'   => 'categoria_oferta',
			'hide_empty' => false,
		)
	);

	return ( is_array( $termos ) && ! empty( $termos ) ) ? $termos : array();
}

/**
 * Consulta ofertas do CPT.
 *
 * $args: categoria (slug), ordenar (destaque|menor-preco|maior-preco|avaliacao|desconto),
 *        busca, limite, destaque (bool), review (bool).
 */
function trowin_get_ofertas( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'categoria' => '',
			'ordenar'   => 'destaque',
			'busca'     => '',
			'limite'    => 60,
			'destaque'  => null,
			'review'    => null,
		)
	);

	$query_args = array(
		'post_type'      => 'oferta',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $args['limite'],
	);

	if ( ! empty( $args['categoria'] ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'categoria_oferta',
				'field'    => 'slug',
				'terms'    => $args['categoria'],
			),
		);
	}

	if ( ! empty( $args['busca'] ) ) {
		$query_args['s'] = $args['busca'];
	}

	$meta_query = array();

	if ( true === $args['destaque'] ) {
		$meta_query[] = array( 'key' => '_trowin_destaque', 'value' => '1' );
	}

	if ( true === $args['review'] ) {
		$meta_query[] = array( 'key' => '_trowin_review', 'value' => '1' );
	}

	if ( ! empty( $meta_query ) ) {
		$query_args['meta_query'] = $meta_query;
	}

	switch ( $args['ordenar'] ) {
		case 'menor-preco':
			$query_args['meta_key'] = '_trowin_preco';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'ASC';
			break;
		case 'maior-preco':
			$query_args['meta_key'] = '_trowin_preco';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;
		case 'avaliacao':
			$query_args['meta_key'] = '_trowin_avaliacao';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;
		default:
			$query_args['meta_key'] = '_trowin_avaliacao';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;
	}

	$query   = new WP_Query( $query_args );
	$ofertas = array();

	while ( $query->have_posts() ) {
		$query->the_post();
		$ofertas[] = trowin_get_oferta( get_the_ID() );
	}

	wp_reset_postdata();

	if ( 'desconto' === $args['ordenar'] ) {
		usort(
			$ofertas,
			function ( $a, $b ) {
				return $b->desconto - $a->desconto;
			}
		);
	}

	if ( 'destaque' === $args['ordenar'] ) {
		usort(
			$ofertas,
			function ( $a, $b ) {
				if ( $a->destaque !== $b->destaque ) {
					return $a->destaque ? -1 : 1;
				}
				return ( $b->avaliacao * 100 ) - ( $a->avaliacao * 100 );
			}
		);
	}

	return $ofertas;
}
