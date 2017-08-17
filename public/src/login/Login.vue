
<style lang="sass">
	@import '../../fonts/HelveticaNeue/HelveticaNeue.css';
	@import '../../fonts/NotoSerif/NotoSerif.css';

	$primary: #12B886;
	@import "~bulma";

	@import '../../css/app.css';
</style>

<template lang="jade">
	section.section
		form.login-form(method="post", :class="form.style", @submit.prevent="submit")
			h1.title.has-text-centered Вход
			div.field
				p.control
					input.input(type="text", placeholder="Логин", v-model="form.username.value", :class="form.username.style")
			div.field
				p.control
					input.input(type="password", placeholder="Пароль", v-model="form.password.value", :class="form.password.style")
			div.field
				p.control
					button.button.is-primary.is-fullwidth Войти

</template>

<script>
import Form from './../utils/Form.js'

export default {

	data() {
		return {
			form: new Form({
				username: {
					require: true
				},
				password: {
					require: true
				}
			})
		}
	},

	methods: {
		submit() {
			this.form.submit('/auth/login', () => {
				window.location.reload();
			});
		}
	}
}
</script>
