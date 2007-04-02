{strip}
{if $recommendsPackageActive}
	{bitmodule title="$moduleTitle" name="last_recommended"}
		<ul class="recommends">
			{section name=ix loop=$modLastRecommends}
				<li class="{cycle values="odd,even"}">
					<div class="title">{$modLastRecommends[ix].title}</div>			
					<div class="date">{$modLastRecommends[ix].created|bit_long_date}
					<br />
					by {displayname hash=$modLastRecommends[ix]}</div>
					<a href="{$modLastRecommends[ix].post_url}">Read more</a>
				</li>
			{sectionelse}
				<li></li>
			{/section}
		</ul>
	{/bitmodule}
{/if}
{/strip}
