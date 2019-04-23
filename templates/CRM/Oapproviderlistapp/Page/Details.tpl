{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<tr class="{cycle values="odd-row,even-row"} crm-section form-item"><td>
  <table class="form-item provider-row">
      {foreach from=$credentials item=credential}
      <tr>
        <td>
        <b><i>{ts}Credentials{/ts}:</i></b> {$credential.which_of_the_following_credentia_7} <br/>
        {if $credential.bcba_certification_date_8}<b><i>{ts}BCBA Certification Date{/ts}</i>:</b> {$credential.bcba_certification_date_8}{/if}  {if $credential.bcba_certification_number_40}<b><i>{ts}BCBA Certification Number{/ts}</i>:</b> {$credential.bcba_certification_number_40}{/if}
        </td>
      </tr>
      {/foreach}
      {foreach from=$employers item=employer}
      <tr>
        <td>
          <a href='https://oapproviderlist.ca/civicrm/contact/view?reset=1&cid={$employer.id}'>{$employer.organization_name}</a> <br/>
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
</tr>
{/crmScope}
