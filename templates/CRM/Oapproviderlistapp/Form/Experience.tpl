{assign var="groupID" value="10"}
<div class="crm-public-form-item crm-section experience">
  {include file="CRM/UF/Form/Block.tpl" fields=$experience}
</div>
<div id="customData"></div>
{include file="CRM/common/customData.tpl"}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  CRM.buildCustomData('Individual', 'Provider');
  $('.crm-profile legend').hide();
});
</script>
{/literal}
