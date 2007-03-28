{assign var=icon_width value=$gBitSystem->getConfig('recommends_icon_width',48)}
{capture assign=recommends_width}{math equation="3*x" x=$icon_width}{/capture}
.recommends-recommending	{ldelim}line-height:1px; list-style:none; padding:0px; position:relative; width:{$recommends_width}px; margin:0 auto;{rdelim}
.recommends-recommending li	{ldelim}list-style:none; padding:0px; margin:0px; /*\*/ float:left; /* */ width:{$icon_width}px; {rdelim}
div.recommends-container {ldelim}margin:0 auto; text-align: center; width:{$recommends_width}px; {rdelim}
