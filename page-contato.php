<?php
/**
 * Template Name: Página de Contato
 * Template Post Type: page
 *
 * @package TROWIN_STORE
 */

get_header();

$canais = array(
	array(
		'icone'       => '&#9993;',
		'titulo'      => 'E-mail',
		'valor'       => 'suporte@trowinstore.com.br',
		'href'        => 'mailto:suporte@trowinstore.com.br',
		'descricao'   => 'Respondemos em até 2 dias úteis.',
	),
	array(
		'icone'       => '&#129309;',
		'titulo'      => 'Imprensa e parcerias',
		'valor'       => 'parcerias@trowinstore.com.br',
		'href'        => 'mailto:parcerias@trowinstore.com.br',
		'descricao'   => 'Propostas comerciais e publieditoriais sinalizados.',
	),
	array(
		'icone'       => '&#128293;',
		'titulo'      => 'Ofertas',
		'valor'       => '/ofertas/',
		'href'        => home_url( '/ofertas/' ),
		'descricao'   => 'Antes de perguntar, confira se o produto já está na lista.',
	),
);

while ( have_posts() ) :
	the_post();
	?>
	<div class="container" style="padding:40px 20px">
		<?php echo trowin_breadcrumbs( array( array( 'label' => 'Contato' ) ) ); ?>

		<div class="contact-grid" style="margin-top:24px">
			<section class="contact-card">
				<p class="eyebrow" style="color:#00a6cc;font-weight:700;letter-spacing:.24em;text-transform:uppercase;font-size:12px;margin:0">Fale com a gente</p>
				<h1 style="margin-top:12px"><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<?php trowin_contato_form(); ?>
			</section>

			<aside class="sidebar">
				<?php foreach ( $canais as $canal ) : ?>
					<div class="widget">
						<span class="category-icon"><?php echo esc_html( $canal['icone'] ); ?></span>
						<h2 style="margin-top:16px"><?php echo esc_html( $canal['titulo'] ); ?></h2>
						<a href="<?php echo esc_url( $canal['href'] ); ?>"><?php echo esc_html( $canal['valor'] ); ?></a>
						<p><?php echo esc_html( $canal['descricao'] ); ?></p>
					</div>
				<?php endforeach; ?>

				<div class="widget widget-cta">
					<h2>Horário de atendimento</h2>
					<p>Segunda a sexta, das 9h às 18h (horário de Brasília). Mensagens fora do expediente são respondidas no próximo dia útil.</p>
				</div>
			</aside>
		</div>
	</div>
	<?php
endwhile;

get_footer();
