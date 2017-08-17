
<style lang="sass">
	@import '../../css/selectize.css';

	.is-danger ~ .selectize-control .selectize-input {
		border-color: #ff3860;
	}
</style>

<template lang="jade">
	select
		slot
</template>

<script>
import Selectize from 'selectize'
import $ from 'jquery'

export default {

	props: {
		placeholder: String,
		multiple: {
			type: Boolean,
			default: false
		},
		src: {
			type: String,
			default: null
		},
		options: {
			type: Object,
			default: null
		},
		value: {
			type: [String, Number, Array],
			default: null
		}
	},

	data() {
		return {
			selectize: null
		}
	},

	watch: {
		value(value) {
			var newVal = [].concat(value).join(',')
			  , oldVal = this.selectize.items.join(',');

			if (newVal !== oldVal) {
				this.setValue(value);
			}
		}
	},

	mounted() {
		var self = this
		  , settings = {
				plugins: ['remove_button'],
				maxItems: (this.multiple ? null : 1),
				placeholder: this.placeholder,
				hideSelected: true,
				valueField: 'value',
				labelField: 'label',
				searchField: ['label']
			};

		if (this.options) {
			settings.options = [];
			$.each(this.options, (value, label) => {
				settings.options.push({
					value: value,
					label: label
				});
			});
		}

		if (this.src) {
			$.extend(settings, {
				preload: 'focus',
				load(query, callback) {
					self.$http.post(self.src, {
						q: query,
						num: 20
					}).then(
						(response) => {
							callback(response.body);
						},
						(error) => {
							callback();
						}
					);
				}
			});
		}
		
		this.selectize = new Selectize($(this.$el), $.extend({}, Selectize.defaults, settings));

		$(this.$el).on('change', () => {
			this.$emit('input', this.selectize.getValue());
		});

		this.setValue(this.value);
	},

	destroyed() {
		this.selectize.destroy();
	},

	methods: {
		setValue(value) {
			if (this.src) {
				this.$http.post(this.src, { value: value }).then(
					(response) => {
						this.selectize.addOption(response.body);
						this.selectize.setValue(value, true);
					},
					(error) => {
						console.log(error.body);
					}
				);
			} else {
				this.selectize.setValue(value, true);
			}
		}
	}
}
</script>