$("select").select2();

let plusClickCount = 0;
$(".fa-plus-circle").on("click", function(){
	plusClickCount = (++plusClickCount)%2; 
	console.log($(this).siblings("ol"));
	if(plusClickCount) {
		$(this).siblings("ol").show();
	} else {
		$(this).siblings("ol").hide();
	}
});