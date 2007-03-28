{strip}
{if $average_pixels}
	<div class="row">
		{formlabel label="Average Rating" for=""}
		{forminput}
			<div class="stars-container">
				<ul class="stars-rating" >
					<li class="stars-current" style="width:{$average_pixels|default:0}px;"></li>
				</ul>
			</div>
			{if $gQueryUser}
				<a href="{$smarty.const.STARS_PKG_URL}details.php?user_id={$gQueryUser->mUserId}">{tr}Individual ratings{/tr}</a>
			{/if}
		{/forminput}
	</div>
{/if}
{/strip}
