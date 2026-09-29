<?php
/**
 * Meta boxes do CPT "oferta" e campos extras da taxonomia.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Registra a caixa de detalhes da oferta. */
function trowin_add_oferta_metabox() {
	add_meta_box(
		'trowin_oferta_detalhes',
		'Detalhes da oferta (TROWIN STORE)',
		'trowin_render_oferta_metabox',
		'oferta',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'trowin_add_oferta_metabox' );

function trowin_render_oferta_metabox( $post ) {
	wp_nonce_field( 'trowin_oferta_save', 'trowin_oferta_nonce' );

	$campos = array(
		'_trowin_asin'        => 'ASIN da Amazon',
		'_trowin_preco'       => 'Preço atual (em centavos)',
		'_trowin_preco_antigo' => 'Preço de tabela (centavos)',
		'_trowin_avaliacao'   => 'Avaliação (0 a 5)',
		'_trowin_avaliacoes'  => 'Quantidade de avaliações',
		'_trowin_badge'       => 'Selo (ex.: Oferta do dia)',
		'_trowin_cupom'       => 'Cupom',
		'_trowin_resumo'      => 'Resumo curto',
		'_trowin_pros'        => 'Prós (separados por |)',
		'_trowin_contras'     => 'Contras (separados por |)',
	);

	echo '<table class="form-table" role="presentation">';
	foreach ( $campos as $chave => $label ) {
		$valor = get_post_meta( $post->ID, $chave, true );
		echo '<tr><th scope="row"><label for="' . esc_attr( $chave ) . '">' . esc_html( $label ) . '</label></th>';
		echo '<td><input class="regular-text" type="text" id="' . esc_attr( $chave ) . '" name="' . esc_attr( $chave ) . '" value="' . esc_attr( $valor ) . '"></td></tr>';
	}

	$destaque = get_post_meta( $post->ID, '_trowin_destaque', true );
	$review   = get_post_meta( $post->ID, '_trowin_review', true );

	echo '<tr><th scope="row">Destaque</th><td><label><input type="checkbox" name="_trowin_destaque" value="1"' . checked( $destaque, '1', false ) . '> Exibir na home</label></td></tr>';
	echo '<tr><th scope="row">Review</th><td><label><input type="checkbox" name="_trowin_review" value="1"' . checked( $review, '1', false ) . '> Publicar na seção de reviews</label></td></tr>';
	echo '</table>';
}

function trowin_save_oferta_metabox( $post_id ) {
	if ( ! isset( $_POST['trowin_oferta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trowin_oferta_nonce'] ) ), 'trowin_oferta_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$texto = array(
		'_trowin_asin',
		'_trowin_preco',
		'_trowin_preco_antigo',
		'_trowin_avaliacao',
		'_trowin_avaliacoes',
		'_trowin_badge',
		'_trowin_cupom',
		'_trowin_resumo',
		'_trowin_pros',
		'_trowin_contras',
	);

	foreach ( $texto as $chave ) {
		if ( isset( $_POST[ $chave ] ) ) {
			update_post_meta( $post_id, $chave, sanitize_text_field( wp_unslash( $_POST[ $chave ] ) ) );
		}
	}

	update_post_meta( $post_id, '_trowin_destaque', isset( $_POST['_trowin_destaque'] ) ? '1' : '' );
	update_post_meta( $post_id, '_trowin_review', isset( $_POST['_trowin_review'] ) ? '1' : '' );
}
add_action( 'save_post_oferta', 'trowin_save_oferta_metabox' );

/* ---------------- Campos da taxonomia: ícone e tagline ---------------- */

function trowin_categoria_campos_novo() {
	wp_nonce_field( 'trowin_categoria_save', 'trowin_categoria_nonce' );
	echo '<div class="form-field"><label for="_trowin_icone">Ícone (emoji)</label>';
	echo '<input type="text" id="_trowin_icone" name="_trowin_icone" value=""></div>';
	echo '<div class="form-field"><label for="_trowin_tagline">Descrição curta</label>';
	echo '<input type="text" id="_trowin_tagline" name="_trowin_tagline" value=""></div>';
}
add_action( 'categoria_oferta_add_form_fields', 'trowin_categoria_campos_novo' );

function trowin_categoria_campos_editar( $term ) {
	wp_nonce_field( 'trowin_categoria_save', 'trowin_categoria_nonce' );
	$icone   = get_term_meta( $term->term_id, '_trowin_icone', true );
	$tagline = get_term_meta( $term->term_id, '_trowin_tagline', true );

	echo '<tr class="form-field"><th scope="row"><label for="_trowin_icone">Ícone (emoji)</label></th>';
	echo '<td><input type="text" id="_trowin_icone" name="_trowin_icone" value="' . esc_attr( $icone ) . '"></td></tr>';
	echo '<tr class="form-field"><th scope="row"><label for="_trowin_tagline">Descrição curta</label></th>';
	echo '<td><input type="text" id="_trowin_tagline" name="_trowin_tagline" value="' . esc_attr( $tagline ) . '"></td></tr>';
}
add_action( 'categoria_oferta_edit_form_fields', 'trowin_categoria_campos_editar' );

function trowin_categoria_salvar( $term_id ) {
	if ( ! isset( $_POST['trowin_categoria_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trowin_categoria_nonce'] ) ), 'trowin_categoria_save' ) ) {
		return;
	}

	if ( isset( $_POST['_trowin_icone'] ) ) {
		update_term_meta( $term_id, '_trowin_icone', sanitize_text_field( wp_unslash( $_POST['_trowin_icone'] ) ) );
	}

	if ( isset( $_POST['_trowin_tagline'] ) ) {
		update_term_meta( $term_id, '_trowin_tagline', sanitize_text_field( wp_unslash( $_POST['_trowin_tagline'] ) ) );
	}
}
add_action( 'created_categoria_oferta', 'trowin_categoria_salvar' );
add_action( 'edited_categoria_oferta', 'trowin_categoria_salvar' );
