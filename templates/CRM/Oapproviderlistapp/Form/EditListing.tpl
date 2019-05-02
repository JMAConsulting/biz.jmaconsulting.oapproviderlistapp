{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<div class="crm-public-form-item crm-section listing">
  {include file="CRM/UF/Form/Block.tpl" fields=$listing}
</div>
{if $imageURL}
  <div class="file-attachment">
    <div class="crm-contact_image crm-contact_image-block">{$imageURL}</div>
    <div class='crm-contact_image-block crm-contact_image crm-contact_image-delete'>{$deleteURL}</div>
  </div>
{/if}

<div class="crm-section">
<div class="label"><label for="{ts}Employer(s){/ts}">{ts}Employer(s){/ts}</label></div>
<div class="content">
<table class="form-item">
{foreach from=$credentials item=credential}
  <tr>
    <td>
    {ts}Credentials{/ts}: {$credential.which_of_the_following_credentia_7} <br/>
    </td>
  </tr>
{/foreach}
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
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.file-attachment').insertAfter('#editrow-image_URL');
});
</script>
{/literal}
