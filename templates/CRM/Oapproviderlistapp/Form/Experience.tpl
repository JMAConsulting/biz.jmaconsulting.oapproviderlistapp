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
  $('tr.custom_48_25-row-help-pre').insertAfter($('tr.custom_48_25-row'));
  var empFields = [
    "custom_32",
    "custom_33",
    "custom_47",
    "custom_48",
    "custom_35",
    "custom_36",
    "custom_37",
    "custom_38",
  ];
  $( document ).ajaxComplete(function() {
    $.each(empFields, function(key, value){
      $("label[for^='"+value+"']").append("&nbsp;<span class='crm-marker' title='This field is required.'>*</span>");
    });
  });

  $('#_qf_Experience_submit_done-bottom').on('click', function() {
    var msg = {/literal}"{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}An email will be sent to you{/ts}. {ts}It will contain a link that you can click to continue to review this request within the next seven days.{/ts}{/crmScope}"{literal};
    CRM.alert(msg);
  });
});
</script>
{/literal}
