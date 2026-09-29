<?php
/**
 * Importador de conteúdo de exemplo do tema TROWIN STORE.
 *
 * Cria, com um clique em Ofertas > Conteúdo de exemplo:
 * 6 categorias, 12 ofertas (com todos os metadados), 4 posts e 5 páginas.
 * Itens já existentes (mesmo slug) não são duplicados.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Adiciona a página de importação no admin. */
function trowin_conteudo_exemplo_menu() {
	add_submenu_page(
		'edit.php?post_type=oferta',
		'Conteúdo de exemplo',
		'Conteúdo de exemplo',
		'manage_options',
		'trowin-conteudo-exemplo',
		'trowin_conteudo_exemplo_pagina'
	);
}
add_action( 'admin_menu', 'trowin_conteudo_exemplo_menu' );

/** Carrega os dados de exemplo (compartilhados com o modo demonstração PHP). */
function trowin_conteudo_exemplo_dados() {
	$arquivo = dirname( TROWIN_DIR ) . '/demo/data.php';

	if ( ! file_exists( $arquivo ) ) {
		return array( 'categorias' => array(), 'ofertas' => array(), 'posts' => array(), 'paginas' => array() );
	}

	return (array) require $arquivo;
}

/** Converte o texto simples de exemplo em HTML. */
function trowin_texto_para_html( $texto, $tipo = 'post' ) {
	if ( 'page' === $tipo ) {
		return $texto;
	}

	$html     = '';
	$em_lista = false;

	foreach ( preg_split( '/\n/', $texto ) as $linha ) {
		$linha = trim( $linha );

		if ( '' === $linha ) {
			if ( $em_lista ) {
				$html    .= '</ul>';
				$em_lista = false;
			}
			continue;
		}

		if ( 0 === strpos( $linha, '## ' ) ) {
			if ( $em_lista ) {
				$html    .= '</ul>';
				$em_lista = false;
			}
			$html .= '<h2>' . esc_html( substr( $linha, 3 ) ) . '</h2>';
			continue;
		}

		if ( 0 === strpos( $linha, '- ' ) ) {
			if ( ! $em_lista ) {
				$html    .= '<ul>';
				$em_lista = true;
			}
			$html .= '<li>' . esc_html( substr( $linha, 2 ) ) . '</li>';
			continue;
		}

		if ( $em_lista ) {
			$html    .= '</ul>';
			$em_lista = false;
		}

		$html .= '<p>' . esc_html( $linha ) . '</p>';
	}

	if ( $em_lista ) {
		$html .= '</ul>';
	}

	return $html;
}

/** Verifica se já existe um post com aquele slug. */
function trowin_post_existe( $slug, $tipo ) {
	$existente = get_page_by_path( $slug, OBJECT, $tipo );

	return (bool) $existente;
}

