{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<div class="content description-text">
  {$descriptionText}
</div>
<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
</div>
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
<div id="application-dialog">
  <p>{ts}As a psychologist or psychological associate, you will need to a BCBA-D provide an "Applied Behaviour Analysis Expertise Package", which will confirm your ABA expertise.{/ts} <a href="https://oapproviderlist.ca/sites/default/files/2020-01/OAP%20-%20Reg%20Psychologist%20ABA%20Expertise%20Package%20V3.pdf">{ts}Click here for details of what that package must include.{/ts}</a></p>
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();

  rearrangeFields();

  $( document ).ajaxComplete(function( event, xhr, settings ) {
    rearrangeFields();
  });

  var disableTab = '{/literal}{$disableTab}{literal}';
  if (disableTab == 1) {
    $('#mainTabContainer ul li a').not($('#mainTabContainer ul li.ui-tabs-active')).removeAttr('class');
  }

  $('#application-dialog').dialog({
    autoOpen: false,
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
      "Ok": function() {
        $(this).dialog('close');
      },
    },
  });

  $('#custom_7_3').on('click', function() {
    if ($(this).prop('checked') && !$('#custom_7_1').prop('checked') && !$('#custom_7_2').prop('checked')) {
      $('#application-dialog').dialog('open');
    }
  });

  $('#custom_7_4').on('click', function() {
    if ($(this).prop('checked') && !$('#custom_7_1').prop('checked') && !$('#custom_7_2').prop('checked')) {
      $('#application-dialog').dialog('open');
    }
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

  $('#_qf_Professional_submit_done-bottom').on('click', function() {
    var msg = {/literal}"{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}An email will be sent to you{/ts}. {ts}It will contain a link that you can click to continue to review this request within the next seven days.{/ts}{/crmScope}"{literal};
    CRM.alert(msg);
  });
});
</script>
{/literal}
{/crmScope}
