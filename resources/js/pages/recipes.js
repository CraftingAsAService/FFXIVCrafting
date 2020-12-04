const SearchBox = {
	data () {
		return {
			craftingListIds: craftingListIds,
			jobs: null,
			searchData: {
				name: '',
				sort: 'name.asc',
				levelMin: 1,
				levelMax: maxLevel,
				stars: 'any',
				jobs: {
					Any: 99
				},
				page: 1,
				perpage: 30
			},
			response: null,
			results: false,
		}
	},
	mounted () {
		this.restoreSession()
		this.loadJobs()
	},
	computed: {
		splitResults () {
			const pieces = window.innerWidth > 991 ? 3 : (window.innerWidth < 768 ? 1 : 2)
			const split = Math.ceil(this.results.length / pieces)

			if (pieces == 1) {
				return [ this.results ]
			} else if (pieces == 2) {
				return [
					this.results.splice(0, split),
					this.results.splice(-split)
				]
			}

			return [
				this.results.splice(0, split),
				this.results.splice(split, split + split),
				this.results.splice(-split)
			]
		}
	},
	methods: {
		restoreSession () {
			this.searchData = JSON.parse(localStorage.getItem('recipesSearchData', '{}')) || this.searchData
		},
		loadJobs () {
			axios
				.get('/api/job/types/crafting')
				.then(response => { this.jobs = response.data })
				// .catch(error => console.log(error))
		},
		toggleJobs (id, name) {
			this.searchData.jobs[name] = id - (this.searchData.jobs[name] || 0)

			// If any class has a positive value, stop
			let good = 0
			for (const [name, value] of Object.entries(this.searchData.jobs)) {
				if (name !== 'Any' && value > 0) {
					good++
				}
			}

			if (good === 0 || (name == 'Any' && this.searchData.jobs['Any'] === 99)) {
				// No selected values, ensure the Any is selected
				this.searchData.jobs = { 'Any': 99 }
			} else {
				// Otherwise, make sure Any is not selected
				this.searchData.jobs['Any'] = 0
			}
		},
		search () {
			this.searchData.page = 1
			this.searchSubmit()
		},
		searchSubmit() {
			localStorage.setItem('recipesSearchData', JSON.stringify(this.searchData))
			axios
				.post('/api/recipe/search', this.searchData)
				.then(response => {
					this.response = response.data
					this.results = this.response.data // aka response.data.data
				})
				// .catch(error => console.log(error))
		},
		previousPage () {
			this.searchData.page--
			this.searchSubmit()
		},
		nextPage () {
			this.searchData.page++
			this.searchSubmit()
		}
	}
}

Vue.createApp(SearchBox).mount('#searchBox')