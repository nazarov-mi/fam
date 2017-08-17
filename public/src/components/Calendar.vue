
<style lang="sass">
	.event-calendar {
		cursor: pointer;
	}
</style>

<template lang="jade">
	div.event-calendar.is-unselectable(:class="{ overflow: isLoading }")
		svg(:width="width", :height="height")
			g
				path(fill="none", stroke-width="1", v-for="line in lines", :stroke="line.color", :d="line.d")

			g
				text(font-family="Verdana", font-size="12", fill="#444", v-for="label in labels", :transform="label.transform", :text-anchor="label.archor")
					tspan(x="0," y="0") {{ label.text }}

			g(v-for="timeline in timelines")
				rect(opacity="0.5", v-for="event in timeline", :transform="event.transform", :width="event.w", :height="event.h", :fill="event.color", @click="changeEvent(event.id)")

</template>

<script>
import moment from 'moment'

moment.locale('ru');

const FROM_HOURS = 1000 * 60 * 60
	 , TO_HOURSE = 1 / FROM_HOURS
	 , FROM_DAYS = FROM_HOURS * 24
	 , TO_DAYS = 1 / FROM_DAYS;

export default {

	data() {
		return {
			width: 0,
			height: 0,
			headHeight: 25,
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
			enterFrame: null,
			isLoading: false,

			isMobile: 'ontouchstart' in document,
			wheelEvent: 'onwheel' in document ? 'wheel' : ('onmousewheel' in document ? 'onmousewheel' : 'MozMousePixelScroll')
		}
	},

	mounted() {
		window.addEventListener('resize', this.update);

		if (this.isMobile) {
			this.$el.addEventListener('touchstart', this.startDrag);
		} else {
			this.$el.addEventListener('mousedown', this.startDrag);
		}
		
		this.$el.addEventListener(this.wheelEvent, this.onWheel);

		this.load();
		this.now();
	},

	beforeDestroy() {
		this.stopDrag();
		window.removeEventListener('resize', this.update);

		if (this.isMobile) {
			this.$el.removeEventListener('touchstart', this.startDrag);
		} else {
			this.$el.removeEventListener('mousedown', this.startDrag);
			this.$el.removeEventListener(this.wheelEvent, this.onWheel);
		}
	},

	methods: {
		startDrag(e) {
			e = e || window.event;

			this.touchX = (e.targetTouches ? e.targetTouches[0].clientX : e.clientX);
			this.oldTimestamp = this.timestamp;

			if (this.isMobile) {
				window.addEventListener('touchend', this.stopDrag);
				window.addEventListener('touchmove', this.onDrag);
			} else {
				window.addEventListener('mouseup', this.stopDrag);
				window.addEventListener('mousemove', this.onDrag);
			}
		},
		stopDrag(e) {
			if (this.isMobile) {
				window.removeEventListener('touchend', this.stopDrag);
				window.removeEventListener('touchmove', this.onDrag);
			} else {
				window.removeEventListener('mouseup', this.stopDrag);
				window.removeEventListener('mousemove', this.onDrag);
			}
		},
		onDrag(e) {
			e = e || window.event;

			var clientX = (e.targetTouches ? e.targetTouches[0].clientX : e.clientX)
			  , days = Math.round((this.touchX - clientX) / this.colWidth)
			  , ts = this.oldTimestamp + (days * FROM_DAYS);
			
			if (ts != this.timestamp) {
				this.timestamp = ts;
				this.update();
			}
		},
		onWheel(e) {
			e = e || window.event;

			var delta = (e.deltaY || e.detail || e.wheelDelta || 0) < 0 ? 1 : -1;

			this.timestamp += FROM_DAYS * delta;
			this.update();

			e.preventDefault ? e.preventDefault() : (e.returnValue = false);
		},
		load() {
			this.isLoading = true;
			this.$http.post('/event/calendar').then(
				(responce) => {
					this.setData(responce.body);
					this.isLoading = false;
				},
				(error) => {
					console.log('Error: ' + error.statusText);
					this.isLoading = false;
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
			if (this.curId == id) return;

			var es = this.events
			  , l = es.length
			  , i, e;

			for (i = 0; i < l; ++ i) {
				e = es[i];

				if (e.id === id) {
					this.timestamp = Math.round((e.end - e.start) * .5) + parseInt(e.start, 10);
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
			this.cols = Math.max(Math.round(this.width * .01), 3);
			this.colWidth = this.width / this.cols;

			this.rowHeight = (this.width < 769 ? 40 : 35);
			this.gridHeight = this.rowHeight * this.rows;
			this.height = this.headHeight + this.gridHeight;

			var ts = this.timestamp
			  , day = Math.floor(ts * TO_DAYS)
			  , start = (day - Math.floor(this.cols * .5)) * FROM_DAYS
			  , end = start + this.cols * FROM_DAYS
			  , date = moment(start)
			  , x, y, i, name;
			
			this.lines = [];
			this.labels = [];
			this.timelines = [];
			
			// Lines

			for (i = 1; i < this.cols; ++ i) {
				x = this.colWidth * i;
				y = this.headHeight - 6;

				this.createLine(x, y, x, this.height, '#dcdcdc');
			}

			for (i = 0; i < this.rows + 1; ++ i) {
				y = this.headHeight + this.rowHeight * i;

				this.createLine(0, y, this.width, y, '#dcdcdc');
			}
			
			this.createLine(         1, this.headHeight,          1, this.height, '#000000');
			this.createLine(this.width, this.headHeight, this.width, this.height, '#000000');

			// Labels

			for (i = .5; i < this.cols; ++ i) {
				x = this.colWidth * i;
				y = this.headHeight - 12;

				this.createLabel(x, y, date.format('D MMM YYYY'), 'middle');
				date.add(1, 'd');
			}

			i = .5;

			for (name in this.locations) {
				x = 6;
				y = this.headHeight + this.rowHeight * i + 6;

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
			xa = parseInt(xa, 10) - .5;
			ya = parseInt(ya, 10) - .5;
			xb = parseInt(xb, 10) - .5;
			yb = parseInt(yb, 10) - .5;

			this.lines.push({
				d: 'M' + xa + ',' + ya + ' L' + xb + ',' + yb,
				color: color
			});
		},
		createLabel(x, y, text, archor) {
			x = parseInt(x, 10) - .5;
			y = parseInt(y, 10) - .5;

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
			var sc = this.width / (end - start)
			  , x, y, w, h;

			x = Math.max(e.start, start);
			w = Math.min(e.end, end);

			w = parseInt((w - x) * sc, 10) - .5;
			x = parseInt((x - start) * sc, 10) - .5;

			h = this.rowHeight;
			y = this.headHeight + h * row;

			return {
				transform: 'translate(' + x + ',' + y + ')',
				w: (w < 0 ? 0 : w),
				h: h,
				color: (e.id == this.curId ? '#12b886' : (e.error == 1 ? '#ff3860' : (e.warn == 1 ? '#FFDD57' : '#cbcbcb'))),
				id: e.id
			}
		},
		changeEvent(id) {
			this.$emit('change', id);
		}
	}
}
</script>