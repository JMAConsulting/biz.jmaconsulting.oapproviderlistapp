<div class="content description-text">To join the Provider List, you must be a Board Certified Behavior Analyst in good standing, or a registered psychologist or psychological associate in good standing with the College of Psychologists of Ontario.</div>
{assign var="groupID" value="12"}
<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
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

  rearrangeFields();

  $( document ).ajaxComplete(function( event, xhr, settings ) {
    rearrangeFields();
  });

  function rearrangeFields() {
    // Certification Dates
    $('#editrow-custom_8').insertAfter($('input[name="custom_7[1]"]').parent('td'));
    $('#editrow-custom_9').insertAfter($('input[name="custom_7[2]"]').parent('td'));
    $('#editrow-custom_10').insertAfter($('input[name="custom_7[3]"]').parent('td'));
    $('#editrow-custom_11').insertAfter($('input[name="custom_7[4]"]').parent('td'));

    // Certification Numbers
    $('#editrow-custom_40').insertAfter($('#editrow-custom_8'));
    $('#editrow-custom_41').insertAfter($('#editrow-custom_9'));
    $('#editrow-custom_42').insertAfter($('#editrow-custom_10'));
    $('#editrow-custom_43').insertAfter($('#editrow-custom_11'));
  }
});
</script>
{/literal}
