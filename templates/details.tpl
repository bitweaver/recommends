<div class="display recommends">
	<div class="header">
		<h1>{tr}Recommends Details{/tr}</h1>
	</div>

	<div class="body">
		{legend legend="Recommends Details"}
			{if $recommendsDetails}
				<div class="control-group">
					{formlabel label="Title"}
					{forminput}
					<a href="{$recommendsDetails.display_url}">{$recommendsDetails.title|escape}</a> <small>({$gLibertySystem->getContentTypeName($recommendsDetails.content_type_guid)})</small>
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Creator"}
					{forminput}
						{displayname real_name=$recommendsDetails.creator_real_name login=$recommendsDetails.creator_user}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Last Editor"}
					{forminput}
						{displayname real_name=$recommendsDetails.modifier_real_name login=$recommendsDetails.modifier_user}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Recommendation Level"}
					{forminput}
						{$recommendsDetails.recommends_recommending}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Number of recommends"}
					{forminput}
						{$recommendsDetails.recommends_votes}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Users who have recommended"}
					{forminput}
						<ul class="data">
							{foreach from=$recommendsDetails.user_recommendings item=user}
								<li class="item {cycle values="odd,even"}">
									<a href="{$smarty.const.RECOMMENDS_PKG_URL}details.php?user_id={$user.user_id}" title="User Recommendations">{displayname hash=$user nolink=1}</a> &bull; <em>{if $user.recommending == 1}Recommended{else}Recommended Avoid{/if}</em>
								</li>
							{/foreach}
						</ul>
					{/forminput}
				</div>
			{elseif $userRecommendings}
				{include file="bitpackage:recommends/user_recommendings.tpl"}

				<div class="control-group">
					{formlabel label="Individual Recommendings" for=""}
					{forminput}
						<ul class="data">
							{foreach from=$userRecommendings item=recommending}
								<li class="item {cycle values="odd,even"}">
									{$recommending.display_link} &bull; <em>{if $recommending.user_recommending == 1}Recommended{else}Recommended Avoid{/if}</em>
								</li>
							{/foreach}
						</ul>
					{/forminput}
				</div>
			{/if}
		{/legend}
	</div><!-- end .body -->
</div><!-- end .recommends -->
