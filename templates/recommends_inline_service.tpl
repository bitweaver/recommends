{strip}
{if $serviceHash.recommends_load}
{assign var=icon_width value=$gBitSystem->getConfig('recommends_icon_width',48)}
{capture assign="icon_size"}
{if $icon_width==36}
-med
{elseif $icon_width==24}
-sml
{/if}
{/capture}
{capture assign="up_icon"}up{$icon_size}{/capture}
{capture assign="down_icon"}down{$icon_size}{/capture}
{capture assign="clear_icon"}clear{$icon_size}{/capture}
{assign var=divid value="recommends-display-`$serviceHash.content_id`"}
{capture assign="upaction"}
{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
href="javascript:void(0);" onclick="javascript:BitAjax.updater($('{$divid}'), '{$smarty.const.RECOMMENDS_PKG_URL}recommend.php', 'content_id={$serviceHash.content_id}&amp;recommends_recommending=1' );"
{else}
href="{$smarty.const.RECOMMENDS_PKG_URL}recommend.php?content_id={$serviceHash.content_id}&amp;recommends_recommending=1"
{/if}
{/capture}
{capture assign="downaction"}
{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
href="javascript:void(0)" onclick="javascript:BitAjax.updater($('{$divid}'), '{$smarty.const.RECOMMENDS_PKG_URL}recommend.php', 'content_id={$serviceHash.content_id}&amp;recommends_recommending=-1' );"
{else}
href="{$smarty.const.RECOMMENDS_PKG_URL}recommend.php?content_id={$serviceHash.content_id}&amp;recommends_recommending=-1"
{/if}
{/capture}
{capture assign="clearaction"}
{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
href="javascript:void(0);" href="javascript:BitAjax.updater($('{$divid}'), '{$smarty.const.RECOMMENDS_PKG_URL}recommend.php', 'content_id={$serviceHash.content_id}&amp;recommends_recommending=0' );"
{else}
href="{$smarty.const.RECOMMENDS_PKG_URL}recommend.php?content_id={$serviceHash.content_id}&amp;recommends_recommending=0"
{/if}
{/capture}
	<div class="recommends-container" id="{$divid}" >				
	{if $gBitUser->isRegistered() && (empty($serviceHash.recommends_changes) || $serviceHash.recommends_changes < $gBitSystem->getConfig('recommends_max_changes', 1)) && (empty($serviceHash.recommends_time) || $serviceHash.recommends_time > $recommends_user_timeout) && (empty($recommends_timeout) || $serviceHash.created > $recommends_timeout) }
		<ul class="recommends-recommending" id="recommends-current" onmouseover="flip('recommends-doit1');flip('recommends-doit3');flip('recommends-doit2');flip('recommends-doit4');" onmouseout="flip('recommends-doit1');flip('recommends-doit2');flip('recommends-doit3');flip('recommends-doit4');">
			<li>
				<a id="recommends-doit1" style="display:none" {$upaction} >{biticon ipackage="recommends" iname=$up_icon iexplain="{tr}I Recommend{/tr}" iforce="icon"}</a>&nbsp;
			</li>
			<li>				
				<span id="recommends-doit3">
				{if empty($serviceHash.recommends_recommending)}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{elseif $serviceHash.recommends_recommending >= $gBitSystem->getConfig('recommends_minimum_recommends')}
					{biticon ipackage="recommends" iname=$up_icon alt="Recommended"}
				{elseif  $serviceHash.recommends_recommending <= -1 * $gBitSystem->getConfig('recommends_minimum_recommends')}
					{biticon ipackage="recommends" iname=$down_icon alt="Dislike"}
				{else}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{/if}
				</span>
				<a {$clearaction} id="recommends-doit4" style="display:none">{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}</a>
				&nbsp;
			</li>
			<li>
				<a id="recommends-doit2" style="display:none" {$downaction} >{biticon ipackage="recommends" iname=$down_icon iexplain="{tr}I Dislike{/tr}" iforce="icon"}</a>&nbsp;
			</li>
		</ul>
		<div id="recommends-errors" class="row small">
			{if !empty($serviceHash.recommends_votes)}
			{$serviceHash.recommends_recommending} {tr}in{/tr} {$serviceHash.recommends_votes} {tr}votes{/tr}
			{/if}
			{if !empty($serviceHash.recommends_user_recommending)}
				<br/>{tr}You{/tr} {if $serviceHash.recommends_user_recommending == 1}{tr}Recommend{/tr}{else}{tr}Dislike{/tr}{/if}
			{/if}
		</div>
	{else}
		<ul class="recommends-recommending" id="recommends-current" onmouseover="showById('recommend-text');" onmouseout="hideById('recommend-text');">
			<li>&nbsp;</li>
			<li>
				{if empty($serviceHash.recommends_recommending)}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{elseif $serviceHash.recommends_recommending >= $gBitSystem->getConfig('recommends_minimum_recommends')}
					{biticon ipackage="recommends" iname=$up_icon alt="Recommended"}
				{elseif  $serviceHash.recommends_recommending <= -1 * $gBitSystem->getConfig('recommends_minimum_recommends')}
					{biticon ipackage="recommends" iname=$down_icon alt="Dislike"}
				{else}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{/if}
			</li>
			<li>&nbsp;</li>
		</ul>
		<div id="recommends-errors" class="row small">
			{if !empty($serviceHash.recommends_votes)}
				{$serviceHash.recommends_recommending} {tr}in{/tr} {$serviceHash.recommends_votes} {tr}votes{/tr}
			{/if}
			{if !empty($serviceHash.recommends_user_recommending)}
				<br/>{tr}You{/tr} {if $serviceHash.recommends_user_recommending == 1}{tr}Recommend{/tr}{else}{tr}Dislike{/tr}{/if}
			{/if}
			<span id="recommend-text" class="warning" style="display:none;">{if $gBitUser->isRegistered()}{tr}You are no longer able to recommend this content.{/tr}{else}<a class="recommends-{$recommend}" href="{$smarty.const.USERS_PKG_URL}login.php">{tr}You need to log in to recommend{/tr}</a>{/if}</span>
		</div>
	{/if}
	{formfeedback hash=$recommendsfeed}
	</div>
{/if}
{/strip}
