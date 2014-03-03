$ ->
  SyntaxHighlighter.all()
  #$("#menu-toggle").click ->
  #  if $("#menu-container").is(":hidden")
  #    $("#menu-container").slideDown()
  #  else
  #    $("#menu-container").slideUp()

  $("a[href^=#]").click ->
    speed = 500
    href = $(this).attr("href")
    target = $((if href is "#" or href is "" then "html" else href))
    position = target.offset().top

    if $("#menu-toggle").is(":hidden")
      position = position - 40
    else
      position = position - 40


    $("html, body").animate
      scrollTop: position 
    , speed, "swing"
    false

  if $("#menu-toggle").is(":hidden")
    box = $("#menu-container")
  else
    box = $("#menu aside")

  boxTop = box.offset().top
  $(window).scroll ->
    if $(window).scrollTop() >= boxTop
      box.addClass "fixed"
      $("body").css "margin-top", "40px"
    else
      box.removeClass "fixed"
      $("body").css "margin-top", "0px"
    return

  return
