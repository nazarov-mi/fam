
<template lang="jade">
	div
		div.field
			p.control
				a.button.is-primary.is-outlined.add-button(@click="add")
					span.icon.is-small
						i.fa.fa-plus
					span Добавить
		vue-table(ref="table", get-api="/audience/all", delete-api="/audience/delete", :head="head", @change="open")
		vue-audience-modal(ref="modal", @save="onSave")

</template>

<script>
import VueTable from './../components/Table'
import VueAudienceModal from './../admin/AudienceModal'

export default {

	components: {
		VueTable,
		VueAudienceModal
	},

	data() {
		return {
			head: {
				name: 'Название',
				desc: 'Описание'
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