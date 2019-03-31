<div class="content description-text">
  {crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
  <p>
  {ts}To join the Provider List, you must be a Board Certified Behavior Analyst® in good standing, or a registered psychologist or psychological associate in good standing with the College of Psychologists of Ontario.{/ts}  
  </p>
  <p>
  {ts}If you are a psychologist or psychological associate with ABA expertise, you are eligible to join the OAP Provider List. You do not need to obtain a BCBA® or BCBA-D™, however, you will need to have your ABA expertise confirmed by a BCBA-D™. Upon receiving your application package, the Provider List administrator will contact you with more information about the equivalency process for confirming ABA expertise.{/ts}
  </p>
  {/crmScope}
  </div>
{assign var="groupID" value="12"}
<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
</div>
<div class="content description-text">
<p>
{ts}Please note any other relevant credentials you have achieved and the date you obtained them.{/ts}
</p>
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
