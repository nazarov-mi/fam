
import Vue from 'Vue'
import VueResource from 'vue-resource'
import Login from './login/Login'

Vue.use(VueResource);

window.onload = function () {
	window._VM_ = new Vue({
		el: '#app',
		render (ce) {
			return ce('login');
		},
		components: { Login }
	})
}