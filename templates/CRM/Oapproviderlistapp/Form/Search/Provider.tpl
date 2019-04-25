<tr class="crm-section form-item"><td>
  <table class="form-item provider-row">
    <tr>
      <td>
        <h1 class="{if $row.accepting_new_clients__63 eq 0}not-accepting{/if}">
          {$row.first_name} {$row.last_name}&nbsp;&nbsp;&nbsp;
          {if $row.accepting_new_clients__63 eq 1}
            <span class="provider-icon icon-accepting-img" title="Currently accepting new clients"></span>
          {elseif $row.accepting_new_clients__63 eq 0}
            <span class="provider-icon icon-not-accepting-img" title="Currently not accepting new clients"></span>
          {/if}
          {if $row.travels_to_remote_areas__65 eq 1}<span class="provider-icon icon-remote-travel-img" title="Travels to remote areas"></span>{/if}
          {if $row.offers_supervision__66 eq 1}<span class="provider-icon icon-supervision-img" title="Offers supervision"></span>{/if}
          {if $row.offers_video_conferencing_servic_69 eq 1}<span class="provider-icon icon-videoconferencing-img" title="Offers remote services"></span>{/if}
        </h1>
      </td>
    </tr>
    <tr>
      <td>
        <div class="label"><i>Speaks:</i></div>
        <div class="content">{$row.language_68}</div>
        <div class=clear></div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="label"><i>Region:</i></div>
        <div class="content">{$row.region_67}</div>
        <div class=clear></div>
      </td>
    </tr>
    <tr>
      <td>
        <a class="nowrap bold crm-expand-row rasp-expand-hint" title="{ts}view details{/ts}" href="{crmURL p="civicrm/provider/details" q="reset=1&cid=`$row.contact_id`"}">
          Click for more details
        </a>
      </td>
    </tr>
  </table>
</tr>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('a.rasp-expand-hint').click(function () {
    if ($(this).hasClass('expanded')) {
      $(this).removeClass('rasp-expand');
    }
    else {
      $(this).addClass('rasp-expand');
    }
  });
});

</script>
{/literal}
