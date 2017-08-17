
<style lang="sass">

	.datepicker {
		table {
			width: 100%;
			height: 210px;

			td, th {
				width: 14.2857%;
				text-align: center;
				vertical-align: middle;
			}
		}

		.datepicker__item {
			border-radius: 3px;
			line-height: 30px;
			text-align: center;
			cursor: pointer;

			* {
				line-height: 30px;
			}

			&:hover {
				background-color: whitesmoke;
			}

			&.datepicker__item_prev,
			&.datepicker__item_next {
				opacity: .5;
			}

			&.datepicker__item_today {
				position: relative;

				&:after {
					content: ' ';
					position: absolute;
					display: block;
					left: 10%;
					bottom: 3px;
					width: 80%;
					height: 2px;
					background-color: #12B886;
				}
			}

			&.datepicker__item_active {
				background-color: #12B886;
				color: #fff;

				&.datepicker__item_today:after {
					background-color: #fff;
				}
			}
		}

		.datepicker__time {
			font-size: 1.4em;
		}

		.datepicker__nav {
			
			& > div {
				float: left;
			}

			&:after {
				content: ' ';
				display: table;
				clear: both;
			}

			.datepicker__arrow {
				width: 15%;
			}

			.datepicker__head {
				width: 70%;
			}
		}
	}

</style>

<template lang="jade">
	div
		input.input(type="text", ref="input", :value="curValue", :class="classes", :placeholder="placeholder", @focus="show")
		vue-modal(ref="modal")
			div.datepicker.is-unselectable
				div.datepicker__item(@click="clear", v-show="curValue !== undefined")
					i.fa.fa-trash(aria-hidden="true")
					b &nbsp;Очистить

				div(v-if="tab == 'months'")
					div.datepicker__nav
						div.datepicker__item.datepicker__arrow(@click="sub('y')")
							i.fa.fa-arrow-left(aria-hidden="true")
						div.datepicker__item.datepicker__head
							b {{ cur.format('YYYY') }}
						div.datepicker__item.datepicker__arrow(@click="add('y')")
							i.fa.fa-arrow-right(aria-hidden="true")
					table
						tr(v-for="row in months")
							td.datepicker__item(v-for="month in row", @click="setMonth(month)") {{ month | capitalize }}
					div.datepicker__item(@click="setTab('date')")
						i.fa.fa-calendar(aria-hidden="true")

				div(v-else-if="tab == 'date'")
					div.datepicker__nav
						div.datepicker__item.datepicker__arrow(@click="sub('M')")
							i.fa.fa-arrow-left(aria-hidden="true")
						div.datepicker__item.datepicker__head(@click="setTab('months')")
							b {{ cur.format('MMM YYYY') | capitalize }}
						div.datepicker__item.datepicker__arrow(@click="add('M')")
							i.fa.fa-arrow-right(aria-hidden="true")
					table
						tr
							th(v-for="day in week") {{ day | capitalize }}
						tr(v-for="row in date")
							td.datepicker__item(v-for="day in row", :class="day.class", @click="setDate(day.value)") {{ day.label }}
					div.datepicker__item(@click="setTab('time')")
						i.fa.fa-clock-o(aria-hidden="true")

				div(v-else-if="tab == 'time'")
					div.datepicker__item(@click="setTab('date')")
						b {{ cur.format('MMM YYYY') | capitalize }}
					table
						tr
							td.datepicker__item(@click="add('h')")
								i.fa.fa-arrow-up(aria-hidden="true")
							td
							td.datepicker__item(@click="add('m', 5)")
								i.fa.fa-arrow-up(aria-hidden="true")
						tr
							td.datepicker__item.datepicker__time(@click="setTab('hours')") {{ cur.format('HH') }}
							td.datepicker__time
								b :
							td.datepicker__item.datepicker__time(@click="setTab('minutes')") {{ cur.format('mm') }}
						tr
							td.datepicker__item(@click="sub('h')")
								i.fa.fa-arrow-down(aria-hidden="true")
							td
							td.datepicker__item(@click="sub('m', 5)")
								i.fa.fa-arrow-down(aria-hidden="true")
					div.datepicker__item(@click="setTab('date')")
						i.fa.fa-calendar(aria-hidden="true")

				div(v-else-if="tab == 'hours'")
					div.datepicker__item(@click="setTab('time')")
						b {{ cur.format('HH:mm') }}
					table
						tr(v-for="row in hours")
							td.datepicker__item(v-for="val in row", @click="setHour(val)") {{ val }}
					div.datepicker__item(@click="setTab('time')")
						i.fa.fa-clock-o(aria-hidden="true")

				div(v-else-if="tab == 'minutes'")
					div.datepicker__item(@click="setTab('time')")
						b {{ cur.format('HH:mm') }}
					table
						tr(v-for="row in minutes")
							td.datepicker__item(v-for="val in row", @click="setMinutes(val)") {{ val }}
					div.datepicker__item(@click="setTab('time')")
						i.fa.fa-clock-o(aria-hidden="true")

