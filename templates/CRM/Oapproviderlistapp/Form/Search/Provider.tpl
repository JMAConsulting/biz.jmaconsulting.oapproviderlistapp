<tr class="{cycle values="odd-row,even-row"} crm-section form-item"><td>
  <table class="form-item provider-row">
    <tr>
      <td>
        <h1>
          {$row.first_name} {$row.last_name}&nbsp;&nbsp;&nbsp;
          <span class="provider-icon icon-accepting-img" title="Currently accepting new clients"></span>
          <span class="provider-icon icon-not-accepting-img" title="Not accepting new clients"></span>
          <span class="provider-icon icon-remote-travel-img" title="Travels to remote areas"></span>
          <span class="provider-icon icon-supervision-img" title="Currently offers supervision"></span>
          <span class="provider-icon icon-videoconferencing-img" title="Offers video conferencing services"></span>
        </h1>
      </td>
    </tr>
    <tr>
      <td>
        <div class="label"><i>Speaks:</i></div>
        <div class="content">{$row.language_64}</div>
        <div class=clear></div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="label"><i>Region:</i></div>
        <div class="content">{$row.region_63}</div>
        <div class=clear></div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="label">Postal Code:</div>
        <div class="content">{$row.postal_code}</div>
        <div class=clear></div>
      </td>
    </tr>
    <tr>
      <td>
        <a class="nowrap bold crm-expand-row" title="{ts}view details{/ts}" href="{crmURL p='civicrm/provider/details' q='reset=1&cid={$row.contact_id}'}">
          Click here to view details
        </a>
      </td>
    </tr>
  </table>
</tr>
