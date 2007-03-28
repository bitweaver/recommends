{strip}
<div class="floaticon">{bithelp}</div>
<div class="listing recommends">
	<div class="header">
		<h1>{tr}Recommends{/tr}</h1>
	</div>
	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="data">
				<tr>
					<th>{smartlink ititle="Title" isort=title offset=$control.offset iorder=desc idefault=0}</th>
					<th>{smartlink ititle="Recommendation Level" isort=recommending offset=$control.offset iorder=desc idefault=1}</th>
					<th>{smartlink ititle="Created" isort=created offset=$control.offset iorder=desc idefault=1}</th>
				</tr>

				{foreach from=$recommendedContent item=content}
					<tr class="{cycle values="even,odd"}">
						<td><a href="{$content.display_url}" title="{$content.title|escape}">{$content.title|escape}</a></td>
						<td>{$content.recommending} in {$content.votes} votes</td>
						<td>{$content.created|bit_short_date}</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">
						{tr}No records found{/tr}
					</td></tr>
				{/foreach}
			</table>
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
