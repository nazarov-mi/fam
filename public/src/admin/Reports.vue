
<template lang="jade">
	div
		h3.title Составление отчёта
		h3.subtitle Выберите необходимые параметры

		div.field
			label.label Поля отчёта
			p.control
				vue-select(placeholder="Выберите поля для таблицы", :multiple="true", :options="options", v-model="form.fields.value", :class="form.fields.style")

		div.field
			label.label Тип мероприятия
			p.control
				vue-select(placeholder="Выберите типы мероприятия", src="/search/types", :multiple="true", v-model="form.types.value")

		div.field
			label.label Аудитория
			p.control
				vue-select(placeholder="Выберите аудитории", src="/search/audience", :multiple="true", v-model="form.audience.value")

		div.field
			label.label Место проведения
			p.control
				vue-select(placeholder="Выберите места проведения", src="/search/locations", :multiple="true", v-model="form.locations.value")

		div.field
			label.label Поиск по дате
			div.field.is-grouped
				p.control.is-expanded
					vue-date-picker(placeholder="Начало", v-model="form.active_date.value")

				p.control.is-expanded
					vue-date-picker(placeholder="Окончание", v-model="form.end_date.value")

		div.field
			label.label Количество людей
			div.field.is-grouped
				p.control.is-expanded
					input.input(type="number", min="0", pattern="[0-9]*", inputmode="numeric", placeholder="От", v-model="form.peoples_min.value")

				p.control.is-expanded
					input.input(type="number", min="0", pattern="[0-9]*", inputmode="numeric", placeholder="До", v-model="form.peoples_max.value")

		div.field
			label.label Выводить конфликты мероприятий?
			div.field
				p.control
					label.radio
						input.radio(type="radio", name="conflicts", value="0", v-model="form.conflicts.value")
						| Выводить
			div.field
				p.control
					label.radio
						input.radio(type="radio", name="conflicts", value="1", v-model="form.conflicts.value")
						| Не выводить
			div.field
				p.control
					label.radio
						input.radio(type="radio", name="conflicts", value="2", v-model="form.conflicts.value")
						| Вывети только конфликты мероприятий

			span.help(:class="form.conflicts.style") Обязательно к заполнению

		div.field.is-grouped
			p.control
				a.button.is-primary(@click="create") Составить отчёт

			p.control
				a.button(@click="reset") Очистить

</template>

<script>
import VueSelect from './../components/Select'
import VueDatePicker from './../components/DatePicker'
import Form from './../utils/Form.js'

export default {

	components: {
		VueSelect,
		VueDatePicker
	},

	data() {
		return {
			options: {
				id: 'ID',
				name: 'Название',
				type_id: 'Тип',
				audience_id: 'Аудитория',
				location_id: 'Место проведения',
				peoples: 'Колличство человек',
				active_date: 'Начало',
				end_date: 'Окончание'
			},
			form: new Form({
				fields: {
					default: ['name','location_id','active_date','end_date'],
					required: true
				},
				types: null,
				audience: null,
				locations: null,
				active_date: null,
				end_date: null,
				peoples_min: null,
				peoples_max: null,
				conflicts: {
					required: true,
					default: 0
				}
			})
		}
	},

	methods: {
		create: function () {

			if (!this.form.check()) return;

			var src = this.form.getQueryString('/main/report');
			window.open(src);
		},
		reset: function () {
			this.form.reset();
		}
	}
}
</script> 