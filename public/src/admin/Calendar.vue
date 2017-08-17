
<template lang="jade">
	div
		h3.title Календарь мероприятий
		h3.subtitle Для перемещения по временной шкале, перетащите её 
			span.is-hidden-mobile или используйте колесико мыши

		vue-calendar(ref="calendar", @change="open")
		vue-event-modal(ref="modal", @save="onSave")
</template>

<script>
import VueCalendar from './../components/Calendar'
import VueEventModal from './../admin/EventModal'

export default {
	
	components: {
		VueCalendar,
		VueEventModal
	},

	methods: {
		open(id) {
			this.$refs.modal.open(id);
		},
		onSave() {
			this.$refs.calendar.load();
		}
	}
}
</script>