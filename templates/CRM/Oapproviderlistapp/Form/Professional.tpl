<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
</div>
{include file="CRM/common/customDataBlock.tpl"}
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();
});
</script>
{/literal}