/** Executa a importação e devolve um resumo. */
function trowin_conteudo_exemplo_importar() {
	$dados    = trowin_conteudo_exemplo_dados();
	$resumo   = array( 'categorias' => 0, 'ofertas' => 0, 'posts' => 0, 'paginas' => 0 );
	$modelos  = array( 'ofertas' => 'page-ofertas.php', 'contato' => 'page-contato.php' );

	foreach ( $dados['categorias'] as $categoria ) {
		$termo = get_term_by( 'slug', $categoria['slug'], 'categoria_oferta' );

		if ( ! $termo ) {
			$novo = wp_insert_term( $categoria['name'], 'categoria_oferta', array( 'slug' => $categoria['slug'] ) );
			if ( is_wp_error( $novo ) ) {
				continue;
			}
			$termo = get_term( $novo['term_id'], 'categoria_oferta' );
		}

		if ( $termo && ! is_wp_error( $termo ) ) {
			update_term_meta( $termo->term_id, '_trowin_icone', $categoria['icone'] );
			update_term_meta( $termo->term_id, '_trowin_tagline', $categoria['tagline'] );
			$resumo['categorias']++;
		}
	}

	foreach ( $dados['ofertas'] as $linha ) {
		if ( trowin_post_existe( $linha['slug'], 'oferta' ) ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'oferta',
				'post_status'  => 'publish',
				'post_title'   => $linha['titulo'],
				'post_name'    => $linha['slug'],
				'post_excerpt' => $linha['resumo'],
				'post_content' => wp_kses_post( trowin_texto_para_html( $linha['descricao'], 'oferta' ) ),
				'meta_input'   => array(
					'_trowin_asin'         => $linha['asin'],
					'_trowin_preco'        => (string) $linha['preco'],
					'_trowin_preco_antigo' => (string) $linha['preco_antigo'],
					'_trowin_avaliacao'    => (string) $linha['avaliacao'],
					'_trowin_avaliacoes'   => (string) $linha['avaliacoes'],
					'_trowin_badge'        => isset( $linha['badge'] ) ? $linha['badge'] : '',
					'_trowin_cupom'        => isset( $linha['cupom'] ) ? $linha['cupom'] : '',
					'_trowin_resumo'       => $linha['resumo'],
					'_trowin_pros'         => $linha['pros'],
					'_trowin_contras'      => $linha['contras'],
					'_trowin_destaque'     => empty( $linha['destaque'] ) ? '' : '1',
					'_trowin_review'       => empty( $linha['review'] ) ? '' : '1',
					'_trowin_imagem'       => $linha['imagem'],
				),
			)
		);

		if ( $id && ! is_wp_error( $id ) ) {
			wp_set_object_terms( $id, array( $linha['categoria'] ), 'categoria_oferta' );
			$resumo['ofertas']++;
		}
	}

	foreach ( $dados['posts'] as $linha ) {
		if ( trowin_post_existe( $linha['slug'], 'post' ) ) {
			continue;
		}

		$categoria_id = 0;
		$categoria    = get_term_by( 'name', $linha['categoria'], 'category' );
		if ( ! $categoria ) {
			$nova = wp_insert_term( $linha['categoria'], 'category' );
			if ( ! is_wp_error( $nova ) ) {
				$categoria_id = (int) $nova['term_id'];
			}
		} else {
			$categoria_id = (int) $categoria->term_id;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $linha['titulo'],
				'post_name'    => $linha['slug'],
				'post_excerpt' => $linha['resumo'],
				'post_content' => wp_kses_post( trowin_texto_para_html( $linha['conteudo'], 'post' ) ),
				'post_date'    => $linha['data'] . ' 10:00:00',
				'post_category' => array( $categoria_id ),
				'meta_input'   => array( '_trowin_imagem' => $linha['imagem'] ),
			)
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$resumo['posts']++;
		}
	}

	foreach ( $dados['paginas'] as $linha ) {
		if ( trowin_post_existe( $linha['slug'], 'page' ) ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $linha['titulo'],
				'post_name'    => $linha['slug'],
				'post_content' => wp_kses_post( $linha['conteudo'] ),
				'meta_input'   => array(
					'_wp_page_template' => isset( $modelos[ $linha['slug'] ] ) ? $modelos[ $linha['slug'] ] : 'default',
				),
			)
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$resumo['paginas']++;
		}
	}

	return $resumo;
}

/** Página de administração com o botão de importação. */
function trowin_conteudo_exemplo_pagina() {
	$resumo = null;

	if ( isset( $_POST['trowin_importar_conteudo'] ) && check_admin_referer( 'trowin_importar_conteudo', 'trowin_importar_nonce' ) ) {
		$resumo = trowin_conteudo_exemplo_importar();
	}
	?>
	<div class="wrap">
		<h1>TROWIN STORE &mdash; Conteúdo de exemplo</h1>
		<p>Importa as 6 categorias, 12 ofertas (com preços, ASIN, avaliações, prós e contras), 4 posts de blog e as 5 páginas institucionais exibidas no site original.</p>

		<?php if ( is_array( $resumo ) ) : ?>
			<div class="notice notice-success"><p>
				Importação concluída:
				<?php echo (int) $resumo['categorias']; ?> categorias,
				<?php echo (int) $resumo['ofertas']; ?> ofertas,
				<?php echo (int) $resumo['posts']; ?> posts e
				<?php echo (int) $resumo['paginas']; ?> páginas processadas.
			</p></div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'trowin_importar_conteudo', 'trowin_importar_nonce' ); ?>
			<p><button type="submit" class="button button-primary" name="trowin_importar_conteudo" value="1">Importar conteúdo de exemplo</button></p>
		</form>

		<h2>Depois de importar</h2>
		<ol>
			<li>Vá em <strong>Configurações &rsaquo; Leitura</strong> e defina a página inicial como uma página estática (use &ldquo;Ofertas&rdquo; ou crie uma página com o template &ldquo;Página de Ofertas&rdquo;).</li>
			<li>Defina a página de posts como <strong>Blog</strong> (ou uma página com o template de blog).</li>
			<li>Em <strong>Aparência &rsaquo; Menus</strong>, crie o menu principal (Home, Ofertas, Reviews, Blog, Contato) e atribua à posição &ldquo;Menu Principal&rdquo;.</li>
			<li>Em <strong>Aparência &rsaquo; Personalizar &rsaquo; TROWIN STORE: Anúncios</strong>, informe seu Client ID e os slots do AdSense.</li>
		</ol>
	</div>
	<?php
}
