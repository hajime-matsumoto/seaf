$(document).ready ->
  el=$('#contact_form')
  validator = el.kendoValidator().data('kendoValidator')

  viewModel = kendo.observable 
    name: "hajime"
    mail: "mail@hazime.org"
    message: "test"
    submit: -> false
    sendMessage: (e)->
      e.stopPropagation()
      e.preventDefault()

      if(validator.validate())
        formContent = el.serialize()
        $.ajax
          type: 'PUT'
          url: MMIZUI.url+'/sendMail'
          data: formContent
          success: (res)->
            console.debug(res)

  kendo.bind el, viewModel



