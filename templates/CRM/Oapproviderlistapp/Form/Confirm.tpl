{* HEADER *}

<div class="crm-public-form-item crm-section professional">
  {include file="CRM/UF/Form/Block.tpl" fields=$professional}
  {if $otherProfessional}
    <div class="crm-section form-item">
    {ts}Other Professional Credentials{/ts}
    {foreach from=$otherProfessional item=values}
      <div class="crm-section form-item">
      {foreach from=$values item=value key=label}
        <div class="label">
          <label>{$label}</label>
        </div>
        <div class="content">{$value}</div>
        <div class="clear"></div>
      {/foreach}
      </div>
    {/foreach}
    </div>
  {/if}
</div>

<div class="crm-public-form-item crm-section experience">
  {include file="CRM/UF/Form/Block.tpl" fields=$experience}
  {if $employers}
    <div class="crm-section form-item">
    {ts}Employment History{/ts}
    {foreach from=$employers item=values}
      <div class="crm-section form-item">
      {foreach from=$values item=value key=label}
        <div class="label">
          <label>{$label}</label>
        </div>
        <div class="content">{$value}</div>
        <div class="clear"></div>
      {/foreach}
      </div>
    {/foreach}
  </div>
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
  $('#editrow-custom_46 .content .crm-frozen-field').val($('#editrow-custom_46 .content .crm-frozen-field').html($('#editrow-custom_46 .content .crm-frozen-field').text()).text());
  $('#editrow-custom_57 .content .crm-frozen-field').val($('#editrow-custom_57 .content .crm-frozen-field').html($('#editrow-custom_57 .content .crm-frozen-field').text()).text());
  $('#editrow-custom_58 .content .crm-frozen-field').val($('#editrow-custom_58 .content .crm-frozen-field').html($('#editrow-custom_58 .content .crm-frozen-field').text()).text());
});
</script>
{/literal}
