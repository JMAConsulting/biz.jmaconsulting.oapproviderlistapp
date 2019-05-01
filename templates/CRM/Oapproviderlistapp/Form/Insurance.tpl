<div class="crm-public-form-item crm-section sectorcheck">
  {include file="CRM/UF/Form/Block.tpl" fields=$insurance}
  {if $custom_57_file.displayURL}
   <div class="file-attachment">
      <div class="crm-section file_displayURL-section file_displayURL-section"><div class="content">{ts}Uploaded file:{/ts} <a src="{$custom_57_file.displayURL}">{$custom_57_file.name}</a></div></div>
      <div class="crm-section file_deleteURL-section file_deleteURL-section"><div class="content">{$custom_57_file.deleteURL}</div></div>
    </div>
  {/if}
</div>
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();
  $('.file-attachment').insertAfter('#editrow-custom_57');
  $('#_qf_Insurance_submit_done-bottom').on('click', function() {
    var msg = {/literal}"{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}An email will be sent to you{/ts}. {ts}It will contain a link that you can click to continue to review this request within the next seven days.{/ts}{/crmScope}"{literal};
    CRM.alert(msg);
  });
});
</script>
{/literal}
