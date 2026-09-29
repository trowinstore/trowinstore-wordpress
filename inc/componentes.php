<?php
/**
 * Componentes reutilizáveis do tema (cards, estrelas, breadcrumbs, disclosure).
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Fallback do menu principal (quando nenhum menu foi atribuído). */
function trowin_menu_fallback() {
	$itens = array(
		'/'         => 'Home',
		'/ofertas/' => 'Ofertas',
		'/#reviews' => 'Reviews',
		'/blog/'    => 'Blog',
		'/contato/' => 'Contato',
	);

	echo '<nav class="main-nav">';
	foreach ( $itens as $url => $label ) {
		echo '<a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '</nav>';
}

/** Tempo estimado de leitura em minutos. */
function trowin_tempo_leitura( $post_id = null ) {
	$post     = get_post( $post_id ? $post_id : get_the_ID() );
	$palavras = $post ? str_word_count( wp_strip_all_tags( $post->post_content ) ) : 0;

	return max( 1, (int) ceil( $palavras / 200 ) );
}

/** Estrelas de avaliação. */
function trowin_stars( $rating ) {
	$rating = (float) $rating;
	$cheias = (int) floor( $rating );

	$html  = '<span class="stars" aria-label="' . esc_attr( sprintf( 'Avaliação %s de 5', number_format( $rating, 1, ',', '.' ) ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$html .= ( $i <= $cheias ) ? '<span>★</span>' : '<span class="off">★</span>';
	}
	$html .= '<span class="stars-note">' . esc_html( number_format( $rating, 1, ',', '.' ) ) . '</span>';
	$html .= '</span>';

	return $html;
}

/** Breadcrumbs: Home > ... > atual */
function trowin_breadcrumbs( $items = array() ) {
	$trail = array_merge(
		array( array( 'label' => 'Home', 'href' => home_url( '/' ) ) ),
		$items
	);

	$html = '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol>';
	$total = count( $trail );

	foreach ( $trail as $index => $item ) {
		$last = ( $index === $total - 1 );
		$html .= '<li>';

		if ( ! $last && ! empty( $item['href'] ) ) {
			$html .= '<a href="' . esc_url( $item['href'] ) . '">' . esc_html( $item['label'] ) . '</a>';
			$html .= ' <span aria-hidden="true">/</span>';
		} else {
			$html .= '<span class="current">' . esc_html( $item['label'] ) . '</span>';
		}

		$html .= '</li>';
	}

	$html .= '</ol></nav>';

	return $html;
}

/** Nota de disclosure de afiliado (obrigatória em posts com links). */
function trowin_disclosure( $compact = false ) {
	$html  = '<div class="disclosure' . ( $compact ? ' compact' : '' ) . '"><p>';
	$html .= '<strong>Divulgação de afiliado:</strong> este conteúdo contém links de afiliado da Amazon. ';
	$html .= 'Se você comprar por um dos nossos links, recebemos uma pequena comissão — sem nenhum custo adicional para você. ';
	$html .= 'Isso ajuda a manter o projeto e não altera nossa avaliação sobre os produtos. ';
	$html .= '<a href="' . esc_url( home_url( '/disclosure/' ) ) . '">Saiba mais</a>.';
	$html .= '</p></div>';

	return $html;
}

/** Card de oferta (grid). */
function trowin_oferta_card( $oferta ) {
	$desconto = trowin_desconto( $oferta->preco, $oferta->preco_antigo );

	echo '<article class="card product-card">';
	echo '<a class="product-media" href="' . esc_url( $oferta->link ) . '">';
	echo '<img src="' . esc_url( $oferta->imagem ) . '" alt="' . esc_attr( $oferta->titulo ) . '" loading="lazy" width="600" height="600">';
	if ( $desconto > 0 ) {
		echo '<span class="product-flag">-' . (int) $desconto . '%</span>';
	}
	if ( $oferta->badge ) {
		echo '<span class="product-badge">' . esc_html( $oferta->badge ) . '</span>';
	}
	echo '</a>';

	echo '<div class="product-body">';
	echo '<h3><a href="' . esc_url( $oferta->link ) . '">' . esc_html( $oferta->titulo ) . '</a></h3>';
	echo '<div>' . trowin_stars( $oferta->avaliacao ) . ' <span class="stars-note">(' . esc_html( number_format( $oferta->avaliacoes, 0, ',', '.' ) ) . ')</span></div>';
	echo '<p class="product-summary">' . esc_html( $oferta->resumo ) . '</p>';

	echo '<div class="product-price">';
	echo '<span class="price">' . esc_html( trowin_format_brl( $oferta->preco ) ) . '</span>';
	if ( $oferta->preco_antigo > $oferta->preco ) {
		echo '<span class="old">' . esc_html( trowin_format_brl( $oferta->preco_antigo ) ) . '</span>';
	}
	echo '</div>';

	echo '<div class="product-actions">';
	echo '<a class="btn-primary btn-sm" href="' . esc_url( $oferta->afiliado ) . '" target="_blank" rel="nofollow sponsored noopener">Ver Oferta &#128293;</a>';
	echo '<a class="btn-ghost btn-sm" href="' . esc_url( $oferta->link ) . '">Detalhes</a>';
	echo '</div>';

	echo '</div></article>';
}

/** Linha de tabela de ofertas. */
function trowin_oferta_linha( $oferta ) {
	$desconto = trowin_desconto( $oferta->preco, $oferta->preco_antigo );

	echo '<tr><td><div class="cell-product">';
	echo '<img class="thumb" src="' . esc_url( $oferta->imagem ) . '" alt="' . esc_attr( $oferta->titulo ) . '" loading="lazy" width="80" height="80">';
	echo '<div><a href="' . esc_url( $oferta->link ) . '">' . esc_html( $oferta->titulo ) . '</a>';
	echo '<p>' . esc_html( $oferta->resumo ) . '</p></div>';
	echo '</div></td>';

	echo '<td>' . trowin_stars( $oferta->avaliacao ) . '</td>';

	echo '<td><span class="price">' . esc_html( trowin_format_brl( $oferta->preco ) ) . '</span>';
	if ( $desconto > 0 ) {
		echo ' <span class="old">' . esc_html( trowin_format_brl( $oferta->preco_antigo ) ) . '</span>';
	}
	echo '</td>';

	echo '<td style="text-align:right"><a class="btn-primary btn-xs" href="' . esc_url( $oferta->afiliado ) . '" target="_blank" rel="nofollow sponsored noopener">Ver Oferta</a></td>';
	echo '</tr>';
}

/** Item de review (lista com prós e contras). */
function trowin_review_item( $oferta ) {
	$desconto = trowin_desconto( $oferta->preco, $oferta->preco_antigo );

	echo '<article class="card review-item">';
	echo '<div class="review-media">';
	echo '<a href="' . esc_url( $oferta->link ) . '"><img src="' . esc_url( $oferta->imagem ) . '" alt="' . esc_attr( $oferta->titulo ) . '" loading="lazy" width="400" height="400"></a>';
	echo '</div>';

	echo '<div>';
	echo '<div class="entry-meta">' . trowin_stars( $oferta->avaliacao );
	echo '<span>' . esc_html( number_format( $oferta->avaliacoes, 0, ',', '.' ) ) . ' avaliações verificadas</span>';
	echo '<span class="review-tag">Review TROWIN</span></div>';

	echo '<h3><a href="' . esc_url( $oferta->link ) . '">' . esc_html( $oferta->titulo ) . '</a></h3>';
	echo '<p>' . esc_html( $oferta->resumo ) . '</p>';

	if ( ! empty( $oferta->pros ) || ! empty( $oferta->contras ) ) {
		echo '<ul class="review-pros">';
		foreach ( $oferta->pros as $pro ) {
			echo '<li>&#10003; ' . esc_html( $pro ) . '</li>';
		}
		foreach ( $oferta->contras as $contra ) {
			echo '<li class="con">&#10005; ' . esc_html( $contra ) . '</li>';
		}
		echo '</ul>';
	}

	echo '<div class="review-cta">';
	echo '<span class="price">' . esc_html( trowin_format_brl( $oferta->preco ) ) . '</span>';
	if ( $desconto > 0 ) {
		echo '<span class="off-badge">' . (int) $desconto . '% OFF</span>';
	}
	echo '<span class="spacer"></span>';
	echo '<a class="btn-primary btn-sm" href="' . esc_url( $oferta->afiliado ) . '" target="_blank" rel="nofollow sponsored noopener">Ver Oferta</a>';
	echo '<a class="btn-ghost btn-sm" href="' . esc_url( $oferta->link ) . '">Leia Mais</a>';
	echo '</div>';

	echo '</div></article>';
}

/** Card de categoria (taxonomia categoria_oferta). */
function trowin_categoria_card( $term ) {
	$icone   = get_term_meta( $term->term_id, '_trowin_icone', true );
	$icone   = $icone ? $icone : '&#10024;';
	$tagline = get_term_meta( $term->term_id, '_trowin_tagline', true );

	echo '<a class="card category-card" href="' . esc_url( home_url( '/ofertas/?categoria=' . $term->slug ) ) . '">';
	echo '<span class="category-icon">' . esc_html( $icone ) . '</span>';
	echo '<span><h3>' . esc_html( $term->name ) . '</h3>';
	echo '<span class="tagline">' . esc_html( $tagline ) . '</span></span>';
	echo '</a>';
}

/** Abre uma seção com título padrão. */
function trowin_section_head( $eyebrow, $titulo, $subtitulo = '', $acao = array() ) {
	echo '<div class="section-head"><div>';
	if ( $eyebrow ) {
		echo '<p class="eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h2>' . esc_html( $titulo ) . '</h2>';
	if ( $subtitulo ) {
		echo '<p class="sub">' . esc_html( $subtitulo ) . '</p>';
	}
	echo '</div>';

	if ( ! empty( $acao ) ) {
		echo '<a class="btn-ghost btn-sm" href="' . esc_url( $acao['href'] ) . '">' . esc_html( $acao['label'] ) . ' &rarr;</a>';
	}

	echo '</div>';
}
