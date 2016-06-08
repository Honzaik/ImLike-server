var VertCent = {};

VertCent.center = function(){
	var elementsToVertCent = $(".vertCent");
	for(var i = 0; i < elementsToVertCent.length; i++){
		var elementH = $(elementsToVertCent[i]).height();
		var parentH = $(elementsToVertCent[i]).parent().height();
		if(elementH < parentH){
			var gap = (parentH - elementH)/2;
			$(elementsToVertCent[i]).css("marginTop", gap + "px");
		}
	}
};

$(function(){
	VertCent.center();
});
