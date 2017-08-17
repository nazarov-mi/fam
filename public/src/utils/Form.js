
/**
 * Form
 */

import FormField from './FormField.js'

export default class Form
{
	constructor(fields) {
		this._fields = fields;
		this.isLoading = false;

		for (var key in this._fields) {
			this[key] = new FormField(this._fields[key]);
		}
	}

	has(key) {
		return this._fields.hasOwnProperty(key);
	}

	set(key, value) {
		if (this.has(key)) {
			this[key].value = value;
		}
	}

	get(key, def) {
		return this[key].value || def;
	}

	add(data) {
		for (var key in data) {
			this.set(key, data[key]);
		}
	}

	all() {
		var data = {}
		  , key, field;

		for (key in this._fields) {
			field = this[key];
			
			if (field.isNotEmpty()) {
				data[key] = field.value;
			}
		}
		
		return data;
	}

	reset() {
		for (var key in this._fields) {
			this[key].reset();
		}

		this.resetErrors();
	}

	record(data) {
		this.reset();
		this.add(data);
	}

	setQueryString(query) {
		query = query.slice(query.indexOf('?') + 1);

		var params = query.split('&')
		  , data = {}
		  , val;

		for (i = data.length; i >= 0; -- i) {
			val = params[i];
			if (val) {
				val = val.split('=');
				data[val[0]] = val[1];
			}
		}

		this.record(data);
	}

	getQueryString(url) {
		var data = this.all()
		  , res = []
		  , key;
		
		for (key in data) {
			res.push(key + '=' + data[key]);
		}

		return url + (url.indexOf('?') < 0 ? '?' : '&') + res.join('&');
	}

	// Errors

	anyErrors() {
		for (var key in this._fields) {
			if (this[key].error) {
				return true;
			}
		}

		return false;
	}

	resetErrors() {
		for (var key in this._fields) {
			this[key].error = false;
		}
	}

	// Submit

	check() {
		var res = true
		  , key;

		this.resetErrors();

		for (key in this._fields) {
			res = this[key].check() && res;
		}

		return res;
	}

	submit(url, callback) {
		if (!this.check()) return;
		
		this.isLoading = true;

		window._VM_.$http.post(url, this.all()).then(
			(response) => {
				this.isLoading = false;
				if (response.body.status && callback) {
					callback && callback(response.body);

					if (response.body.data) {
						this.add(response.body.data);
					}
				}
			},
			(error) => {
				this.isLoading = false;
				console.log('Error: ' + error.statusText);
			}
		);
	}

	load(url, id, callback) {
		this.reset();
		this.isLoading = true;

		window._VM_.$http.post(url, { 'id': id }).then(
			(response) => {
				this.isLoading = false;
				this.add(response.body);
				callback && callback(response.body);
			},
			(error) => {
				this.isLoading = false;
				console.log('Error: ' + error.statusText);
			}
		);
	}

	get style() {
		return {
			overflow: this.isLoading
		}
	}
}