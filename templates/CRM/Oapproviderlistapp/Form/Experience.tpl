{assign var="groupID" value="10"}
<div class="crm-public-form-item crm-section experience">
  {include file="CRM/UF/Form/Block.tpl" fields=$experience}
</div>
<div id="customData1"></div>
{include file="CRM/Oapproviderlistapp/Form/customData.tpl"}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  CRM.buildCustomData('Individual', 'Provider', 1);
  $('.crm-profile legend').hide();
  $('#_qf_Experience_submit_done-bottom').on('click', function() {
    var msg = {/literal}"{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}An email will be sent to you{/ts}. {ts}It will contain a link that you can click to continue to review this request within the next seven days.{/ts}{/crmScope}"{literal};
    CRM.confirm({message: msg});
  });
});
</script>
{/literal}
