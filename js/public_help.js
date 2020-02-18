(function(CRM, $) {
  var publicHelpDisplay, publicHelpPrevious;
  // Non-ajax example:
  //   CRM.help('Example title', 'Here is some text to describe this example');
  // Ajax example (will load help id "foo" from templates/CRM/bar.tpl):
  //   CRM.help('Example title', {id: 'foo', file: 'CRM/bar'});
  CRM.publicHelp = function (title, params, url) {
    var ajax = typeof params !== 'string';
    if (publicHelpDisplay && helpDisplay.close) {
      // If the same link is clicked twice, just close the display
      if (publicHelpDisplay.isOpen && _.isEqual(publicHelpPrevious, params)) {
        publicHelpDisplay.close();
        return;
      }
      publicHelpDisplay.close();
    }
    publicHelpPrevious = _.cloneDeep(params);
    publicHelpDisplay = CRM.publicAlert(ajax ? '...' : params, title, 'crm-help ' + (ajax ? 'crm-msg-loading' : 'info'), {expires: 0});
    if (ajax) {
      if (!url) {
        url = CRM.url('civicrm/ajax/inline');
        params.class_name = 'CRM_Core_Page_Inline_Help';
        params.type = 'page';
      }
      $.ajax(url, {
        data: params,
        dataType: 'html',
        success: function (data) {
          $('#crm-public-notification-container .crm-help .notify-content:last').html(data);
          $('#crm-public-notification-container .crm-help').removeClass('crm-msg-loading').addClass('info');
        },
        error: function () {
          $('#crm-public-notification-container .crm-help .notify-content:last').html('Unable to load help file.');
          $('#crm-public-notification-container .crm-help').removeClass('crm-msg-loading').addClass('error');
        }
      });
    }
  };

  /**
   * @see https://wiki.civicrm.org/confluence/display/CRMDOC/Notification+Reference
   */
  CRM.publicAlert = function (text, title, type, options) {
    type = type || 'alert';
    title = title || '';
    options = options || {};
    if ($('#crm-public-notification-container').length) {
      var params = {
        text: text,
        title: title,
        type: type
      };
      // By default, don't expire errors and messages containing links
      var extra = {
        expires: (type == 'error' || text.indexOf('<a ') > -1) ? 0 : (text ? 10000 : 5000),
        unique: true
      };
      options = $.extend(extra, options);
      options.expires = (options.expires === false || !CRM.config.allowAlertAutodismissal) ? 0 : parseInt(options.expires, 10);
      if (options.unique && options.unique !== '0') {
        $('#crm-public-notification-container .ui-notify-message').each(function () {
          if (title === $('h1', this).html() && text === $('.notify-content', this).html()) {
            $('.icon.ui-notify-close', this).click();
          }
        });
      }
      return $('#crm-public-notification-container').notify('create', params, options);
    }
    else {
      if (title.length) {
        text = title + "\n" + text;
      }
      // strip html tags as they are not parsed in standard alerts
      alert($("<div/>").html(text).text());
      return null;
    }
  };

  var elements = $('a.helpicon');
  for (var i = 0; i < elements.lenth; i++) {
    var onclick = elements[i].attr('onclick');
    console.log(onclick);
  }
}(CRM, CRM.$);
