
<style lang="sass">
	@import '../../fonts/HelveticaNeue/HelveticaNeue.css';
	@import '../../fonts/NotoSerif/NotoSerif.css';

	$primary: #12B886;
	@import "~bulma";
	
	$fa-font-path: "~font-awesome/fonts/";
	@import "~font-awesome/scss/font-awesome";
	@import '../../css/app.css';
</style>

<template lang="jade">
	div
		div.header-img
			h3.title.is-3 Система для составления плана мероприятий ГАУ АО МСЦ "Семья"

		nav.nav.has-shadow.menu
			div.container

				span.nav-toggle(@click="toggleMenu")
					span
					span
					span

				div.nav-right.nav-menu(:class="{ 'is-active': menuIsShown }")
					router-link.nav-item.is-tab(to="/", exact) Руководство
					router-link.nav-item.is-tab(to="/calendar") Календарь
					router-link.nav-item.is-tab(to="/events") События
					router-link.nav-item.is-tab(to="/types") Типы
					router-link.nav-item.is-tab(to="/audience") Аудитория
					router-link.nav-item.is-tab(to="/locations") Места
					router-link.nav-item.is-tab(to="/reports") Отчёты
					router-link.nav-item.is-tab(to="/users") Пользователи
					a.nav-item.is-tab(href="/auth/logout") Выход

		section.section
			div.container
				router-view

</template>

<script>
export default {
	data() {
		return {
			menuIsShown: false
		}
	},

	watch: {
		'$route' () {
			this.menuIsShown = false;
		}
	},

	methods: {
		toggleMenu() {
			this.menuIsShown = !this.menuIsShown
		}
	}
}
</script>