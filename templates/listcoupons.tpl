{if isset($message) && !empty($message)}<p style="color:red;">{$message}</p>{/if}
<fieldset>
	<legend>&nbsp;{$title_coupon_filter}&nbsp;</legend>
	{$formstart}
		<div class="pageoverflow">
			<p class="pageinput">{$title_description}&nbsp;</p>
			<p class="pageinput">{$input_description}&nbsp;
			{$submitcouponfilter}&nbsp;
			{* $submitcouponreset *}{$hidden}</p>
		</div>
	{$formend}
</fieldset>
<div class="pageoptions"><p class="pageoptions">{$addcouponlink}</p></div>
{if $itemcount > 0}
<table cellspacing="0" class="pagetable cms_sortable tablesorter">
	<thead>
		<tr>
			<th>{$title_description}</th>
			<th>{$title_coupon_code}</th>
			<th>{$title_start_date}</th>
			<th>{$title_end_date}</th>
			<th>{$title_value}</th>
			<th data-sorter="false">{$title_status}</th>
			<th data-sorter="false" class="pageicon">&nbsp;</th>
			<th data-sorter="false" class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->description}</td>
			<td>{$entry->coupon_code}</td>
			<td>{$entry->start_date|date_format:$smarty_date}</td>
			<td>{$entry->end_date|date_format:$smarty_date}</td>
			<td>{$entry->value}</td>
			<td>{$entry->statuslink}</td>
			<td>{$entry->editlink}</td>
			<td>{$entry->deletelink}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{if $pagecount > 1}
	<p>
	{if $pagenumber > 1}
		{$firstpage}&nbsp;{$prevpage}&nbsp;
	{/if}
	{$pagename}&nbsp;{$pagenumber}&nbsp;{$oftext}&nbsp;{$pagecount}
	{if $pagenumber < $pagecount}
		&nbsp;{$nextpage}&nbsp;{$lastpage}
	{/if}
	</p>
{/if}
{/if}

<div class="pageoptions"><p class="pageoptions">{$hidden}{$addcouponlink}</p></div>