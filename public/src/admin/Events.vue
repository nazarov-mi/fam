
<template lang="jade">
	div
		div.field
			p.control
				a.button.is-primary.is-outlined.add-button(@click="add")
					span.icon.is-small
						i.fa.fa-plus
					span Добавить
		vue-table(ref="table", get-api="/event/all", delete-api="/event/delete", :head="head", @change="open")
			template(scope="props")
				a.tag(v-show="(props.row.warn + props.row.error) > 0", :class="{'is-warning': props.row.warn == 1, 'is-danger': props.row.error == 1}")
					i.fa.fa-exclamation-triangle.fa-lg(aria-hidden="true")

		vue-event-modal(ref="modal", @save="onSave")

</template>

<script>
import VueTable from './../components/Table'
import VueEventModal from './../admin/EventModal'

export default {

	components: {
		VueTable,
		VueEventModal
	},

	data() {
		return {
			head: {
				name: 'Название',
				location: 'Место',
				active_date: 'Начало',
				end_date: 'Окончание'
			}
		}
	},

	methods: {
		add() {
			this.$refs.modal.open();
		},
		open(id) {
			this.$refs.modal.open(id);
		},
		onSave() {
			this.$refs.table.update();
		}
	}
}
</script>