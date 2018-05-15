<table>
<tr class="crm-price-field-form-block-depends_on_pfids">
	<td class="label">{$form.depends_on_pfids.label}</td>
  <td>&nbsp;{$form.depends_on_pfids.html}</td>
</tr>
</table>
<script type="text/javascript">
{literal}
(function($) {
  cj("tr.crm-price-field-form-block-depends_on_pfids").insertAfter(cj("tr.crm-price-field-form-block-is_required"));
})(CRM.$ || cj);
{/literal}
</script>
