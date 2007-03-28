{strip}
	<ul>
		{if $gBitUser->hasPermission( 'p_recomends_view')}
			<li>{biticon ipackage="icons" iname="format-justify-fill" iexplain="List Recommendations" iforce="icon"} <a class="item" href="{$smarty.const.RECOMMENDS_PKG_URL}index.php">{tr}Recommendations{/tr}</a></li>
		{/if}
	</ul>
{/strip}
