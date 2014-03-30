<th {{ isset($original_level) && ($original_level > $level || ( ! $slim_mode && $original_level + 3 == $level)) ? 'class="hidden" ' : '' }}data-level='{{ $level }}'>
	<div class='stats-box row'>
	</div>
</th>