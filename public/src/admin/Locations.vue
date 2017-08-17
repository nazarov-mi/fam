
<template lang="jade">
	div
		div.field
			p.control
				a.button.is-primary.is-outlined.add-button(@click="add")
					span.icon.is-small
						i.fa.fa-plus
					span Добавить
		vue-table(ref="table", get-api="/location/all", delete-api="/location/delete", :head="head", @change="open")
		vue-location-modal(ref="modal", @save="onSave")

</template>

<script>
import VueTable from './../components/Table'
import VueLocationModal from './../admin/LocationModal'

export default {

	components: {
		VueTable,
		VueLocationModal
	},

	data() {
		return {
			head: {
				name: 'Название',
				peoples: 'Максимальное кол-во человек'
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