<?php
/**
 * Footer do tema TROWIN STORE.
 *
 * @package TROWIN_STORE
 */

?>
</main><!-- #conteudo -->

<footer class="site-footer" role="contentinfo">
	<div class="container footer-top">
		<div>
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="brand-mark">T</span>
				<span>TROWIN <span class="gradient-text">STORE</span></span>
			</a>
			<p>
				Curadoria diária das melhores ofertas em tecnologia da Amazon Brasil.
				Comparamos preços, testamos produtos e mostramos apenas o que realmente vale o seu dinheiro.
			</p>
			<div class="social-links">
				<a href="https://instagram.com/trowinstore" target="_blank" rel="noopener noreferrer">In</a>
				<a href="https://tiktok.com/@trowinstore" target="_blank" rel="noopener noreferrer">Tk</a>
				<a href="https://youtube.com/@trowinstore" target="_blank" rel="noopener noreferrer">Yt</a>
			</div>
		</div>

		<div>
			<h3>Navegue</h3>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
				<li><a href="<?php echo esc_url( home_url( '/ofertas/' ) ); ?>">Ofertas</a></li>
				<li><a href="<?php echo esc_url( home_url( '/#reviews' ) ); ?>">Reviews</a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contato/' ) ); ?>">Contato</a></li>
			</ul>
		</div>

		<div>
			<h3>Institucional</h3>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/sobre/' ) ); ?>">Sobre Nós</a></li>
				<li><a href="<?php echo esc_url( home_url( '/politica/' ) ); ?>">Política de Privacidade</a></li>
				<li><a href="<?php echo esc_url( home_url( '/termos/' ) ); ?>">Termos de Uso</a></li>
				<li><a href="<?php echo esc_url( home_url( '/disclosure/' ) ); ?>">Disclosure</a></li>
			</ul>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> TROWIN STORE. Todos os direitos reservados.</p>
			<p>Participante do Programa de Associados da Amazon &mdash; como associado, recebemos por compras qualificadas.</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
