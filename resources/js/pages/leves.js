const LevesBox = {
	data () {
		return {
			craftingListIds: craftingListIds,
			maxLevel: maxLevel,
			activeJob: 8,
			activeLevel: 1,
			hq: false,
			jobs: null,
			levels: null,
			response: null,
			results: false
		}
	},
	mounted () {
		this.restoreSession()
		this.loadJobs()
		this.configureLevels()
		this.search()
	},
	watch: {
		hq: function (newValue, old) {
			localStorage.setItem('leveHQToggle', newValue)
		}
	},
	methods: {
		restoreSession () {
			this.activeJob = localStorage.getItem('leveActiveJob') || this.activeJob
			this.activeLevel = localStorage.getItem('leveActiveLevel') || this.activeLevel
			this.hq = localStorage.getItem('leveHQToggle') || this.hq
		},
		loadJobs () {
			axios
				.get('/api/job/types/crafting')
				.then(response => { this.jobs = response.data })
				// .catch(error => console.log(error))
		},
		configureLevels () {
			this.levels = [
				'01', '05',
				...Array.from(Array(9).keys()).map((i) => i * 5 + 10), // 10-45
				...Array.from(Array((this.maxLevel - 52) / 2).keys()).map((i) => i * 2 + 52) // 52-78[maxLevel - 2]
			]
		},
		search () {
			axios
				.post('/api/leve/search', {
					jobId: this.activeJob,
					level: this.activeLevel
				})
				.then(response => {
					this.response = response.data
					this.results = this.response.data // aka response.data.data
					console.log(this.results)
				})
				// .catch(error => console.log(error))
		},
		updateJob (jobId) {
			this.activeJob = jobId
			localStorage.setItem('leveActiveJob', this.activeJob)
			this.search()
		},
		updateLevel (level) {
			this.activeLevel = level
			localStorage.setItem('leveActiveLevel', this.activeLevel)
			this.search()
		}
	}
}

Vue.createApp(LevesBox).mount('#levesBox')