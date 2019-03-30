<table>
  <tr>
    <td colspan="2">
      <p>
        Please provide information on your primary current employer(s) through which you will be clinically supervising Ontario Autism Program behavioural services.

        You must provide a proof of employment letter for each employer listed below. This letter must be on the organization’s letterhead and include the role and credentials of the person signing. You do not need to submit proof of self-employment.
      </p>
    </td>
  </tr>
  <tr>
    <td width="48%">
      <div class="crm-public-form-item crm-section individual" colspan="2">
        {include file="CRM/UF/Form/Block.tpl" fields=$individual}
      </div>
    </td>
    <td>
      <table>
        <tr>
          <td>
            <div>
            {section name='i' start=1 loop=5}
            {assign var='rowNumber' value=$smarty.section.i.index}
            <dt id="organization_name-{$rowNumber}" class="{if $rowNumber neq 1}hiddenElement{/if} crm-section line-item">
              <div class="label">{$form.organization_name.$rowNumber.label}</div>&nbsp;&nbsp;&nbsp;&nbsp;{$form.organization_name.$rowNumber.html}
            </dt>
            {/section}
          </div>
            <br/>
            <br/>
            <span id="add-another-item" class="crm-hover-button"><a href=#>{ts}Add another employer{/ts}</a></span>
          </td>
        </tr>
        <tr>
          <td>
            <div class="crm-public-form-item crm-section phoneaddress">
              {include file="CRM/UF/Form/Block.tpl" fields=$phoneaddress}
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<div id="crm-submit-buttons" class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();
  $('#add-another-item').on('click', function(e) {
    e.preventDefault();
    if ($('[id^="organization_name-"]').hasClass("hiddenElement")) {
      $('.hiddenElement:first').removeClass('hiddenElement');
    }
  });
});
</script>
{/literal}
