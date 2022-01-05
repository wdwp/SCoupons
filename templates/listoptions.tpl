{if isset($message) && !empty($message)}<p style="color:red;">{$message}</p>{/if}
{if isset($title_pagination_help) && $title_pagination_help!=''}<p>{$title_pagination_help}</p>{/if}
{$startform}
	<div class="pageoverflow">
		<p class="pagetext">{$title_dateformat}:</p>
		<p class="pageinput">{$input_dateformat}</p>
	</div>
	<fieldset>
		<legend>&nbsp;{$title_code_generator}&nbsp;</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_generate}:</p>
			<p class="pageinput">{$input_generate}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_code_length}:</p>
			<p class="pageinput">{$input_code_length}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_code_upper}:</p>
			<p class="pageinput">{$input_code_upper}</p>
		</div>
	</fieldset>
	<div class="pageoverflow">
		<p class="pagetext">{$title_pagelimit}:</p>
		<p class="pageinput">{$input_pagelimit}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}