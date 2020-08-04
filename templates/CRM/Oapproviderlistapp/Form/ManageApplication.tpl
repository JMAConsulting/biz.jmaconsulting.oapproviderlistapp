{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}<p>{ts}Complete the application online on the form below or{/ts} <a href='{$fileLink}'>{ts}click here to download a fillable PDF application form.{/ts}</a></p>

<p>{ts}Please note{/ts}: {ts}If you download the application form, please email the completed form to{/ts} <a href="mailto: info@oapproviderlist.ca">info@oapproviderlist.ca</a></p>
&nbsp;</p>{/crmScope}

{include file="CRM/common/TabHeader.tpl"}
{include file="CRM/common/TabSelected.tpl" defaultTab='individual'}

{include file="CRM/common/footer.tpl"}
<div id="crm-public-notification-container" role="alert" aria-live="assertive" aria-atomic=”true” style="display:none">
  <div id="crm-notification-alert" class="#{ldelim}type{rdelim}">
    <div class="icon ui-notify-close" title="{ts}close{/ts}"> </div>
    <a class="ui-notify-cross ui-notify-close" href="#" title="{ts}close{/ts}">x</a>
    <h1>#{ldelim}title{rdelim}</h1>
    <div class="notify-content">#{ldelim}text{rdelim}</div>
  </div>
</div>
