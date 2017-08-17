
<style lang="sass">
	.event-calendar {
		cursor: pointer;
	}
</style>

<template lang="jade">
	div.event-calendar.is-unselectable
		svg(:width="width", :height="height")
			g
				text(font-family="Verdana", font-size="12", fill="#444", v-for="label in labels", :transform="label.transform", :text-anchor="label.archor")
					tspan(x="0," y="0") {{ label.text }}

			g
				path(fill="none", stroke-width="1", v-for="line in lines", :stroke="line.color", :d="line.d")

			g(v-for="timeline in timelines")
				rect(opacity="0.5", v-for="event in timeline", @click="changeEvent(event.id)", :transform="event.transform", :width="event.w", :height="event.h", :fill="event.color")

</template>

<script>
import DateUnit from './../utils/DateUnit.js'

const FROM_HOURS = 1000 * 60 * 60
	 , TO_HOURSE = 1 / FROM_HOURS
	 , FROM_DAYS = FROM_HOURS * 24
	 , TO_DAYS = 1 / FROM_DAYS;

export default {

	data() {
		return {
			width: 0,
			height: 0,
			gridX: 100,
			gridY: 25,
			gridWidth: 0,
			gridHeight: 0,
			rowHeight: 0,
			colWidth: 0,
			cols: 0,
			rows: 0,

			labels: [],
			lines: [],
			timelines: [],

			events: null,
			locations: null,
			timestamp: 0,
			curId: null,

			oldTimestamp: 0,
			touchX: 0,
			enterFrame: null
		}
	},

	mounted() {
		window.addEventListener('resize', this.update);
		this.$el.addEventListener('mousedown', this.startDrag);
		this.$el.addEventListener('mousewheel', this.onWheel);
		this.load();
		this.now();
		this.now();
		this.now();
	},

	beforeDestroy() {
		this.stopDrag();
		window.removeEventListener('resize', this.update);
		this.$el.removeEventListener('mousedown', this.startDrag);
		this.$el.removeEventListener('mousewheel', this.onWheel);
	},

	methods: {
		startDrag(e) {
			this.touchX = e.x;
			this.oldTimestamp = this.timestamp;

			window.addEventListener('mouseup', this.stopDrag);
			window.addEventListener('mousemove', this.onDrag);
		},
		stopDrag(e) {
			window.removeEventListener('mouseup', this.stopDrag);
			window.removeEventListener('mousemove', this.onDrag);
		},
		onDrag(e) {
			var days = Math.round((this.touchX - e.x) / this.colWidth);

			var ts = this.oldTimestamp + (days * FROM_DAYS);

			if (ts != this.timestamp) {
				this.timestamp = ts;
				this.update();
			}
		},
		onWheel(e) {
			this.timestamp += FROM_DAYS * (e.deltaY < 0 ? 1 : -1);
			this.update();
		},
		load() {
			this.$http.post('/event/calendar').then(
				(responce) => {
					this.setData(responce.body);
				},
				(error) => {
					console.log('Error: ' + error.statusText)
				}
			);
		},
		setData(data) {
			var res = {}
			  , l = data.length
			  , r = 0
			  , i, e;

			for (i = 0; i < l; ++ i) {
				e = data[i];

				if (!res[e.location]) {
					res[e.location] = [];
					++ r;
				}
				
				res[e.location].push(e);
			}

			this.events = data;
			this.locations = res;
			this.rows = r;

			this.update();
		},
		now() {
			this.timestamp = (new Date()).getTime();
			this.update();
		},
		show(id) {
			var es = this.events
			  , l = es.length
			  , i, e;

			for (i = 0; i < l; ++ i) {
				e = es[i];

				if (e.id === id) {
					this.timestamp = Math.round((e.end - e.start) * .5) + parseInt(e.start);
					this.curId = id;
					this.update();
					break;
				}
			}
		},
		update() {
			if (!this.enterFrame) {
				this.enterFrame = setTimeout(this.enterFrameHandler, 100);
			}
		},
		enterFrameHandler()
		{
			clearInterval(this.enterFrame);
			this.enterFrame = null;
			this.draw();
		},
		draw() {
			this.width = this.$el.offsetWidth || 0;
			this.gridWidth = this.width - this.gridX;
			this.cols = Math.max(parseInt(this.gridWidth * .01), 2);
			this.colWidth = this.gridWidth / this.cols;

			this.rowHeight = (this.width < 500 ? 50 : 35);
			this.gridHeight = this.rowHeight * this.rows;
			this.height = this.gridY + this.gridHeight;

			var ts = this.timestamp
			  , date = new DateUnit()
			  , day = Math.round(ts * TO_DAYS)
			  , start = (day - Math.ceil(this.cols * .5)) * FROM_DAYS
			  , end = (day + Math.floor(this.cols * .5)) * FROM_DAYS
			  , x, y, i, name;

			date.setTimestamp(start);
			this.lines = [];
			this.labels = [];
			this.timelines = [];
			
			// Lines

			for (i = 1; i < this.cols; ++ i) {
				x = this.colWidth * i;

				this.createLine(x, -6, x, this.gridHeight, '#e6e6e6');
			}

			for (i = 0; i < this.rows + 1; ++ i) {
				y = this.rowHeight * i;

				this.createLine(0, y, this.gridWidth, y, '#e6e6e6');
				this.createLine(0, y,             -6, y, '#000000');
			}

			this.createLine(             0, 0,              0, this.gridHeight, '#000000');
			this.createLine(this.gridWidth, 0, this.gridWidth, this.gridHeight, '#000000');

			// Labels

			for (i = .5; i < this.cols; ++ i) {
				x = this.colWidth * i;

				date.addDays(1);

				this.createLabel(x, -12, date.getShortFormattedDate(), 'middle');
			}

			i = .5;

			for (name in this.locations) {
				x = -this.gridX;
				y = this.rowHeight * i + 6;

				this.createLabel(x, y, name, 'start');
				++ i;
			}

			// Timeline

			i = 0;

			for (name in this.locations) {
				this.createTimeLine(i, start, end, this.locations[name]);
				++ i;
			}
		},
		createLine(xa, ya, xb, yb, color) {
			xa = this.gridX + parseInt(xa) - .5;
			ya = this.gridY + parseInt(ya) - .5;
			xb = this.gridX + parseInt(xb) - .5;
			yb = this.gridY + parseInt(yb) - .5;

			this.lines.push({
				d: 'M' + xa + ',' + ya + ' L' + xb + ',' + yb,
				color: color
			});
		},
		createLabel(x, y, text, archor) {
			x = this.gridX + parseInt(x) - .5;
			y = this.gridY + parseInt(y) - .5;

			this.labels.push({
				transform: 'translate(' + x + ',' + y + ')',
				text: text,
				archor: archor
			});
		},
		createTimeLine(row, start, end, es) {
			var res = []
			  , l = es.length
			  , i, e;

			for (i = 0; i < l; ++ i) {
				e = es[i];

				if (e.end <= start || e.start >= end) continue;

				res.push(this.createEvent(e, start, end, row));
			}

			this.timelines.push(res);
		},
		createEvent(e, start, end, row) {
			var sc = this.gridWidth / (end - start)
			  , x, y, w, h;

			x = Math.max(e.start, start);
			w = Math.min(e.end, end);

			w = parseInt((w - x) * sc) - .5;
			x = this.gridX + parseInt((x - start) * sc) - .5;

			h = this.rowHeight;
			y = this.gridY + h * row;

			return {
				transform: 'translate(' + x + ',' + y + ')',
				w: (w < 0 ? 0 : w),
				h: h,
				color: (e.id == this.curId ? '#6C4F8F' : (e.error == 1 ? '#ff3860' : (e.warn == 1 ? '#ffdd57' : '#dcdcdc'))),
				id: e.id
			}
		},
		changeEvent(id) {
			this.$emit('change', id);
		}
	}
}
</script>