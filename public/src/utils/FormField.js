
/**
 * FormField
 */

export default class FormField
{
	constructor(options) {
		var cur = {
			default: null,
			required: false,
			validator: null
		}, key;

		for (key in cur) {
			this[key] = cur[key];
		}

		if (typeof options == 'object') {
			for (key in options) {
				this[key] = options[key];
			}
		} else {
			this.default = options;
			
		}

		this.reset();
	}

	isNotEmpty()
	{
		return this.value !== undefined && this.value !== null && this.value !== '' && (!Array.isArray(this.value) || this.value.length > 0);
	}

	check() {
		this.error = this.isNotEmpty() ? (this.validator && !this.validator(this.value)) : this.required;

		return !this.error;
	}

	reset() {
		this.value = this.default;
		this.error = false;
	}

	get style()
	{
		return {
			'is-danger': this.error
		}
	}
}