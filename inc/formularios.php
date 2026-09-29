<?php
/**
 * Formulários (newsletter e contato) e seus processadores.
 *
 * @package TROWIN_STORE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Guarda/recupera mensagens de retorno dos formulários. */
function trowin_flash( $key, $value = null ) {
	if ( ! isset( $GLOBALS['trowin_flash'] ) || ! is_array( $GLOBALS['trowin_flash'] ) ) {
		$GLOBALS['trowin_flash'] = array();
	}

	if ( null !== $value ) {
		$GLOBALS['trowin_flash'][ $key ] = $value;
		return $value;
	}

	return isset( $GLOBALS['trowin_flash'][ $key ] ) ? $GLOBALS['trowin_flash'][ $key ] : '';
}

/** Seção de newsletter (fundo gradiente escuro). */
function trowin_newsletter_section() {
	$msg_ok  = trowin_flash( 'newsletter_ok' );
	$msg_err = trowin_flash( 'newsletter_erro' );

	echo '<section class="newsletter" id="newsletter"><div class="container">';
	echo '<div>';
	echo '<p class="eyebrow">Newsletter TROWIN</p>';
	echo '<h2>Receba as melhores ofertas antes de todo mundo</h2>';
	echo '<p>Um e-mail por semana com os maiores descontos da Amazon Brasil, cupons exclusivos e reviews honestos. Sem spam, cancele quando quiser.</p>';
	echo '</div>';

	echo '<form method="post" action="' . esc_url( home_url( '/#newsletter' ) ) . '">';
	if ( function_exists( 'wp_nonce_field' ) ) {
		wp_nonce_field( 'trowin_newsletter', 'trowin_newsletter_nonce' );
	}
	echo '<input type="email" name="trowin_email" required placeholder="seu@email.com.br" aria-label="Seu e-mail" value="">';
	echo '<button type="submit" class="btn-primary" name="trowin_newsletter_submit" value="1">Assinar</button>';

	if ( $msg_ok ) {
		echo '<p class="msg ok">' . esc_html( $msg_ok ) . '</p>';
	} elseif ( $msg_err ) {
		echo '<p class="msg err">' . esc_html( $msg_err ) . '</p>';
	} else {
		echo '<p class="note">Ao se inscrever você concorda com nossa <a href="' . esc_url( home_url( '/politica/' ) ) . '">Política de Privacidade</a>.</p>';
	}

	echo '</form></div></section>';
}

