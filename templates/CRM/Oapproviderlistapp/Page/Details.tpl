{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<tr class="crm-section form-item addDetails"><td>
  <div class="provider-wrapper">
  <div class="provider-details">
  <table class="form-item">
      {foreach from=$credentials item=credential}
      <tr>
        <td>
        {ts}Credentials{/ts}: {$credential.which_of_the_following_credentia_7} <br/>
        </td>
      </tr>
      {/foreach}
      {foreach from=$employers item=employer}
      <tr>
        <td>
          {$employer.organization_name} <br/>
          {$employer.street_address} <br/>
          {if $employer.supplemental_address_1}{$employer.supplemental_address_1} <br/>{/if}
          {$employer.city}{if $employer.abbreviation}, {$employer.abbreviation}{/if} {$employer.postal_code} <br/>
          <a href='{$employer.url}' style='color:#0071bd !important;cursor: pointer !important' target='_blank'>{$employer.url}</a>
          <div class=clear></div>
        </td>
        <td>
          {if $employer.phone}{ts}Phone{/ts}: {$employer.phone} {if $employer.phone_ext}{ts}ext{/ts}: {$employer.phone_ext}{/if}<br/>{/if}
          <a href='mailto:{$employer.email}' style='color:#0071bd !important;cursor: pointer !important'>{$employer.email}</a>
          <div class=clear></div>
        </td>
      </tr>
      {/foreach}
  </table>
  </div>
  {if $image}
    <div class="provider-image">
      <img src="{$image}" />
    </div>
  {/if}
  </div>
  </td>
</tr>
{/crmScope}
