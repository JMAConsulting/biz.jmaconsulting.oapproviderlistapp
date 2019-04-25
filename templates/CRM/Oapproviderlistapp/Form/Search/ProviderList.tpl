{* Default template custom searches. This template is used automatically if templateFile() function not defined in
   custom search .php file. If you want a different layout, clone and customize this file and point to new file using
   templateFile() function.*}
<div class="crm-block crm-form-block crm-contact-custom-search-form-block">
        <table class="form-layout-compressed">
          {foreach from=$elements item=element}
              <tr class="crm-contact-custom-search-form-row-{$element}">
                {if $element eq 'accepting_clients_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-accepting-img" title="Currently accepting new clients"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'remote_travel_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-remote-travel-img" title="Travels to remote areas"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'supervision_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-supervision-img" title="Offers supervision"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'videoconferencing_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-videoconferencing-img" title="Offers remote services"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {else}
                  <td class="label">
                    {$form.$element.label}{if $element eq 'region'}&nbsp;&nbsp;<a href="https://oapproviderlist.ca/civicrm/file?filename=region_064682ff6d22e5b9fc624a950a799257.png&id=285&reset=1" class="crm-image-popup"><i class="crm-i fa-map-marker"></i></a>{/if}
                  </td>
                  {if $element|strstr:'_date'}
                      <td>{include file="CRM/common/jcalendar.tpl" elementName=$element}</td>
                  {else}
                      <td>{$form.$element.html}</td>
                  {/if}
                {/if}
              </tr>
          {/foreach}
        </table>
        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div><!-- /.crm-form-block -->

{if $rowsEmpty || $rows}
<div class="crm-content-block">
{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/Custom/EmptyResults.tpl"}
{/if}

{if $rows}
  <div class="crm-results-block">
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {* This section displays the rows along and includes the paging controls *}
      <div class="crm-search-results">

        {include file="CRM/common/pager.tpl" location="top"}

        {* Include alpha pager if defined. *}
        {if $atoZ}
            {include file="CRM/common/pagerAToZ.tpl"}
        {/if}
        <table>
          {counter start=0 skip=1 print=false}
            {foreach from=$rows item=row}
              {include file="CRM/Oapproviderlistapp/Form/Search/Provider.tpl"}
            {/foreach}
        </table>


        {include file="CRM/common/pager.tpl" location="bottom"}

        </p>
    {* END Actions/Results section *}
    </div>
    </div>
{/if}



</div>
{/if}
{crmScript file='js/crm.expandRow.js'}

{literal}
<script type="text/javascript">
CRM.$(function($) {
$('#block-seven-breadcrumbs').hide();
$('a#expand').click( function() {
    if( $(this).attr('href') == '#expand') {
      var message = {/literal}"{ts escape='js'}Collapse all tabs{/ts}"{literal};
      $(this).attr('href', '#collapse');
    }
    else {
      var message = {/literal}"{ts escape='js'}Expand all tabs{/ts}"{literal};
      $('.crm-accordion-wrapper:not(.collapsed)').crmAccordionToggle();
      $(this).attr('href', '#expand');
    }
    $(this).html(message);
    return false;
  });
});
</script>
{/literal}