/** Formulário de contato. */
function trowin_contato_form() {
	$assuntos = array( 'Dúvida sobre um produto', 'Parceria', 'Sugestão de review', 'Problema no site', 'Outro' );
	$nome     = isset( $_POST['trowin_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['trowin_nome'] ) ) : '';
	$email    = isset( $_POST['trowin_email_contato'] ) ? sanitize_email( wp_unslash( $_POST['trowin_email_contato'] ) ) : '';
	$assunto  = isset( $_POST['trowin_assunto'] ) ? sanitize_text_field( wp_unslash( $_POST['trowin_assunto'] ) ) : $assuntos[0];
	$mensagem = isset( $_POST['trowin_mensagem'] ) ? sanitize_textarea_field( wp_unslash( $_POST['trowin_mensagem'] ) ) : '';

	$msg_ok  = trowin_flash( 'contato_ok' );
	$msg_err = trowin_flash( 'contato_erro' );

	echo '<form method="post" class="contact-form">';
	if ( function_exists( 'wp_nonce_field' ) ) {
		wp_nonce_field( 'trowin_contato', 'trowin_contato_nonce' );
	}

	echo '<div class="form-row">';
	echo '<div class="form-group"><label for="trowin_nome">Nome</label>';
	echo '<input class="field" type="text" id="trowin_nome" name="trowin_nome" required value="' . esc_attr( $nome ) . '" placeholder="Seu nome completo"></div>';
	echo '<div class="form-group"><label for="trowin_email_contato">E-mail</label>';
	echo '<input class="field" type="email" id="trowin_email_contato" name="trowin_email_contato" required value="' . esc_attr( $email ) . '" placeholder="seu@email.com.br"></div>';
	echo '</div>';

	echo '<div class="form-group"><label for="trowin_assunto">Assunto</label>';
	echo '<select class="field" id="trowin_assunto" name="trowin_assunto">';
	foreach ( $assuntos as $opcao ) {
		echo '<option value="' . esc_attr( $opcao ) . '"' . selected( $assunto, $opcao, false ) . '>' . esc_html( $opcao ) . '</option>';
	}
	echo '</select></div>';

	echo '<div class="form-group"><label for="trowin_mensagem">Mensagem</label>';
	echo '<textarea class="field" id="trowin_mensagem" name="trowin_mensagem" rows="6" required placeholder="Escreva sua mensagem...">' . esc_textarea( $mensagem ) . '</textarea></div>';

	echo '<button type="submit" class="btn-primary btn-block" name="trowin_contato_submit" value="1">Enviar mensagem</button>';

	if ( $msg_ok ) {
		echo '<p class="msg ok">' . esc_html( $msg_ok ) . '</p>';
	} elseif ( $msg_err ) {
		echo '<p class="msg err">' . esc_html( $msg_err ) . '</p>';
	}

	echo '</form>';
}

/** Processa os envios (newsletter + contato). Executado no hook init pelo WordPress. */
function trowin_processar_formularios() {
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
		return;
	}

	// Newsletter.
	if ( isset( $_POST['trowin_newsletter_submit'] ) ) {
		$nonce_ok = ! function_exists( 'wp_verify_nonce' )
			|| wp_verify_nonce( isset( $_POST['trowin_newsletter_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['trowin_newsletter_nonce'] ) ) : '', 'trowin_newsletter' );

		$email = isset( $_POST['trowin_email'] ) ? sanitize_email( wp_unslash( $_POST['trowin_email'] ) ) : '';

		if ( ! $nonce_ok ) {
			trowin_flash( 'newsletter_erro', 'Sessão expirada. Recarregue a página e tente novamente.' );
		} elseif ( ! is_email( $email ) ) {
			trowin_flash( 'newsletter_erro', 'Informe um e-mail válido.' );
		} else {
			trowin_salvar_inscrito( $email );
			trowin_flash( 'newsletter_ok', 'Inscrição confirmada! As melhores ofertas chegam no seu e-mail.' );
		}
	}

	// Contato.
	if ( isset( $_POST['trowin_contato_submit'] ) ) {
		$nonce_ok = ! function_exists( 'wp_verify_nonce' )
			|| wp_verify_nonce( isset( $_POST['trowin_contato_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['trowin_contato_nonce'] ) ) : '', 'trowin_contato' );

		$nome     = isset( $_POST['trowin_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['trowin_nome'] ) ) : '';
		$email    = isset( $_POST['trowin_email_contato'] ) ? sanitize_email( wp_unslash( $_POST['trowin_email_contato'] ) ) : '';
		$assunto  = isset( $_POST['trowin_assunto'] ) ? sanitize_text_field( wp_unslash( $_POST['trowin_assunto'] ) ) : 'Contato';
		$mensagem = isset( $_POST['trowin_mensagem'] ) ? sanitize_textarea_field( wp_unslash( $_POST['trowin_mensagem'] ) ) : '';

		if ( ! $nonce_ok ) {
			trowin_flash( 'contato_erro', 'Sessão expirada. Recarregue a página e tente novamente.' );
		} elseif ( strlen( $nome ) < 2 || ! is_email( $email ) || strlen( $mensagem ) < 10 ) {
			trowin_flash( 'contato_erro', 'Preencha nome, e-mail válido e uma mensagem com pelo menos 10 caracteres.' );
		} else {
			trowin_salvar_mensagem( $nome, $email, $assunto, $mensagem );
			trowin_flash( 'contato_ok', 'Mensagem enviada com sucesso! Respondemos em até 2 dias úteis.' );
		}
	}
}

/** Salva o inscrito (CPT privado "inscrito" + e-mail ao admin). */
function trowin_salvar_inscrito( $email ) {
	$existe = get_page_by_title( $email, OBJECT, 'inscrito' );

	if ( ! $existe && function_exists( 'wp_insert_post' ) ) {
		wp_insert_post(
			array(
				'post_type'    => 'inscrito',
				'post_status'  => 'publish',
				'post_title'   => $email,
				'post_content' => '',
			)
		);
	}

	if ( function_exists( 'wp_mail' ) ) {
		wp_mail( get_option( 'admin_email' ), 'Novo inscrito TROWIN STORE', 'E-mail: ' . $email );
	}
}

/** Salva a mensagem de contato (CPT privado "mensagem"). */
function trowin_salvar_mensagem( $nome, $email, $assunto, $mensagem ) {
	if ( function_exists( 'wp_insert_post' ) ) {
		wp_insert_post(
			array(
				'post_type'    => 'mensagem',
				'post_status'  => 'publish',
				'post_title'   => $assunto . ' - ' . $nome,
				'post_content' => $mensagem,
				'meta_input'   => array(
					'_trowin_nome'  => $nome,
					'_trowin_email' => $email,
				),
			)
		);
	}

	if ( function_exists( 'wp_mail' ) ) {
		$corpo  = "Nome: {$nome}\nE-mail: {$email}\nAssunto: {$assunto}\n\n{$mensagem}";
		wp_mail( 'suporte@trowinstore.com.br', 'Contato TROWIN STORE: ' . $assunto, $corpo );
	}
}

/** Shortcode [trowin_newsletter] e [trowin_contato]. */
function trowin_newsletter_shortcode() {
	ob_start();
	trowin_newsletter_section();

	return ob_get_clean();
}
add_shortcode( 'trowin_newsletter', 'trowin_newsletter_shortcode' );

function trowin_contato_shortcode() {
	ob_start();
	trowin_contato_form();

	return ob_get_clean();
}
add_shortcode( 'trowin_contato', 'trowin_contato_shortcode' );
