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
{capture assign="upurl"}
{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
javascript:ajax_updater('{$divid}', '{$smarty.const.RECOMMENDS_PKG_URL}recommend.php', 'content_id={$serviceHash.content_id}&amp;recommends_recommending=1' );
{else}
{$smarty.const.RECOMMENDS_PKG_URL}recommend.php?content_id={$serviceHash.content_id}&amp;recommends_recommending=1
{/if}
{/capture}
{capture assign="downurl"}
{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
javascript:ajax_updater('{$div_id}', '{$smarty.const.RECOMMENDS_PKG_URL}recommend.php', 'content_id={$serviceHash.content_id}&amp;recommends_recommending=-1' );
{else}
{$smarty.const.RECOMMENDS_PKG_URL}recommend.php?content_id={$serviceHash.content_id}&amp;recommends_recommending=-1
{/if}
{/capture}
{capture assign="clearurl"}
{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
javascript:ajax_updater('{$div_id}', '{$smarty.const.RECOMMENDS_PKG_URL}recommend.php', 'content_id={$serviceHash.content_id}&amp;recommends_recommending=0' );
{else}
{$smarty.const.RECOMMENDS_PKG_URL}recommend.php?content_id={$serviceHash.content_id}&amp;recommends_recommending=0
{/if}
{/capture}
	{if $gBitSystem->isFeatureActive( 'recommends_use_ajax' )}
		<script type="text/javascript">/*<![CDATA[*/ show_spinner('spinner'); /*]]>*/</script>
	{/if}
	<div class="recommends-container" id="recommends-{$serviceHash.content_id}" >				
	{assign var="datetime" value=$gBitSystem->getUTCTime()}
	{debug}
	{if $gBitUser->isRegistered() && $serviceHash.changes < $gBitSystem->getConfig('recommends_max_changes', 1) && $serviceHash.created < $datetime - ($gBitSystem->getConfig('recommends_recommend_period', 15) * 24 * 60 * 60) && $serviceHash.recommends_time < $datetime - ($gBitSystem->getConfig('recommends_change_timeout', 1) * 60)}
		<ul class="recommends-recommending" id="recommends-current" onmouseover="flip('recommends-doit1');flip('recommends-doit3');flip('recommends-doit2');flip('recommends-doit4');" onmouseout="flip('recommends-doit1');flip('recommends-doit2');flip('recommends-doit3');flip('recommends-doit4');">
			<li>
				<a id="recommends-doit1" style="display:none" href="{$upurl}" >{biticon ipackage="recommends" iname=$up_icon iexplain="{tr}I Recommend{/tr}" iforce="icon"}</a>&nbsp;
			</li>
			<li>				
				<span id="recommends-doit3">
				{if empty($serviceHash.recommends_recommending)}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{elseif $serviceHash.recommends_recommending >= $gBitSystem->getConfig('recommends_minimum_recommends')}
					{biticon ipackage="recommends" iname=$up_icon alt="Recommended"}
				{elseif  $serviceHash.recommends_recommending <= -1 * $gBitSystem->getConfig('recommends_minimum_recommends')}}
					{biticon ipackage="recommends" iname=$down_icon alt="Dislike"}
				{else}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{/if}
				</span>
				<a href="{$clearurl}" id="recommends-doit4" style="display:none">{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}</a>
				&nbsp;
			</li>
			<li>
				<a id="recommends-doit2" style="display:none" href="{$downurl}" >{biticon ipackage="recommends" iname=$down_icon iexplain="{tr}I Dislike{/tr}" iforce="icon"}</a>&nbsp;
			</li>
		</ul>
		<div id="{$divid}" class="row small">
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
				{elseif  $serviceHash.recommends_recommending <= -1 * $gBitSystem->getConfig('recommends_minimum_recommends')}}
					{biticon ipackage="recommends" iname=$down_icon alt="Dislike"}
				{else}
					{biticon ipackage="recommends" iname=$clear_icon alt="Needs Recommendations"}
				{/if}
			</li>
			<li>&nbsp;</li>
		</ul>
		<div id="{$divid}" class="row small warning">
			{if !empty($serviceHash.recommends_votes)}
				{$serviceHash.recommends_recommending} {tr}in{/tr} {$serviceHash.recommends_votes} {tr}votes{/tr}
			{/if}
			<span id="recommend-text" style="display:none;">{if $gBitUser->isRegistered()}{tr}You are no longer able to recommend this content.{/tr}{else}<a class="recommends-{$recommend}" href="{$smarty.const.USERS_PKG_URL}login.php">{tr}You need to log in to recommend{/tr}</a>{/if}</span>
		</div>
	{/if}
	{formfeedback hash=$recommendsfeed}
	</div>
{/if}
{/strip}
