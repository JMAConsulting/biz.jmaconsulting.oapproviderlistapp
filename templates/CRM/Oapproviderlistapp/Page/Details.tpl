{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<tr class="crm-section form-item addDetails"><td>
  <div class="provider-wrapper">
  <div class="provider-details">
  <table class="form-item">
      {if $employers}
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
          {if !$isOrg}<strong>{$employer.organization_name}</strong>{/if}
          <br/>
          {$employer.street_address} <br/>
          {if $employer.supplemental_address_1}{$employer.supplemental_address_1} <br/>{/if}
          {$employer.city}{if $employer.abbreviation}, {$employer.abbreviation}{/if} {$employer.postal_code} <br/>
          <div class=clear></div>

          {if $employer.phone}
             {$employer.phone} {if $employer.phone_ext}{ts}ext{/ts}: {$employer.phone_ext}{/if}
            <div class=clear></div>
          {/if}

          {if $employer.email}
            <a href='mailto:{$employer.email}' style='color:#0071bd !important;cursor: pointer !important'>{$employer.email}</a>
            <div class=clear></div>
          {/if}

          {if $employer.url}
            <a href='{$employer.url}' style='color:#0071bd !important;cursor: pointer !important' target='_blank'>{$employer.url}</a>
            <br/>
            <div class=clear></div>
          {/if}

          {if !empty($providers)}
            <div class='provider-list'>
              <br><div class="label-text">{ts}Providers{/ts}:</div>
              {foreach from=$providers item=provider}
                <br/>{$provider}
              {/foreach}
            </div>
          {/if}
        </td>
        <td>
        </td>
      </tr>
      {/foreach}
      {else}
      <tr>
          {if $image}
          <td>
              <div class="provider-image">
                  {$image}
              </div>
          </td>
          {/if}
          {if $credentials}
          <td>
              {foreach from=$credentials item=credential}
                  <div>
                      {ts}Credentials{/ts}: {$credential.which_of_the_following_credentia_7} <br/>
                  </div>
              {/foreach}
          </td>
          {/if}
      </tr>
      {/if}
  </table>
  </div>
  </div>
  </td>
</tr>
{/crmScope}
