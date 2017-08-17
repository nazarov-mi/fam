
<template lang="jade">
	div.modal.is-active(v-show="show", :style="{ 'z-index': index }")
		div.modal-background
		div.modal-content
			div.box
				slot
		button.modal-close(@click="close")
</template>

<script>
import VueBaseModal from './../components/BaseModal'

export default {

	mixins: [ VueBaseModal ]
}
</script>