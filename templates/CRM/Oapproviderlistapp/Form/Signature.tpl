<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$signature}
</div>
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
