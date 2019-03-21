<div id="first-education">
  <fieldset><legend>Post Secondary Education</legend></fieldset>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$educationField.0.label} {$form.$educationField.0.html}</div>
  <div class="crm-section education-field" style="float:left;padding-right:20px">{$form.$degreeField.0.label} {$form.$degreeField.0.html}</div>
  <div class="crm-section education-field" style="float:left">{$form.$yearField.0.label} {$form.$yearField.0.html}</div>
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
<span id="add-another-item" class="crm-hover-button" style="font-weight:bold;padding:10px;"><a href=#>{ts}Add another degree{/ts}</a></span>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  var education = '{/literal}{$education}{literal}';
  var degree = '{/literal}{$degree}{literal}';
  var year = '{/literal}{$year}{literal}';

  $('#add-another-item').insertAfter($('#editrow-postal_code-Primary'));
  $('#education').insertAfter($('#editrow-postal_code-Primary'));
  $('#first-education').insertAfter($('#editrow-postal_code-Primary'));

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

  $('.remove_item').on('click', function(e) {
    e.preventDefault();
    var row = $(this).closest('div.education-row');
    $('#add-another-item').show();
    $('input[id^="' + education + '"]', row).val('');
    $('input[id^="' + degree + '"]', row).val('');
    $('input[id^="' + year + '"]', row).val('');
    row.addClass('hiddenElement').fadeOut("slow");
  });

});
</script>
{/literal}