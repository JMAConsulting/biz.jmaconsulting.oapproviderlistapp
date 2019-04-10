{* HEADER *}

<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
</div>

<div class="crm-public-form-item crm-section experience">
  {include file="CRM/UF/Form/Block.tpl" fields=$experience}
</div>

<div class="crm-public-form-item crm-section sectorcheck">
  {include file="CRM/UF/Form/Block.tpl" fields=$sectorcheck}
</div>

<div class="crm-public-form-item crm-section insurance">
  {include file="CRM/UF/Form/Block.tpl" fields=$insurance}
</div>

<div class="crm-public-form-item crm-section signature">
  {include file="CRM/UF/Form/Block.tpl" fields=$signature}
</div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();
  var custom_46 = {/literal}'{$file_custom_46}'{literal};
  $('#editrow-custom_46 .content .crm-frozen-field').val($('#editrow-custom_46 .content .crm-frozen-field').html(custom_46));
});
</script>
{/literal}
