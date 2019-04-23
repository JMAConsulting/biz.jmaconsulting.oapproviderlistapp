{* Default template custom searches. This template is used automatically if templateFile() function not defined in
   custom search .php file. If you want a different layout, clone and customize this file and point to new file using
   templateFile() function.*}
<div class="crm-block crm-form-block crm-contact-custom-search-form-block">
<div class="crm-accordion-wrapper crm-custom_search_form-accordion {if $rows}collapsed{/if}">
    <div class="crm-accordion-header crm-master-accordion-header">
      {ts}Edit Search Criteria{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
        <table class="form-layout-compressed">
          {foreach from=$elements item=element}
              <tr class="crm-contact-custom-search-form-row-{$element}">
                  <td class="label">{$form.$element.label}</td>
                  {if $element|strstr:'_date'}
                      <td>{include file="CRM/common/jcalendar.tpl" elementName=$element}</td>
                  {else}
                      <td>{$form.$element.html}</td>
                  {/if}
              </tr>
          {/foreach}
        </table>
        <div id="customData"></div>
        {include file="CRM/common/customDataBlock.tpl"}
        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
    </div><!-- /.crm-accordion-body -->
</div><!-- /.crm-accordion-wrapper -->
</div><!-- /.crm-form-block -->

{if $rowsEmpty || $rows}
<div class="crm-content-block">
{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/Custom/EmptyResults.tpl"}
{/if}

{if $rows}
  <div class="crm-results-block">
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
        {* This section handles form elements for action task select and submit *}
       <div class="crm-search-tasks">
        {include file="CRM/Contact/Form/Search/ResultTasks.tpl"}
    </div>
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
