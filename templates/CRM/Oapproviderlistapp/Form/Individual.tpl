<table>
  <tr>
    <td colspan="2">
      {crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
      {ts}
      <p>Please read this form carefully to make sure that you meet the requirements to join the Ontario Autism Program (OAP) Provider List.
      </p>

      <p>Please provide information on your primary current employer(s) through which you will be clinically supervising Ontario Autism Program behavioural services.
      </p>

      <p>You must provide a proof of employment letter for each employer listed below. This letter must be on the organization’s letterhead and include the role and credentials of the person signing. You do not need to submit proof of self-employment.
      </p>
      {/ts}
      {/crmScope}
    </td>
  </tr>
  <tr>
    <td width="55%">
      <div class="crm-public-form-item crm-section individual" colspan="2">
        {include file="CRM/UF/Form/Block.tpl" fields=$individual}
      </div>
    </td>
    <td>
      <table>
        <tr>
          <td>
            <div class="crm-public-form-item crm-section">
            {section name='i' start=1 loop=5}
            {assign var='rowNumber' value=$smarty.section.i.index}
            <div id="organization_name-{$rowNumber}" class="{if $rowNumber neq 1}hiddenElement{/if} {cycle values="odd-row,even-row"} crm-section form-item">
              <br/>
              <div class="label">{$form.organization_name.$rowNumber.label}</div>
              <div class="content">
                {$form.organization_name.$rowNumber.html}
              </div>
              <div class="clear"></div><br/>
              <div class="label">{$form.work_address.$rowNumber.label}</div>
              <div class="content">{$form.work_address.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.phone.$rowNumber.label}</div>
              <div class="content">{$form.phone.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.city.$rowNumber.label}</div>
              <div class="content">{$form.city.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.email.$rowNumber.label}</div>
              <div class="content">{$form.email.$rowNumber.html}</div>
              <div class="clear"></div>
              {if $rowNumber eq 1}
                <br/>
                <div class="label">{$form.custom_49.label}</div>
                <div class="content">{$form.custom_49.html}</div>
                <div class="clear"></div>
              {/if}
              <br/>
            </div>
            {/section}
          </div>
            <br/>
            <br/>
            {crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
            <span id="add-another-item" class="crm-hover-button"><a href=#>{ts}Add another employer{/ts}</a></span>
            {/ts}
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
