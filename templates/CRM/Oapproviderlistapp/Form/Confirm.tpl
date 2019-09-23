{* HEADER *}

<h1>
<div class="crm-summary-display_name">{$displayName}</div>
</h1>
<br/>
<fieldset class="crm-profile">
  <div class="header-dark">{ts}Employer information{/ts}</div>
<table class="form-item">
    {foreach from=$emps item=employer}
    <tr>
      <td width=50%>
        <b>{$employer.organization_name}</b> <br/>
        {if $employer.phone}{ts}Phone{/ts}: {$employer.phone} <br/>
        {$employer.street_address} <br/>
        {$employer.city}{if $employer.abbreviation}, {$employer.abbreviation}{/if} {$employer.postal_code}
        <div class=clear></div>
      </td>
      <td>
        {/if}
        <a href='mailto:{$employer.email}'>{$employer.email}</a>
        <div class=clear></div>
      </td>
    </tr>
    {if !empty($employer.files)}
      <tr>
        <td style="float:left;">
          {if $employer.files.displayURL}
            {ts}Proof of Employment:{/ts}
              {if !empty($employer.files.imageURL)}
                <a class="crm-image-popup" href="{$employer.files.displayURL}">{$employer.files.imageURL}</a>
              {else}
                {$employer.files.displayURL}
              {/if}
            </br>
          {/if}
        </td>
        <td>
        </td>
      </tr>
      <tr><td></br></td><td></td><tr>
    {/if}
    {/foreach}
</table>
</fieldset>
<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
  {if $otherProfessional}
    <fieldset>
    <legend>{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}Other Professional Credentials{/ts}{/crmScope}</legend>
    {foreach from=$otherProfessional item=values}
      <div class="crm-section form-item">
      {foreach from=$values item=value key=label}
        <div class="label">
          <label>{$label}</label>
        </div>
        <div class="content">{$value}</div>
        <br/>
      {/foreach}
      </div>
    {/foreach}
    </fieldset>
  {/if}
</div>

<div class="crm-public-form-item crm-section experience">
  {include file="CRM/UF/Form/Block.tpl" fields=$experience}
  {if $employers}
    <fieldset>
    <legend>{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}Employment History{/ts}{/crmScope}</legend>
    {foreach from=$employers item=values}
      <div class="crm-section form-item">
      {foreach from=$values item=value key=label}
        <div class="label">
          <label>{$label}</label>
        </div>
        <div class="content"><span class="crm-frozen-field">{$value}</span></div>
        <div class="clear"></div>
      {/foreach}
      </div>
    {/foreach}
  </fieldset>
  {/if}
</div>

<div class="crm-public-form-item crm-section sectorcheck">
  {include file="CRM/UF/Form/Block.tpl" fields=$sectorcheck}
</div>

<div class="crm-public-form-item crm-section insurance">
  {include file="CRM/UF/Form/Block.tpl" fields=$insurance}
</div>

<div class="crm-public-form-item crm-section signature">
  {include file="CRM/UF/Form/Block.tpl" fields=$signature}
</div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();
  $('#crm-container .signature').css('width', 'auto');
  $('#editrow-custom_46 .content .crm-frozen-field').val($('#editrow-custom_46 .content .crm-frozen-field').html($('#editrow-custom_46 .content .crm-frozen-field').text()).text());
  $('#editrow-custom_57 .content .crm-frozen-field').val($('#editrow-custom_57 .content .crm-frozen-field').html($('#editrow-custom_57 .content .crm-frozen-field').text()).text());
  $('#editrow-custom_58 .content .crm-frozen-field').val($('#editrow-custom_58 .content .crm-frozen-field').html($('#editrow-custom_58 .content .crm-frozen-field').text()).text());
});
</script>
{/literal}
