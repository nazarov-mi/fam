
import Vue from 'Vue'
import VueRouter from 'vue-router'
import VueResource from 'vue-resource'
import Admin from './admin/Admin'
import Index from './admin/Index'
import Events from './admin/Events'
import Types from './admin/Types'
import Audience from './admin/Audience'
import Locations from './admin/Locations'
import Calendar from './admin/Calendar'
import Reports from './admin/Reports'
import Users from './admin/Users'

Vue.use(VueRouter);
Vue.use(VueResource);

var router = new VueRouter({
	routes: [
		{
			path: '/',
			component: Index
		},
		{
			path: '/calendar',
			component: Calendar
		},
		{
			path: '/events',
			component: Events
		},
		{
			path: '/types',
			component: Types
		},
		{
			path: '/audience',
			component: Audience
		},
		{
			path: '/locations',
			component: Locations
		},
		{
			path: '/reports',
			component: Reports
		},
		{
			path: '/users',
			component: Users
		}
	],
	linkActiveClass: 'is-active',
	// mode: 'history'
})

window.onload = function () {
	window._VM_ = new Vue({
		el: '#app',
		render (ce) {
			return ce('admin');
		},
		components: { Admin },
		router
	})
}