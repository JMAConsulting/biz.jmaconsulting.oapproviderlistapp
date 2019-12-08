{if $searchByOrg}
<tr class="crm-section form-item"><td>
  <table class="form-item provider-row">
    <tr colspan=2>
      <td style="width=50%;">
        <div style="color:#bd3933 !important">
          <font style="float:left;color:#1264A9;font-size:1.4em;font-weight:bold;padding-top:3px;" class=" field--name-field-title provider-title">{$row.organization_name}</font>&nbsp;&nbsp;&nbsp;
        </div>
      <br/>
      </td>
    </tr>
    <tr>
      <td>
        <br/>
        <a class="nowrap bold crm-expand-row rasp-expand-hint" title="{ts}view details{/ts}" href="{crmURL p="civicrm/provider/details" q="reset=1&cid=`$row.contact_id`"}">
          {ts}Click for more details{/ts}
        </a>
      </td>
    </tr>
  </table>
</tr>
{else}
{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<tr class="crm-section form-item"><td>
  <table class="form-item provider-row">
    <tr colspan=2>
      <td style="width=50%;">
        <div style="color:#bd3933 !important">
          <font style="float:left;color:#1264A9;font-size:1.4em;font-weight:bold;padding-top:3px;" class=" field--name-field-title {if $row.accepting_new_clients__65 eq 0}not-accepting{/if} provider-title">{$row.first_name} {$row.last_name}</font>&nbsp;&nbsp;&nbsp;
          {if $row.travels_to_remote_areas__67 eq 1}<span class="provider-icon icon-remote-travel-img" title="{ts}Travels to remote areas{/ts}"></span>{/if}
          {if $row.offers_supervision__68 eq 1}<span class="provider-icon icon-supervision-img" title="{ts}Offers supervision{/ts}"></span>{/if}
          {if $row.offer_video_conferencing_service_70 eq 1}<span class="provider-icon icon-videoconferencing-img" title="{ts}Offers remote services{/ts}"></span>{/if}
          {if $row.accepting_new_clients__65 eq 1}
            <span class="provider-icon icon-accepting-img" title="{ts}Currently accepting new clients{/ts}"></span>
          {elseif $row.accepting_new_clients__65 eq 0}
            <span class="provider-icon icon-not-accepting-img" title="{ts}Not accepting new clients{/ts}"></span>&nbsp;{ts}Not accepting new clients{/ts}
          {/if}
        </div>
<br/>
      </td>
    </tr>
    <tr>
      <td >
        {if $row.bacb_r_disciplinary_action_71 || $row.cpo_discipline_and_other_proceed_72}
        <div style="overflow:hidden;color:#bd3933">
         <a style="color:#bd3933 !important" href="{$row.bacb_r_disciplinary_action_71}">{ts}BACB(r) Disciplinary Action{/ts}</a>{if isset($row.bacb_r_disciplinary_action_71) and isset($row.cpo_discipline_and_other_proceed_72)}, {/if} <a style="color:#bd3933 !important" href="{$row.cpo_discipline_and_other_proceed_72}">{ts}CPO Discipline and Other Proceedings{/ts}</a>
        </div>
        {/if}
        <div style="float: left;width:30%;overflow: hidden;">
        {ts}Region{/ts}: {$row.region_63}
      </div>
      <div style="overflow: hidden;">
        {ts}Speaks{/ts}: {$row.language_64}
      </div>
      </td>
    </tr>
    <tr>
      <td>
        <br/>
        <a class="nowrap bold crm-expand-row rasp-expand-hint" title="{ts}view details{/ts}" href="{crmURL p="civicrm/provider/details" q="reset=1&cid=`$row.contact_id`"}">
          {ts}Click for more details{/ts}
        </a>
      </td>
    </tr>
  </table>
</tr>
{/crmScope}
{/if}
