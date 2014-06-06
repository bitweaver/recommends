{strip}
{if $userAverage}
	<div class="form-group">
		{formlabel label="Recommendation Percentage" for=""}
		{forminput}
			{$userAverage}% Recommends
			{if $gQueryUser}
				<a href="{$smarty.const.RECOMMENDS_PKG_URL}details.php?user_id={$gQueryUser->mUserId}">{tr}Individual recommendings{/tr}</a>
			{/if}
		{/forminput}
	</div>
{/if}
{/strip}
