
<template lang="jade">
	vue-modal-card(ref="modal", btn-title="Сохранить", :title="modalTitle", @ok="save")
		div(:class="form.style")
			div.field
				label.label Название
				p.control
					input.input(type="text", v-model="form.name.value", :class="form.name.style")

			div.field
				label.label Описание
				p.control
					textarea.textarea(v-model="form.desc.value")

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
				desc: null
			}),
			modalTitle: ''
		}
	},

	methods: {
		open(id) {
			this.form.reset();

			if (id) {
				this.modalTitle = 'Изменить данные типа события';
				this.form.load('/type/get', id);
			} else {
				this.modalTitle = 'Добавить тип события';
			}

			this.$refs.modal.open();
		},
		save() {
			this.form.submit('/type/save', () => {
				this.$emit('save');
			});
		}
	}
}
</script>