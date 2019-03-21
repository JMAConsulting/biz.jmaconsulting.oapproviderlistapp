<div id="first-education">
  <fieldset><legend>Post Secondary Education</legend></fieldset>
  <div class="content-description">Please provide information on all of your relevant post-secondary degrees (college, university). There are no specific educational requirements to join the OAP Provider List.</div><br/>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$educationField.0.label} {$form.$educationField.0.html}</div>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$degreeField.0.label} {$form.$degreeField.0.html}</div>
  <div class="crm-section education-field" style="float:left">{$form.$yearField.0.label} {$form.$yearField.0.html}</div>
  <div class="clear"></div>
</div>
<div id="first-employer">
  <fieldset><legend></legend></fieldset>
  <div class="content-description">Please provide information on your employment history to demonstrate that you meet the experience requirements. Please focus on your most recent employment history. The Provider List may contact your references for further information.</div><br/>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$orgField.0.label} {$form.$orgField.0.html}</div>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$titleField.0.label} {$form.$titleField.0.html}</div>
  <div class="crm-section education-field" style="float:left">{$form.$datesField.0.label} {$form.$datesField.0.html}</div>
  <div class="clear"></div>
  <div class="crm-section education-field">{$form.$tasksField.0.label} {$form.$tasksField.0.html}</div>
  <div class="clear"></div>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$hoursField.0.label} {$form.$hoursField.0.html}</div>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$superHoursField.0.label} {$form.$superHoursField.0.html}</div>
  <div class="clear"></div>
  <div class="crm-section education-field">{$form.$superContactField.0.label} {$form.$superContactField.0.html}</div>
  <div class="clear"></div>
</div>
<div id="education">
{section name='i' start=1 loop=6}
    {assign var='rowNumber' value=$smarty.section.i.index}
    <div id="add-education-item-row-{$rowNumber}" class="education-row hiddenElement">
      <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$educationField.$rowNumber.label} {$form.$educationField.$rowNumber.html}</div>
      <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$degreeField.$rowNumber.label} {$form.$degreeField.$rowNumber.html}</div>
      <div class="crm-section education-field">{$form.$yearField.$rowNumber.label} {$form.$yearField.$rowNumber.html}</div>
      <span><a href=# class="remove_item crm-hover-button" title='Remove Degree'><i class="crm-i fa-times"></i></a></span>
      <div class="clear"></div>
    </div>
{/section}
</div>
<div id="employer">
{section name='i' start=1 loop=6}
    {assign var='rowNumber' value=$smarty.section.i.index}
    <div id="add-employer-item-row-{$rowNumber}" class="employer-row hiddenElement">
      <fieldset><legend></legend></fieldset>
      <div class="content-description">Please provide information on your employment history to demonstrate that you meet the experience requirements. Please focus on your most recent employment history. The Provide$
      <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$orgField.0.label} {$form.$orgField.0.html}</div>
      <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$titleField.0.label} {$form.$titleField.0.html}</div>
      <div class="crm-section education-field" style="float:left">{$form.$datesField.0.label} {$form.$datesField.0.html}</div>
      <div class="clear"></div>
      <div class="crm-section education-field">{$form.$tasksField.0.label} {$form.$tasksField.0.html}</div>
      <div class="clear"></div>
      <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$hoursField.0.label} {$form.$hoursField.0.html}</div>
      <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$superHoursField.0.label} {$form.$superHoursField.0.html}</div>
      <div class="clear"></div>
      <div class="crm-section education-field">{$form.$superContactField.0.label} {$form.$superContactField.0.html}</div>
      <span><a href=# class="remove_employer_item crm-hover-button" title='Remove Employer'><i class="crm-i fa-times"></i></a></span>
      <div class="clear"></div>
