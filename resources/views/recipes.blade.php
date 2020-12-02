@extends('app')

@section('meta')
	<meta name="robots" content="nofollow">
@stop

@section('vendor-css')
	<style>
		.class-selector input {
			display: none;
		}
		.class-selector {
			padding: 4px 8px;
			margin-bottom: 4px;
			border: 1px solid transparent;
			border-radius: 4px;
			margin-right: 8px;
		}
		.class-selector.active {
			border: 1px solid #5ab65a;
		}

		[v-cloak] {
			display: none;
		}
		html #banner h1 {
			margin-bottom: 0;
		}
		html #content {
			padding-top: 0;
		}
	</style>
@stop

@section('javascript')
	{{-- Include Vue --}}

	{{-- @if (app()->environment('production')) --}}
	<script src='https://unpkg.com/vue@3.0.2/dist/vue.global.js'></script>
	{{-- @else
		<script src='https://unpkg.com/vue@next'></script>
	@endif --}}
	<script src='https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js' integrity='sha512-DZqqY3PiOvTP9HkjIWgjO6ouCbq+dxqWoJZ/Q+zPYNHmlnI2dQnbJ5bxAHpAMw+LXRm4D72EIRXzvcHQtE8/VQ==' crossorigin='anonymous'></script>

	<script>
		const craftingListIds = @json(array_keys(session('list', [])));
		const maxLevel = {{ config('site.max_level') }};
	</script>

	<script type='text/javascript' src='{{ cdn('/js/pages/recipes.js') }}'></script>
@stop

@section('banner')
	<h1>
		Recipe Book
	</h1>
@stop

@section('content')
	<div id='searchBox' v-cloak>
		<div class='well'>
			<h5 style='margin-top: 0;'>Jobs</h5>

			<div v-if='jobs !== null'>
				<label class='class-selector' :class="searchData.jobs['Any'] > 0 ? 'active' : ''">
					<input type='checkbox' name='class' value='0' checked='checked' @click='toggleJobs(99, "Any")'>
					<img src='/img/roles/hand.png' alt='' width="24" height="24"> <span class="abbr">All</span>
				</label>
				<label class='class-selector' v-for='j in jobs' :key='j.id' :class="searchData.jobs[j.abbr] > 0 ? 'active' : ''">
					<input type='checkbox' name='class' :value='j.id' @click='toggleJobs(j.id, j.abbr)'>
					<img :src='"/img/jobs/" + j.abbr.toUpperCase() + "-inactive.png"' alt='' width="24" height="24"> <span class="abbr hidden-xs hidden-sm" v-html='j.abbr' style='width: 32px; display: inline-block;'></span>
				</label>
			</div>

			<div class='row'>
				<div class='col-sm-5'>
					<h5>Search</h5>

					<input type='text' class='form-control' v-model='searchData.name' maxlength='80' @keyup.enter='search()'>
					<p class='help-block'>Use <kbd>*</kbd> as a wildcard!</p>
				</div>
				<div class='col-xs-6 col-sm-2'>
					<h5>Sorting</h5>

					<select name='order-by' id='order-by' class='form-control' v-model='searchData.sort'>
						<option value='name.asc'>Name: a to z</option>
						<option value='name.desc'>Name: z to a</option>
						<option value='recipe_level.asc'>Level: low to high</option>
						<option value='recipe_level.desc'>Level: high to low</option>
					</select>
				</div>
				<div class='col-xs-6 col-sm-2'>
					<h5>Level Range</h5>
					<div class='row'>
						<div class='col-xs-6'>
							<input type='number' v-model='searchData.levelMin' class='form-control margin-right' min='1' max='{{ config('site.max_level') }}' style='width: 57px; display: inline-block;' @keyup.enter='search()'>
						</div>
						<div class='col-xs-6'>
							<input type='number' v-model='searchData.levelMax' class='form-control' min='1' max='{{ config('site.max_level') }}' style='width: 57px; display: inline-block;' @keyup.enter='search()'>
						</div>
					</div>
				</div>
				<div class='col-xs-6 col-sm-2'>
					<h5>Stars</h5>

					<select class='form-control' name='stars' v-model='searchData.stars'>
						<option value='any'>Any</option>
						<option value='0'>☆☆☆☆</option>
						<option value='1'>★☆☆☆</option>
						<option value='2'>★★☆☆</option>
						<option value='3'>★★★☆</option>
						<option value='4'>★★★★</option>
					</select>
				</div>
				<div class='col-xs-6 col-sm-1 text-right-xs'>
					<h5>&nbsp;</h5>
					<button class='btn btn-success' type='button' @click='search()'><i class='glyphicon glyphicon-search'></i></button>
				</div>
			</div>
		</div>

		<div v-if='results'>
			<div class='row'>
				<div class='col-sm-6 col-md-4' v-for='recipesPart of splitResults'>
					<table class='table table-bordered table-striped table-responsive text-center' id='recipe-book'>
						<colgroup>
							<col span='1' style='width: 64px;'></colgroup>
							<col span='1' style='width: 100%;'></colgroup>
							<col span='1' style='width: 80px;'></colgroup>
						</colgroup>
						<tbody>
							<tr v-for='recipe in recipesPart' :key='recipe.id'>
								<td class='text-left'>
									<img :src='recipe.item.icon' width='48' height='48'>
								</td>
								<td class='text-left'>
									<div style='font-size: 1.1em;' :class='"name rarity-" + recipe.item.rarity'>
										@{{ recipe.item.name }}
									</div>
									<div>
										<img :src='"/img/jobs/" + (jobs[recipe.job_id] ? jobs[recipe.job_id].abbr : "ALL") + "-inactive.png"'>
										@{{ recipe.recipe_level }}
										@{{ "★".repeat(recipe.stars) }}
									</div>
								</td>
								<td class='text-center valign'>
									<button class='btn add-to-list success-after-add' :class="{ 'btn-success': craftingListIds.includes(recipe.item.id), 'btn-default': !craftingListIds.includes(recipe.item.id) }" :data-item-id='recipe.item.id' :data-item-name='recipe.item.name'>
										<i class='glyphicon glyphicon-shopping-cart'></i>
										<i class='glyphicon' :class="{ 'glyphicon-ok': craftingListIds.includes(recipe.item.id), 'glyphicon-plus': !craftingListIds.includes(recipe.item.id) }"></i>
									</button>
								</td>
							</tr>
						</tbody>
						<tfoot>

						</tfoot>
					</table>
				</div>
			</div>
			<nav v-if='this.response.links.prev !== null || this.response.links.next !== null'>
				<ul class="pager">
					<li :class="{ 'invisible': this.response.links.prev === null }"><a href="#" @click.prevent='previousPage()'>Previous</a></li>
					<li>Page @{{ this.response.meta.current_page }}</li>
					<li :class="{ 'invisible': this.response.links.next === null }"><a href="#" @click.prevent='nextPage()'>Next</a></li>
				</ul>
			</nav>
		</div>
	</div>

@stop