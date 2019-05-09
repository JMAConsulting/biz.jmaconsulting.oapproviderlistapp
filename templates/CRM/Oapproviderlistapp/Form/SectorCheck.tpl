<div class="crm-public-form-item crm-section sectorcheck">
  {include file="CRM/UF/Form/Block.tpl" fields=$sectorcheck}
  {if $custom_46_file.displayURL}
   <div class="file-attachment-46">
      <div class="crm-section file_displayURL-section file_displayURL-section"><div class="content">{ts}Uploaded file:{/ts} <a href="{$custom_46_file.displayURL}">{$custom_46_file.name}</a></div></div>
      <div class="crm-section file_deleteURL-section file_deleteURL-section"><div class="content">{$custom_46_file.deleteURL}</div></div>
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
  $('.file-attachment-46').insertAfter('#editrow-custom_46');
  $('#_qf_SectorCheck_submit_done-bottom').on('click', function() {
    var msg = {/literal}"{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}{ts}An email will be sent to you{/ts}. {ts}It will contain a link that you can click to continue to review this request within the next seven days.{/ts}{/crmScope}"{literal};
    CRM.alert(msg);
  });
});
</script>
{/literal}
