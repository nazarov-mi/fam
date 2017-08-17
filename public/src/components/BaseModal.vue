<script>
window.MODAL_INDEX = 2000;

export default {

	data() {
		return {
			show: false,
			index: ++ MODAL_INDEX
		}
	},

	mounted() {
		document.body.appendChild(this.$el);
	},

	destroyed() {
		document.body.removeChild(this.$el);
	},

	methods: {
		open() {
			this.show = true;
			document.getElementsByTagName('html')[0].classList.add('is-clipped');

			this.$emit('open');
		},
		close() {
			this.show = false;
			document.getElementsByTagName('html')[0].classList.remove('is-clipped');

			this.$emit('close');
		}
	}
}
</script>