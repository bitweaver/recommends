<div class="display recommends">
	<div class="header">
		<h1>{tr}Recommended Content{/tr}</h1>
	</div>

	<div class="body">
		<table class="table data">
			<caption>{tr}List of recommended content{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Title" isort="title"}</th>
				<th>{smartlink ititle="Content Type" isort="content_type_guid"}</th>
				<th>{smartlink ititle="Number of recommendings" isort="update_count"}</th>
				<th>{smartlink ititle="Recommending" isort="sts.recommending" iorder=desc idefault=1}</th>
			</tr>

			{foreach from=$recommendedContent item=item}
				<tr class="{cycle values="odd,even"}">
					<td>{$item.display_link}</td>
					<td>{$item.content_type_guid}</td>
					<td style="text-align:right;">{$item.update_count}</td>
					<td style="text-align:right;">
						<a href="{$smarty.const.RECOMMENDS_PKG_URL}details.php?content_id={$item.content_id}">{$item.recommending}%</a>
					</td>
				</tr>
			{/foreach}
		</table>
		{pagination}
	</div><!-- end .body -->
</div><!-- end .recommends -->
