@extends('app')

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
			cursor: pointer;
		}
		.class-selector.active {
			border: 1px solid #5ab65a;
		}

		[v-cloak] {
			display: none !important;
		}
		html #banner h2 {
			margin-bottom: 0;
		}
		html #content {
			padding-top: 0;
		}

		@media (min-width: 992px) {
			#levesBox {
				display: flex;
			}

			.search {
				width: 212px;
				margin-right: 20px;
			}

			.results {
				flex: 1;
			}
		}
	</style>
@endsection

@section('javascript')
	<script src='https://cdnjs.cloudflare.com/ajax/libs/vue/3.0.2/vue.global.prod.js' integrity='sha512-M8VjsuCj1iBzrwKloFNjvQBmFXT2oF0MWExoLGpQT2nEx5tq7CP+BhWGJdczT1LoWAhyqHh+LJ6ihHSVGyclHw==' crossorigin='anonymous'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js' integrity='sha512-DZqqY3PiOvTP9HkjIWgjO6ouCbq+dxqWoJZ/Q+zPYNHmlnI2dQnbJ5bxAHpAMw+LXRm4D72EIRXzvcHQtE8/VQ==' crossorigin='anonymous'></script>

	<script>
		const craftingListIds = @json(array_keys(session('list', [])));
		const maxLevel = {{ config('site.max_level') }};
	</script>

	<script src='{{ cdn('/js/pages/leves.js') }}'></script>
@endsection

@section('banner')
	{{-- <a href='/levequests/advanced' class='btn btn-primary pull-right'>Advanced Tool <i class='glyphicon glyphicon-arrow-right'></i></a> --}}
	<h1>Levequests</h1>
	<h2>
		@php
			$taglines = [
				'Get the most out of your daily Allowances!',
				'Don\'t forget about your Scrips!',
				'Custom Deliveries nets you Custom Experiences!',
				'Screw it, you\'re just going to make Cookies anyway&hellip;',
			];
			$tagline = $taglines[array_rand($taglines)];
		@endphp
		{!! $tagline !!}
	</h2>
@endsection

@section('content')

<div id='levesBox' v-cloak>
	<div class='search'>
		<div class='well' v-if='jobs != null && levels != null'>
			<h5 style='margin-top: 0;'>Job</h5>
			<div>
				<label class='class-selector' v-for='j in jobs' :key='j.id' :class="j.id == activeJob ? 'active' : ''">
					<input type='checkbox' @click='updateJob(j.id)'>
					<img :src='"/img/jobs/" + j?.abbr.toUpperCase() + "-inactive.png"' alt='' width="24" height="24"> <span class="abbr hidden-xs hidden-sm" v-html='j?.abbr' style='width: 32px; display: inline-block;'></span>
				</label>
			</div>

			<h5>Level</h5>
			<div>
				<label class='class-selector' v-for='l in levels' :key='l' :class="l == activeLevel ? 'active' : ''">
					<input type='checkbox' checked='checked' @click='updateLevel(l)'>
					<span v-html='l'></span>
				</label>
			</div>

			<h5>Options</h5>
			<label>
				<input type='checkbox' v-model='hq' style='position: relative; top: 1px; margin-right: 2px;'>
				HQ Turnins
			</label>
		</div>
	</div>
	<div class='results'>
		<fieldset v-if='results != null'>
			<table class='table table-bordered table-striped table-responsive text-center'>
				<colgroup>
					<col span='1' style='width: 60px;'></colgroup>
					<col span='1' style='width: 60%;'></colgroup>
					<col span='1' style='width: 64px;'></colgroup>
					<col span='1' style='width: 40%;'></colgroup>
					<col span='1' style='width: 80px;'></colgroup>
				</colgroup>
				<tbody>
					<tr v-for='leve in results' :key='leve.id'>
						<td style='padding: 2px;'>
							<div style='position: relative; overflow: hidden; width: 37px; opacity: 1;'>
								<img :src='leve.frame' width='37' height='58' style='position: absolute;'>
								<img :src='leve.plate' width='37' height='58'>
							</div>
						</td>
						<td class='text-left'>
							<div class='pull-right text-right'>
								<div>
									<span v-html='new Intl.NumberFormat().format(leve.xp * (hq ? 2 : 1))'></span>
									<img src='/img/xp.png' width='20' height='20' style='vertical-align: bottom; margin-left: 4px;'>
								</div>
								<div style='margin-top: 4px;'>
									<span v-html='new Intl.NumberFormat().format(leve.gil * (hq ? 2 : 1))'></span>
									<img src='/img/coin.png' width='20' height='20' style='vertical-align: bottom; margin-left: 4px;'>
								</div>
							</div>
							<div style='font-size: 1.2em;' class='name'>
								<img :src='"/img/leve_icon" + (leve.repeats ? "_red" : "") + ".png"' width='20' height='20' style='vertical-align: top;'>
								<a :href='"/levequests/breakdown/" + leve.id' v-html='leve.name'></a>
							</div>
							<div style='margin-top: 5px;'>
								<span v-html='leve.location?.name'></span>
							</div>
						</td>
						<td>
							<div style='position: relative;'>
								<img src='/img/hq-overlay.png' v-if='hq' width='48' height='48' style='position: absolute;'>
								<img :src='leve.recipe.item.icon' width='48' height='48'>
							</div>
						</td>
						<td class='text-left'>
							<div style='font-size: 1.1em;' :class='"name rarity-" + leve.recipe.item.rarity' v-html='leve.recipe.item.name'></div>
							<div>
								<img :src='"/img/jobs/" + jobs[leve.recipe.job_id]?.abbr.toUpperCase() + "-inactive.png"'width='20' height='20' style='vertical-align: bottom;'>
								<span class='rlvl' v-html='leve.recipe.recipe_level'></span>
								<span v-html='"★".repeat(leve.recipe.stars)'></span>
							</div>
						</td>
						<td class='text-center valign'>
							<button class='btn add-to-list success-after-add' :class="{ 'btn-success': craftingListIds.includes(leve.recipe.item.id), 'btn-default': !craftingListIds.includes(leve.recipe.item.id) }" :data-item-id='leve.recipe.item.id' :data-item-name='leve.recipe.item.name' :data-item-quantity='leve.quantity'>
								<i class='glyphicon glyphicon-shopping-cart'></i>
								<i class='glyphicon' :class="{ 'glyphicon-ok': craftingListIds.includes(leve.recipe.item.id), 'glyphicon-plus': !craftingListIds.includes(leve.recipe.item.id) }"></i>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
</div>

@endsection
