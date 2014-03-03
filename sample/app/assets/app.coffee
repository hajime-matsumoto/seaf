$ ->
  ###
  $(".naver a").on "click",(e)->
    e.preventDefault()
    e.stopPropagation
  $(".naver").navar()
  ###
  
  $(".scroll").click (e)->

    speed = 500
    href = $(this).attr("href")
    parts=href.split '#'
    target = $("#"+parts[1])

    if target.length != 0
      e.preventDefault()
      e.stopPropagation()
    else
      return true
    position = target.offset().top

    $("html, body").animate
      scrollTop: position 
    , speed, "swing"
    false

  $("nav.naver").naver
    labels:
      closed: "MENU"

