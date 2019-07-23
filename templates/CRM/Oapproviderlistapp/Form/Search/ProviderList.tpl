{* Default template custom searches. This template is used automatically if templateFile() function not defined in
   custom search .php file. If you want a different layout, clone and customize this file and point to new file using
   templateFile() function.*}
{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<div class="crm-block crm-form-block crm-contact-custom-search-form-block">
        <table>
          {foreach from=$elements item=element}
              <tr class="crm-contact-custom-search-form-row-{$element}">
                {if $element eq 'accepting_clients_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-accepting-img" title="{ts}Currently accepting new clients{/ts}"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'remote_travel_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-remote-travel-img" title="{ts}Travels to remote areas{/ts}"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'supervision_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-supervision-img" title="{ts}Offers supervision{/ts}"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'videoconferencing_filter'}
                  <td></td>
                  <td>
                    {$form.$element.html}&nbsp;
                    <span class="provider-icon icon-videoconferencing-img" title="{ts}Offers remote services{/ts}"></span>&nbsp;&nbsp;
                    {$form.$element.label}
                  </td>
                {elseif $element eq 'credentials'}
                  <td style="width:15%;text-align:right;">
                    {$form.$element.label}:
                  </td>
                  <td>
                    &nbsp;{$form.credentials.1.html}<br/>
                    &nbsp;{$form.credentials.2.html}<br/>
                    &nbsp;{$form.credentials.3.html}<br/>
                    &nbsp;{$form.credentials.4.html}<br/>
                  </td>
                {else}
                  <td style="width:10%;text-align:right;">
                    {$form.$element.label}{if $element eq 'region'}&nbsp;&nbsp;<a title="{ts}Click for map of regions{/ts}" href="/civicrm/file?reset=1&filename=Screen_Shot_2019_07_23_at_6_15_47_PM_8d080c4b5453b3e12e165b11764be645.png&mime-type=image/png" class="crm-image-popup"><i class="crm-i fa-map-marker"></i></a>{/if}
                  </td>
                  {if $element|strstr:'_date'}
                      <td>{include file="CRM/common/jcalendar.tpl" elementName=$element}</td>
                  {else}
                      <td>&nbsp;{$form.$element.html}</td>
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
    {include file="CRM/Oapproviderlistapp/Form/Search/EmptyResults.tpl"}
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
        <span style="float:right;"><a href="#expand" id="expand">{ts}Expand all results{/ts}</a></span>
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
      var message = {/literal}"{ts escape='js'}Collapse all results{/ts}"{literal};
      $(this).attr('href', '#collapse');
      $('.rasp-expand-hint').each(function(){
       if (!$(this).hasClass('expanded')) {
        $(this).trigger('click');
       }
     });
    }
    else if ($(this).attr('href') == '#collapse') {
      var message = {/literal}"{ts escape='js'}Expand all results{/ts}"{literal};
      $(this).attr('href', '#expand');
      $('.rasp-expand-hint').each(function(){
       if ($(this).hasClass('expanded')) {
         $(this).trigger('click');
       }
      });
    }
    $(this).html(message);
    return false;
  });

  $('a.rasp-expand-hint').click(function () {
    if ($(this).hasClass('expanded')) {
      $(this).removeClass('rasp-expand');
      var message = {/literal}"{ts escape='js'}Click for more details{/ts}"{literal};
      $(this).text(message);
    }
    else {
      $(this).addClass('rasp-expand');
      var message = {/literal}"{ts escape='js'}Click to hide details{/ts}"{literal};
      $(this).text(message);
    }
  });
});
</script>
{/literal}
{/crmScope}
