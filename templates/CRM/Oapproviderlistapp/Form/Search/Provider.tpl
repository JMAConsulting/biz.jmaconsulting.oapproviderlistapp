<tr class="{cycle values="odd-row,even-row"} crm-section form-item"><td>
  <table class="form-item provider-row">
    <tr>
      <td>
        <h1>
          {$row.first_name} {$row.last_name}&nbsp;&nbsp;&nbsp;
          {if $row.accepting_new_clients__65 eq 1}<span class="provider-icon icon-accepting-img" title="Currently accepting new clients"></span>{/if}
          {if $row.travels_to_remote_areas__67 eq 1}<span class="provider-icon icon-remote-travel-img" title="Travels to remote areas"></span>{/if}
          {if $row.offers_supervision__68 eq 1}<span class="provider-icon icon-supervision-img" title="Offers supervision"></span>{/if}
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
        <a class="nowrap bold crm-expand-row" title="{ts}view details{/ts}" href="{crmURL p="civicrm/provider/details" q="reset=1&cid=`$row.contact_id`"}">
          Click for more details
        </a>
      </td>
    </tr>
  </table>
</tr>
