<tr class="crm-section form-item"><td>
  <table class="form-item provider-row">
    <tr colspan=2>
      <td style="width=50%;">
        <div style="color:#bd3933">
          <font style="float:left;color:#1264A9;font-size:1.4em;font-weight:bold;padding-top:3px;" class=" field--name-field-title {if $row.accepting_new_clients__63 eq 0}not-accepting{/if} provider-title">{$row.first_name} {$row.last_name}</font>&nbsp;&nbsp;&nbsp;
          {if $row.travels_to_remote_areas__65 eq 1}<span class="provider-icon icon-remote-travel-img" title="Travels to remote areas"></span>{/if}
          {if $row.offers_supervision__66 eq 1}<span class="provider-icon icon-supervision-img" title="Offers supervision"></span>{/if}
          {if $row.offers_video_conferencing_servic_69 eq 1}<span class="provider-icon icon-videoconferencing-img" title="Offers remote services"></span>{/if}
{if $row.accepting_new_clients__63 eq 1}
            <span class="provider-icon icon-accepting-img" title="Currently accepting new clients"></span>
          {elseif $row.accepting_new_clients__63 eq 0}
            <span class="provider-icon icon-not-accepting-img" title="Not accepting new clients"></span>&nbsp;Not accepting new clients
          {/if}
        </div>
<br/>
      </td>
    </tr>
    <tr>
            <td >
              Region: {$row.region_67}&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
</tr>
<tr>
            <td>
              Speaks: {$row.language_68}
            </td>
    </tr>
    <tr>
      <td>
        <br/>
        <a class="nowrap bold crm-expand-row rasp-expand-hint" title="{ts}view details{/ts}" href="{crmURL p="civicrm/provider/details" q="reset=1&cid=`$row.contact_id`"}">
          Click for more details
        </a>
      </td>
    </tr>
  </table>
</tr>
