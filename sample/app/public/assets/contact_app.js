// Generated by CoffeeScript 1.7.1
(function() {
  $(document).ready(function() {
    var el, validator, viewModel;
    el = $('#contact_form');
    validator = el.kendoValidator().data('kendoValidator');
    viewModel = kendo.observable({
      name: "hajime",
      mail: "mail@hazime.org",
      message: "test",
      submit: function() {
        return false;
      },
      sendMessage: function(e) {
        var formContent;
        e.stopPropagation();
        e.preventDefault();
        if (validator.validate()) {
          formContent = el.serialize();
          return $.ajax({
            type: 'PUT',
            url: MMIZUI.url + '/sendMail',
            data: formContent,
            success: function(res) {
              return console.debug(res);
            }
          });
        }
      }
    });
    return kendo.bind(el, viewModel);
  });

}).call(this);
