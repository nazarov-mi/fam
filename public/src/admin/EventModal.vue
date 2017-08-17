
<template lang="jade">
	vue-modal-card(ref="modal", btn-title="Сохранить", :title="modalTitle", @ok="save")
		div(:class="form.style")
			div.field
				label.label Название мероприятия
				p.control
					input.input(type="text", v-model="form.name.value", :class="form.name.style")

			div.field
				label.label Тип мероприятия
				p.control
					vue-select(src="/search/types", v-model="form.type_id.value", :class="form.type_id.style")

			div.field
				label.label Аудитория
				p.control
					vue-select(src="/search/audience", v-model="form.audience_id.value", :class="form.audience_id.style")

			div.field
				label.label Место проведения
				p.control
					vue-select(src="/search/locations", v-model="form.location_id.value", :class="form.location_id.style")

			div.field
				label.label Ожидаемое колличество поситителей
				p.control
					input.input(type="number", min="0", pattern="[0-9]*", inputmode="numeric", v-model="form.peoples.value", :class="form.peoples.style")

			div.field
				label.label Сроки проведения
				div.field.is-grouped
					p.control.is-expanded
						vue-date-picker(placeholder="Начало активности", v-model="form.active_date.value", :classes="form.active_date.style")
					p.control.is-expanded
						vue-date-picker(placeholder="Окончание активности", v-model="form.end_date.value", :classes="form.end_date.style")

			div.field
				label.label Описание
				p.control
					textarea.textarea(placeholder="Пару слов о предстоящем мероприятии", v-model="form.desc.value")

			hr
			p.title.is-5 Календарь мероприятий
			vue-calendar(ref="calendar", @change="open")

</template>

<script>
import VueModalCard from './../components/ModalCard'
import VueCalendar from './../components/Calendar'
import VueSelect from './../components/Select'
import VueDatePicker from './../components/DatePicker'
import Form from './../utils/Form.js'

export default {

	components: {
		VueModalCard,
		VueCalendar,
		VueSelect,
		VueDatePicker
	},

	data() {
		return {
			form: new Form({
				id: null,
				name: {
					required: true
				},
				type_id: {
					required: true
				},
				audience_id: {
					required: true
				},
				location_id: {
					required: true
				},
				active_date: {
					required: true
				},
				end_date: {
					required: true
				},
				peoples: {
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
				this.modalTitle = "Изменить данные события";
				this.form.load('/event/get', id);
				this.$refs.calendar.show(id);
			} else {
				this.modalTitle = "Добавить событие";
				this.$refs.calendar.now();
			}

			this.$refs.modal.open();
		},
		save() {
			this.form.submit('/event/save', () => {
				this.$refs.calendar.load();
				this.$emit('save');
			});
		}
	}
}
</script>