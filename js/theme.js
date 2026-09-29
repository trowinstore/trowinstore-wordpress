/**
 * TROWIN STORE — interações do tema.
 */
(function () {
	"use strict";

	document.addEventListener("DOMContentLoaded", function () {
		var toggle = document.querySelector(".menu-toggle");
		var menu = document.getElementById("menu-mobile");

		if (toggle && menu) {
			toggle.addEventListener("click", function () {
				var aberto = menu.classList.toggle("is-open");
				toggle.setAttribute("aria-expanded", aberto ? "true" : "false");
			});
		}

		// Marca o item de menu ativo no fallback de menu.
		var atual = window.location.pathname;
		var links = document.querySelectorAll(".main-nav a");
		for (var i = 0; i < links.length; i++) {
			var href = links[i].getAttribute("href") || "";
			if (href !== "/" && atual.indexOf(href.replace(/\/$/, "")) === 0) {
				links[i].className += " is-active";
			}
		}

		// Lazy loading nativo adicional para imagens sem atributo loading.
		if ("loading" in HTMLImageElement.prototype === false) {
			var imagens = document.querySelectorAll("img:not([loading])");
			for (var j = 0; j < imagens.length; j++) {
				imagens[j].setAttribute("loading", "lazy");
			}
		}
	});
})();
