{strip}
<div class="floaticon">{bithelp}</div>
<div class="listing recommends">
	<div class="header">
		<h1>{tr}Recommends{/tr}</h1>
	</div>
	<div class="body">
	{form legend="Select Content Type"}
		<div class="row">
			{formlabel label="Restrict listing" for="content_type"}
			{forminput}
				{html_options onchange="submit();" options=$contentTypes name=content_type id=content_type selected=$contentSelect}
				<noscript>
					<div><input type="submit" name="content_switch" value="{tr}change content type{/tr}" /></div>
				</noscript>
			{/forminput}

			{forminput}
				<input type="text" name="find" value="{$listInfo.find}" />
				<input type="submit" value="{tr}Apply Filter{/tr}" name="search_objects" />
				{formhelp note="You can restrict the content listing to a given content type or apply a filter."}
			{/forminput}
		</div>
	{/form}
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
						<td><a href="{$smarty.const.RECOMMENDS_PKG_URL}details.php?content_id={$content.content_id}" title="{tr}Details{/tr}">{$content.recommending} in {$content.votes} votes</a></td>
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
