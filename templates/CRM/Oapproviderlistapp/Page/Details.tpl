{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<tr class="crm-section form-item addDetails"><td>
  <table class="form-item">
      {if $image}
      <tr>
        <td>
          <div class="provider-image">
            <img src="{$image}" />
          </div>
        </td>
      </tr>
      {/if}
      {foreach from=$credentials item=credential}
      <tr>
        <td>
        <i>{ts}Credentials{/ts}:</i> {$credential.which_of_the_following_credentia_7} <br/>
        </td>
      </tr>
      {/foreach}
      {foreach from=$employers item=employer}
      <tr>
        <td>
          {$employer.organization_name} <br/>
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
