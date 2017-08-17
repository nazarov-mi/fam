
<template lang="jade">
	div.modal.is-active(v-show="show", :style="{ 'z-index': index }")
		div.modal-background
		div.modal-card
			div.modal-card-head
				p.modal-card-title {{ title }}
				button.delete(@click="close")
			div.modal-card-body
				slot
			div.modal-card-foot
				a.button.is-primary(@click="ok") {{ btnTitle }}
				a.button(@click="cancel") Отмена
</template>

<script>
import VueBaseModal from './../components/BaseModal'

export default {

	mixins: [ VueBaseModal ],

	props: {
		title: {
			type: String,
			default: 'Title'
		},
		btnTitle: {
			type: String,
			default: 'Ок'
		}
	},

	methods: {
		ok() {
			this.$emit('ok');
		},
		cancel() {
			this.$emit('cancel');
			this.close();
		}
	}
}
</script>