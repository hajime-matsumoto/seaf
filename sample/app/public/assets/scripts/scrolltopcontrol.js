$(function(){
	$("a[href^=#]").click(function(){
		var Hash = $(this.hash);
		var HashOffset = $(Hash).offset().top;
		$("html,body").animate({
			scrollTop: HashOffset
		}, 788);
		return false;
	});
});