</div>
<span id="add-another-item" class="crm-hover-button" style="font-weight:bold;padding:10px;"><a href=#>{ts}ADD ANOTHER DEGREE{/ts}</a></span>
<span id="add-another-employer" class="crm-hover-button" style="font-weight:bold;padding:10px;"><a href=#>{ts}ADD ANOTHER EMPLOYER{/ts}</a></span>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  var education = '{/literal}{$educationField}{literal}';
  var degree = '{/literal}{$degreeField}{literal}';
  var year = '{/literal}{$yearField}{literal}';

  var org = '{/literal}{$orgField}{literal}';
  var title = '{/literal}{$titleField}{literal}';
  var dates = '{/literal}{$datesField}{literal}';
  var tasks = '{/literal}{$tasksField}{literal}';
  var hours = '{/literal}{$hoursField}{literal}';
  var superHours = '{/literal}{$superHoursField}{literal}';
  var superContact = '{/literal}{$superContactField}{literal}';

  // Education
  $('#add-another-item').insertAfter($('#editrow-email-Primary'));
  $('#education').insertAfter($('#editrow-email-Primary'));
  $('#first-education').insertAfter($('#editrow-email-Primary'));

  // Employer History
  $('#add-another-employer').insertAfter($('#editrow-custom_12'));
  $('#employer').insertAfter($('#editrow-custom_12'));
  $('#first-employer').insertAfter($('#editrow-custom_12'));

  // Certification Dates
  $('#editrow-custom_8').insertAfter($('input[name="custom_7[1]"]').parent('td'));
  $('#editrow-custom_9').insertAfter($('input[name="custom_7[2]"]').parent('td'));
  $('#editrow-custom_10').insertAfter($('input[name="custom_7[3]"]').parent('td'));
  $('#editrow-custom_11').insertAfter($('input[name="custom_7[4]"]').parent('td'));
  //var submittedRows = $.parseJSON('{/literal}{$childSubmitted}{literal}');

  // after form rule validation when page reloads then show only those line-item which were chosen and hide others
  //$.each(submittedRows, function(e, num) {
  //  isSubmitted = true;
  //  $('#add-education-item-row-' + num).removeClass('hiddenElement');
  //});

  $('#add-another-item').on('click', function(e) {
    e.preventDefault();
    var hasHidden = $('div.education-row').hasClass("hiddenElement");
    if (hasHidden) {
      var row = $('#education div.hiddenElement:first');
      $('div.hiddenElement:first, #education').fadeIn("slow").removeClass('hiddenElement');
      hasHidden = $('div.education-row').hasClass("hiddenElement");
    }
    $('#add-another-item').toggle(hasHidden);
  });

  $('#add-another-employer').on('click', function(e) {
    e.preventDefault();
    var hasHidden = $('div.employer-row').hasClass("hiddenElement");
    if (hasHidden) {
      var row = $('#employer div.hiddenElement:first');
      $('div.hiddenElement:first, #employer').fadeIn("slow").removeClass('hiddenElement');
      hasHidden = $('div.employer-row').hasClass("hiddenElement");
    }
    $('#add-another-employer').toggle(hasHidden);
  });

  $('.remove_item').on('click', function(e) {
    e.preventDefault();
    var row = $(this).closest('div.education-row');
    $('#add-another-item').show();
    $('input[id^="' + education + '"]', row).val('');
    $('input[id^="' + degree + '"]', row).val('');
    $('input[id^="' + year + '"]', row).val('');
    row.addClass('hiddenElement').fadeOut("slow");
  });

  $('.remove_employer').on('click', function(e) {
    e.preventDefault();
    var row = $(this).closest('div.employer-row');
    $('#add-another-employer').show();
    $('input[id^="' + org + '"]', row).val('');
    $('input[id^="' + title + '"]', row).val('');
    $('input[id^="' + dates + '"]', row).val('');
    $('input[id^="' + tasks + '"]', row).val('');
    $('input[id^="' + hours + '"]', row).val('');
    $('input[id^="' + superHours + '"]', row).val('');
    $('input[id^="' + superContact + '"]', row).val('');
    row.addClass('hiddenElement').fadeOut("slow");
  });


});
</script>
{/literal}
