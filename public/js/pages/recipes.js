const SearchBox={data:()=>({craftingListIds:craftingListIds,jobs:null,searchData:{name:"",sort:"name.asc",levelMin:1,levelMax:maxLevel,stars:"any",jobs:{Any:99},page:1,perpage:30},response:null,results:!1}),mounted(){this.restoreSession(),this.loadJobs()},computed:{splitResults(){const s=window.innerWidth>991?3:window.innerWidth<768?1:2,e=Math.ceil(this.results.length/s);return 1==s?[this.results]:2==s?[this.results.splice(0,e),this.results.splice(-e)]:[this.results.splice(0,e),this.results.splice(e,e+e),this.results.splice(-e)]}},methods:{restoreSession(){this.searchData=JSON.parse(localStorage.getItem("recipesSearchData","{}"))||this.searchData},loadJobs(){axios.get("/api/job/types/crafting").then((s=>{this.jobs=s.data}))},toggleJobs(s,e){this.searchData.jobs[e]=s-(this.searchData.jobs[e]||0);let t=0;for(const[s,e]of Object.entries(this.searchData.jobs))"Any"!==s&&e>0&&t++;0===t||"Any"==e&&99===this.searchData.jobs.Any?this.searchData.jobs={Any:99}:this.searchData.jobs.Any=0},search(){this.searchData.page=1,this.searchSubmit()},searchSubmit(){localStorage.setItem("recipesSearchData",JSON.stringify(this.searchData)),axios.post("/api/recipe/search",this.searchData).then((s=>{this.response=s.data,this.results=this.response.data}))},previousPage(){this.searchData.page--,this.searchSubmit()},nextPage(){this.searchData.page++,this.searchSubmit()}}};Vue.createApp(SearchBox).mount("#searchBox");