
<template lang="jade">
	vue-modal-card(ref="modal", btn-title="Сохранить", :title="modalTitle", @ok="save")
		div(:class="form.style")
			div.field
				label.label Имя пользователя
				p.control
					input.input(type="text", v-model="form.name.value", :class="form.name.style")

			div.field
				label.label Логин
				p.control
					input.input(type="text", v-model="form.username.value", :class="form.username.style")

			hr

			div.field
				label.label Пароль
				p.control
					input.input(type="text", v-model="form.password.value", :class="form.password.style")

</template>

<script>
import VueModalCard from './../components/ModalCard'
import Form from './../utils/Form.js'

export default {

	components: {
		VueModalCard
	},

	data() {
		return {
			form: new Form({
				id: null,
				name: {
					required: true
				},
				username: {
					required: true
				},
				password: null,
				create_at: null,
				login_at: null,
				status: 8
			}),
			modalTitle: ''
		}
	},

	methods: {
		open(id) {
			this.form.reset();

			if (id) {
				this.modalTitle = 'Изменить данные пользователя';
				this.form.load('/user/get', id, () => {
					this.form.set('password', null);
				});
			} else {
				this.modalTitle = 'Добавить пользователя';
			}

			this.$refs.modal.open();
		},
		save() {
			this.form.submit('/user/save', () => {
				this.$emit('save');
			});
		}
	}
}
</script>