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
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>