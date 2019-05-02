{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}
    {if $elementName eq 'image_URL'}
      {help id="image-file" file="CRM/Oapproviderlistapp/Form/EditListing.hlp"}
    {/if}
    </div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}
<div class="crm-section">
<div class="label"><label for="{ts}Employer(s){/ts}">{ts}Employer(s){/ts}</label></div>
<div class="content">
<table class="form-item">
{foreach from=$employers item=employer}
      <tr>
        <td>
          {$employer.organization_name} <br/>
          {$employer.street_address} <br/>
          {$employer.city}{if $employer.abbreviation}, {$employer.abbreviation}{/if} {$employer.postal_code}
          <div class=clear></div>
        </td>
        <td>
          {if $employer.phone}Phone: {$employer.phone} <br/>{/if}
          <a href='mailto:{$employer.email}'>{$employer.email}</a>
          <div class=clear></div>
        </td>
      </tr>
{/foreach}
</table>
</div>
</div>
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{/crmScope}