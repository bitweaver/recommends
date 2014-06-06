{strip}
{formfeedback hash=$feedback}
{form}
	{jstabs}

		{jstab title="Generic Settings"}
		{legend legend="Generic Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{foreach from=$formRecommendsOptions key=item item=output}
				<div class="form-group">
					{formlabel label=$output.label for=$item}
					{forminput}
						{if $output.type == 'numeric'}
							{html_options name="$item" values=$numbers output=$numbers selected=$gBitSystem->getConfig($item) labels=false id=$item}
						{elseif $output.type == 'input'}
							<input type='text' name="{$item}" id="{$item}" value="{$gBitSystem->getConfig($item)}" />
						{else}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
						{/if}
						{formhelp note=$output.note page=$output.page}
					{/forminput}
				</div>
			{/foreach}
	
			<div class="form-group">
				{formlabel label="Icon Size"}
				{forminput}
					{html_options name="recommends_icon_width" options=$recommendsIconSizes selected=$gBitSystem->getConfig('recommends_icon_width')}
					{formhelp note="Please select the size of icons you would like to use.."}
				{/forminput}
			</div>
	
			<div class="form-group">
				{formlabel label="Recommendable Content"}
				{forminput}
					{html_checkboxes options=$formRecommendable.guids value=y name=recommendable_content separator="<br />" checked=$formRecommendable.checked}
					{formhelp note="Here you can select what content can be recommended."}
				{/forminput}
			</div>
		{/legend}
		{/jstab}

		{jstab title="Display Settings"}
		{legend legend="Display Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{foreach from=$formRecommendsDisplayOptions key=item item=output}
				<div class="form-group">
					{formlabel label=$output.label for=$item}
					{forminput}
						{if $output.type == 'numeric'}
							{html_options name="$item" values=$numbers output=$numbers selected=$gBitSystem->getConfig($item) labels=false id=$item}
						{elseif $output.type == 'input'}
							<input type='text' name="{$item}" id="{$item}" value="{$gBitSystem->getConfig($item)}" />
						{else}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
						{/if}
						{formhelp note=$output.note page=$output.page}
					{/forminput}
				</div>
			{/foreach}
		{/legend}
		{/jstab}
	{/jstabs}

	<div class="form-group submit">
		<input type="submit" class="btn btn-default" name="recommends_preferences" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{smartlink ititle="View a list of recommended content" ipackage=recommends ifile="index.php"}
{/strip}
