{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<tr class="crm-section form-item addDetails"><td>
  <div class="provider-wrapper">
  <div class="provider-details">
  <table class="form-item">
      {foreach from=$employers key=k item=employer}
      <tr>
        <td>
          {if $image && $k eq 0}
            <div class="provider-image">
              {$image}
            </div>
          {/if}
        </td>
        <td>
          {if $k eq 0}
            {foreach from=$credentials item=credential}
              <div>
                {ts}Credentials{/ts}: {$credential.which_of_the_following_credentia_7} <br/>
              </div>
              <br/>
            {/foreach}
          {/if}
          {if !$isOrg}<strong>{$employer.organization_name}</strong><br/>{/if}
          <br/><div class="label-text">{ts}Address{/ts}:</div>
          {$employer.street_address} <br/>
          {if $employer.supplemental_address_1}{$employer.supplemental_address_1} <br/>{/if}
          {$employer.city}{if $employer.abbreviation}, {$employer.abbreviation}{/if} {$employer.postal_code} <br/>
          <div class=clear></div><br/>

          {if $employer.phone}
            <div class="label-text">{ts}Phone{/ts}:</div>
             {$employer.phone} {if $employer.phone_ext}{ts}ext{/ts}: {$employer.phone_ext}{/if}
            <div class=clear></div><br/>
          {/if}

          {if $employer.email}
            <div class="label-text">{ts}Email{/ts}:</div>
            <a href='mailto:{$employer.email}' style='color:#0071bd !important;cursor: pointer !important'>{$employer.email}</a>
            <div class=clear></div><br/>
          {/if}

          {if $employer.url}
            <div class="label-text">{ts}Website{/ts}:</div>
            <a href='{$employer.url}' style='color:#0071bd !important;cursor: pointer !important' target='_blank'>{$employer.url}</a>
            <br/>
            <div class=clear></div><br/>
          {/if}

          {if !empty($providers)}
            <div class='provider-list'>
              <br><div class="label-text">{ts}Providers{/ts}:</div>
              {foreach from=$providers item=provider}
                {$provider}<br/>
              {/foreach}
            </div>
          {/if}
        </td>
        <td>
        </td>
      </tr>
      {/foreach}
  </table>
  </div>
  </div>
  </td>
</tr>
{/crmScope}
