{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}
<div class="crm-public-form-item crm-section form-item listing">
{if $name}
  <!--<div class="label"><label for="name">{ts}Name{/ts}</label></div>-->
  <div class="content provider-title" style="font-weight:bold">{$name}</div>
  <div class=clear></div>
{/if}
  {include file="CRM/UF/Form/Block.tpl" fields=$listing}
</div>
{if $imageURL}
  <div class="file-attachment">
    <div class="crm-contact_image crm-contact_image-block">{$imageURL}</div>
    <div class='crm-contact_image-block crm-contact_image crm-contact_image-delete'>{$deleteURL}</div>
  </div>
  <div class=clear></div>
{/if}

<div class="crm-section form-item">
{if $credentials.0.which_of_the_following_credentia_7}
<div class="label"><label for="{ts}Credentials{/ts}">{ts}Title/Professional Credentials{/ts}</label> *</div>
<div class="content">
<table class="form-item">
{foreach from=$credentials item=credential}
  <tr>
    <td>
    {$credential.which_of_the_following_credentia_7} <br/>
    </td>
  </tr>
{/foreach}
</table>
</div>
<div class=clear></div>
{/if}
{if $employers}
<div class="label"><label for="{ts}Employer(s){/ts}">{ts}Employer(s){/ts} *</label></div>
<div class="content">
  <table class="form-item">
{foreach from=$employers item=employer}
  <tr>
    <td>
    <b>{$employer.organization_name}</b> <br/>
    {$employer.street_address} <br/>
    {$employer.city}{if $employer.abbreviation}, {$employer.abbreviation}{/if} {$employer.postal_code}
    <div class=clear></div>
    </td>
    <td>
    {if $employer.phone}{ts}Phone{/ts}: {$employer.phone} <br/>{/if}
    <a href='mailto:{$employer.email}'>{$employer.email}</a>
    <div class=clear></div>
    </td>
  </tr>
{/foreach}
</table>
</div>
<div class="content-description">
  <br/>
  <br/>
  <br/>
 * To change these fields, please contact <a href='mailto:info@oapproviderlist.ca'>info@oapproviderlist.ca</a>.
 <br/>
 <br/>
  To change Employer(s) information, please contact <a href="coordinator@oapproviderlist.ca">coordinator@oapproviderlist.ca</a></div>
</div>
{/if}
<div class="crm-submit-buttons">
  {$form.done.html}
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{/crmScope}
{literal}
<script type="text/javascript">
  function contactImagePopUp (url, width, height) {
     newWindow = window.open( url,'name', 'width='+width+', height='+height );
  }
  CRM.$(function($) {
    $('.file-attachment').insertAfter('#image_URL');
    $('#region-marker').insertAfter($('#editrow-custom_63 > .label > label'));
  });
</script>
{/literal}
