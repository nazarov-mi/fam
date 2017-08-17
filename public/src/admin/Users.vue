
<template lang="jade">
	div
		div.field
			p.control
				a.button.is-primary.is-outlined.add-button(@click="add")
					span.icon.is-small
						i.fa.fa-plus
					span Добавить
		vue-table(ref="table", get-api="/user/all", delete-api="/user/delete", :head="head", @change="open")
		vue-user-modal(ref="modal", @save="onSave")

</template>

<script>
import VueTable from './../components/Table'
import VueUserModal from './../admin/UserModal'

export default {

	components: {
		VueTable,
		VueUserModal
	},

	data() {
		return {
			head: {
				name: 'Имя',
				username: 'Логин',
				create_at: 'Дата регистрации',
				login_at: 'Заходил последний раз'
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