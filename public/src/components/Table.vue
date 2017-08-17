
<style lang="sass">
	.vue-table {
		overflow-x: auto;
		
		.table {
			cursor: pointer;
		}

		td, th {
			vertical-align: middle;
		}

		.tag.is-warning {
			color: #fff;
		}

		.small-col {
			white-space: nowrap;
			width: 1%;
		}

		.sub-text {
			max-width: 200px;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}

		.sort {
			display: inline-block;
			margin-bottom: -7px;
			margin-left: 10px;
			float: right;

			&:after,
			&:before {
				content: ' ';
				display: block;
				width: 10px;
				height: 10px;
				border: 5px solid transparent;
			}

			&:before {
				border-bottom-color: #dcdcdc;
				margin-bottom: 3px;
			}

			&:after {
				border-top-color: #dcdcdc;
			}

			&.sort_desc:before {
				border-bottom-color: #12B886;
			}

			&.sort_asc:after {
				border-top-color: #12B886;
			}
		}
	}
</style>

<template lang="jade">
	div(v-if="list.length > 0 || isLoading", :class="{ overflow: isLoading }")

		div.level
			div.level-left
				div.field
					p.control
						span.select.is-fullwidth
							select(@change="setMax($event.target.value)")
								option(value="10") 10
								option(value="20") 20
								option(value="50") 50
								option(value="100") 100
			div.level-right
				div.field
					p.control.has-icon
						input.input(type="text", placeholder="Поиск", v-model.trim="filterKey")
						span.icon.is-small
							i.fa.fa-search(aria-hidden="true")

		div.vue-table
			table.table.is-striped
				thead.is-unselectable
					tr
						th.small-col
						th.small-col(v-if="$scopedSlots.default")
						th(v-for="(val, key) in head", @click="sortBy(key)") {{ val }}
							div.sort(:class="sortKey == key ? (sortDir > 0 ? 'sort_asc' : 'sort_desc') : ''")
				tbody
					tr(v-for="row in pageList", @click="changeRow(row)")
						td.small-col
							a.delete(@click.stop="deleteRow(row)")
						td.small-col(v-if="$scopedSlots.default")
							slot(:row="row")
						td.sub-text(v-for="(val, key) in head", :title="row[key]") {{ row[key] }}

		div.pagination(v-if="numPages > 1")
			a.pagination-previous(:class="{'is-disabled': page == 1}", @click="prevPage") &larr; Предыдущая
			a.pagination-next(:class="{'is-disabled': page == numPages}", @click="nextPage") Следующая &rarr;
			ul.pagination-list
				li(v-for="id in numPages")
					a.pagination-link(:class="{'is-current': id == page}", @click="gotoPage(id)") {{ id }}

	div(v-else) Нет событий
</template>

<script>
export default {

	props: {
		getApi: {
			type: String,
			required: true
		},
		deleteApi: {
			type: String,
			required: true
		},
		head: {
			type: Object,
			required: true
		}
	},

	data() {
		return {
			max: 10,
			page: 1,
			filterKey: '',
			sortKey: '',
			sortDir: 1,
			list: [],
			isLoading: false
		}
	},

	mounted() {
		this.update();
	},

	computed: {
		filteredList() {
			var filterKey = this.filterKey && this.filterKey.toLowerCase()
			  , sortKey = this.sortKey
			  , sortDir = this.sortDir
			  , list = this.list;

			this.page = 1;

			if (filterKey) {
				list = list.filter(function (row) {
					return Object.keys(row).some(function (key) {
						return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
					})
				})
			}

			if (sortKey) {
				list = list.slice().sort(function (a, b) {
					a = a[sortKey];
					b = b[sortKey];

					return (a !== b ? (a > b ? 1 : -1) : 0) * sortDir;
				})
			}
			
			return list;			
		},
		pageList() {
			var list = this.filteredList
			  , to = this.max * this.page
			  , from = to - this.max;
			
			return list.slice(from, to);
		},
		numPages() {
			var list = this.filteredList
			  , max = this.max;

			return Math.ceil(list.length / max);
		}
	},

	methods: {
		deleteRow(row) {
			this.isLoading = true;
			this.$http.post(this.deleteApi, { 'id': row.id }).then(
				(response) => {
					if (response.body.status) {
						this.update();
					}
				},
				(error) => {
					console.log('Error: ' + error.statusText);
				}
			);
		},
		changeRow(row) {
			this.$emit('change', row.id);
		},
		update() {
			this.isLoading = true;
			this.$http.post(this.getApi).then(
				(response) => {
					this.list = response.body;
					this.isLoading = false;
				},
				(error) => {
					console.log('Error: ' + error.statusText);
					this.isLoading = false;
				}
			);
		},
		setMax(max) {
			if (max > 0) {
				this.max = max;
				this.page = 1;
			}
		},
		sortBy(key) {
			if (this.sortKey == key) {
				this.sortDir *= -1;
			} else {
				this.sortKey = key;
				this.sortDir = 1;
			}
		},
		gotoPage(i) {
			if (i >= 1 && i <= this.numPages) {
				this.page = i;
			}
		},
		prevPage() {
			if (this.page > 1) {
				-- this.page;
			}
		},
		nextPage() {
			if (this.page < this.numPages) {
				++ this.page;
			}
		}
	}
}
</script>