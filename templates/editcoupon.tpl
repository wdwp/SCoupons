{literal}
<script type="text/javascript">
	$(document).ready(function() {
		$(function() {$("div.datepicker input").datepicker(
			{dateFormat: "{/literal}{$dateformat}{literal}"
			});
		});
	});
</script>
{/literal}
{$startform}

<div class="pageoverflow">
	<p class="pagetext">*{$title_coupon_code}:</p>
	<p class="pageinput">{$input_coupon_code}</p>
</div>

<div class='pageoverflow'>
	<p class='pagetext'>{$title_description}:</p>
	<p class='pageinput'>{$input_description}</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_start_date}:</p>
	<div class="pageinput datepicker">{$input_start_date}</div>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_end_date}:</p>
	<div class="pageinput datepicker">{$input_end_date}</div>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_type}:</p>
	<p class='pageinput'>{$input_type}</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_value}:</p>
	<p class='pageinput'>{$input_value}</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_order_minimum}:</p>
	<p class='pageinput'>{$input_order_minimum}</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_code_redemptions_max}:</p>
	<p class='pageinput'>{$input_code_redemptions_max}
	{if isset($input_code_redemptions_used ) && $input_code_redemptions_used > 0}
		&nbsp;{$title_code_redemptions_used}:&nbsp;{$input_code_redemptions_used}
	{/if}
	</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_user_redemptions_max}:</p>
	<p class='pageinput'>{$input_user_redemptions_max}</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$title_status}:</p>
	<p class='pageinput'>{$input_status}</p>
</div>

<div class="pageoverflow">
	<p class="pagetext"> </p>
	<p class="pageinput">{if isset($hidden)}{$hidden}{/if}{$submit}{$cancel}</p>
</div>
{$endform}
