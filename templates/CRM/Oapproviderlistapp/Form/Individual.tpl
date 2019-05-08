{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<table style="table-layout: fixed;width: 100%;">
  <tr>
    <td colspan="2">
      <div class="crm-section content description-text"><p>{ts}Please read this form carefully to make sure that you meet the requirements to join the Ontario Autism Program (OAP) Provider List.{/ts}</p>

      <p>{ts}Please provide information on your primary current employer(s) through which you will be clinically supervising Ontario Autism Program behavioural services.{/ts}</p>

      <p>{ts}You must provide a proof of employment letter for each employer listed below. This letter must be on the organization’s letterhead and include the role and credentials of the person signing. You do not need to submit proof of self-employment.{/ts}</p></div>
    </td>
  </tr>
  <tr>
    <td width="55%">
      <div class="crm-public-form-item crm-section individual" colspan="2">
        {include file="CRM/UF/Form/Block.tpl" fields=$individual}
      </div>
    </td>
    <td>
      <table style="table-layout: fixed;width: 100%;">
        <tr>
          <td>
            <div class="crm-public-form-item crm-section">
            {section name='i' start=1 loop=5}
            {assign var='rowNumber' value=$smarty.section.i.index}
            <div id="organization_name-{$rowNumber}" class="{if $rowNumber > $totalCount}hiddenElement{/if} {cycle values="odd-row,even-row"} crm-section form-item">
              <br/>
              <div class="content description">{ts}(if self-employed, write &#8220;self-employed&#8221; here){/ts}</div>
              <div class="label">{$form.organization_name.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
              <div class="content">
                {$form.organization_name.$rowNumber.html}
              </div>
              <div class="clear"></div><br/>
              <div class="label">{$form.work_address.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
              <div class="content">{$form.work_address.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.phone.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span> </div>
              <div class="content">{$form.phone.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.city.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
              <div class="content">{$form.city.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.email.$rowNumber.label} {if $rowNumber neq 1}<span class="crm-marker" title="This field is required.">*</span>{/if}</div>
              <div class="content">{$form.email.$rowNumber.html}</div>
              <div class="clear"></div><br/>
              <div class="label">{$form.custom_49.$rowNumber.label}</div>
              <div class="content">{$form.custom_49.$rowNumber.html}</div>
              <div class="clear"></div>
              {if $custom_49_file.$rowNumber.displayURL}
                <div class="crm-section file_displayURL-section file_displayURL-section"><div class="content">{ts}Uploaded file:{/ts} <a href="{$custom_49_file.$rowNumber.displayURL}">{$custom_49_file.$rowNumber.name}</a></div></div>
                <div class="crm-section file_deleteURL-section file_deleteURL-section"><div class="content">{$custom_49_file.$rowNumber.deleteURL}</div></div>
                <div class="clear"></div>
              {/if}
              {if $rowNumber neq 1}
                <div><a href=# class="remove_item crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
              {/if}
              <br/>
            </div>
            {/section}
          </div>
            <br/>
            <br/>
            {crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
            <span id="add-another-item" class="crm-hover-button"><a href=#>{ts}Add another employer{/ts}</a></span>
            {/crmScope}
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
  $('.remove_item').on('click', function(e) {
    e.preventDefault();
    var row = $(this).closest('[id^="organization_name-"]');
    row.addClass('hiddenElement');
  });

  $('#_qf_Individual_submit_done-bottom').on('click', function(e) {
      var msg = {/literal}"{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}An email will be sent to you{/ts}. {ts}It will contain a link that you can click to continue to review this request within the next seven days.{/ts}{/crmScope}"{literal};
      CRM.alert(msg);
  });
});
</script>
{/literal}
{/crmScope}
