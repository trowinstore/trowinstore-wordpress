<?php
/**
 * Página 404 customizada.
 *
 * @package TROWIN_STORE
 */

get_header();
?>

<div class="error-404">
	<p class="code gradient-text">404</p>
	<h1>Ops, essa oferta saiu do ar!</h1>
	<p>A página que você procura pode ter mudado de endereço ou o produto saiu de catálogo. Que tal voltar para as ofertas de hoje?</p>

	<div class="actions">
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Voltar para o início</a>
		<a class="btn-ghost" href="<?php echo esc_url( home_url( '/ofertas/' ) ); ?>">Ver todas as ofertas &#128293;</a>
	</div>

	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="filter-bar" style="justify-content:center;margin-top:32px">
		<input type="search" name="s" class="field" style="max-width:320px" placeholder="Buscar ofertas..." aria-label="Buscar ofertas">
		<button type="submit" class="btn-primary btn-sm">Buscar</button>
	</form>
</div>

<?php
get_footer();