</template>

<script>
import moment from 'moment'
import VueModal from './../components/Modal'

moment.locale('ru');

export default {

	components: {
		VueModal
	},

	props: {
		value: {
			type: String,
			default: undefined
		},
		classes: [String, Array, Object],
		placeholder: String
	},

	data() {
		return {
			curValue: this.value || undefined,
			cur: moment(this.curValue),
			tab: 'date'
		}
	},

	watch: {
		value: 'setValue'
	},

	computed: {
		months() {
			var res = []
			  , cur = this.cur.clone().startOf('y').startOf('d')
			  , row, i;

			for (i = 0; i < 12; ++ i) {
				if (i % 4 === 0) {
					row = [];
					res.push(row);
				}

				row.push(cur.format('MMM'));
				cur.add(1, 'M');
			}

			return res;
		},
		week() {
			var res = []
			  , cur = this.cur.clone().startOf('w').startOf('d')
			  , i;

			for (i = 0; i < 7; ++ i) {
				res.push(cur.format('dd'));
				cur.add(1, 'd');
			}

			return res;
		},
		date() {
			var res = []
			  , cur = this.cur.clone().startOf('M').startOf('w').startOf('d')
			  , now = moment()
			  , row, i, cls;

			for (i = 0; i < 42; ++ i) {
				if (cur.weekday() === 0) {
					row = [];
					res.push(row);
				}

				cls = [];

				if (cur.isBefore(this.cur, 'M')) {
					cls.push('datepicker__item_prev');
				}

				if (cur.isAfter(this.cur, 'M')) {
					cls.push('datepicker__item_next');
				}

				if (cur.isSame(this.cur, 'd') && !this.unset) {
					cls.push('datepicker__item_active');
				}

				if (cur.isSame(now, 'd')) {
					cls.push('datepicker__item_today');
				}

				row.push({
					value: cur.format('L'),
					label: cur.date(),
					class: cls
				});

				cur.add(1, 'd');
			}

			return res;
		},
		hours() {
			var res = []
			  , row, i;

			for (i = 0; i < 24; ++ i) {
				if (i % 4 === 0) {
					row = [];
					res.push(row);
				}

				row.push((i < 10 ? '0' : '') + i);
			}

			return res;
		},
		minutes() {
			var res = []
			  , row, i;

			for (i = 0; i < 12; ++ i) {
				if (i % 4 === 0) {
					row = [];
					res.push(row);
				}

				row.push(((i * 5) < 10 ? '0' : '') + (i * 5));
			}

			return res;
		},
		iso() {
			return this.cur.format('YYYY-MM-DD HH:mm');
		}
	},

	methods: {
		show() {
			this.$refs.modal.open();
			this.$refs.input.blur();
			this.setTab('date');
		},
		setTab(v) {
			this.tab = v;
		},
		clear() {
			this.setValue();
			this.$refs.modal.close();
		},
		setValue(v) {
			if (this.curValue !== v) {
				this.curValue = v || undefined;
				this.cur = moment(this.curValue);
				this.$emit('input', this.curValue);
			}
		},
		setMoment(m) {
			if (m.isValid()) {
				this.cur = m;
				this.curValue = this.iso;
				this.$emit('input', this.iso);
			}
		},
		setMonth(v) {
			var cur = this.cur.clone().month(v);
			this.setMoment(cur);
			this.setTab('date');
		},
		setDate(v) {
			v = String(v).split('.');
			var cur = this.cur.clone()
				.year(v[2])
				.month(v[1] - 1)
				.date(v[0]);

			this.setMoment(cur);
		},
		setHour(v) {
			var cur = this.cur.clone().hour(v);
			this.setMoment(cur);
			this.setTab('time');
		},
		setMinutes(v) {
			var cur = this.cur.clone().minutes(v);
			this.setMoment(cur);
			this.setTab('time');
		},
		add(p, n) {
			var cur = this.cur.clone().add(n || 1, p);
			this.setMoment(cur);
		},
		sub(p, n) {
			var cur = this.cur.clone().subtract(n || 1, p);
			this.setMoment(cur);
		}
	},

	filters: {
		capitalize(v) {
			return v.charAt(0).toUpperCase() + v.slice(1);
		}
	}
}
</script>