var FontResize = {
	minimalFontSize: 10,
};
/*
FontResize.countCapitals = function(string){
	var capitals = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
	var regex = new RegExp(capitals.join('|'), "g");
	return string.match(regex).length;
}

FontResize.getFontSize = function(string, elementWidth){
	var textLength = string.length;
	var capitalsCount = FontResize.countCapitals(string);
	var fontSize = elementWidth/textLength;
	var multiplier = 1 + ((textLength - capitalsCount)/textLength);
	return fontSize*multiplier;
}
*/
FontResize.resize = function(){
	var elements = $(".fontResize");
	for(var i = 0; i < elements.length; i++){
		var element = $(elements[i]);
		parentElement = $(element).parent();
		if(element.outerWidth() > parentElement.outerWidth()){
			var parentWidth = parentElement.outerWidth();
			var newFontSize = FontResize.minimalFontSize;
			element.css("fontSize", newFontSize + "px");
			while(element.width() + 50 < parentWidth){
				newFontSize += 1;
				element.css("fontSize", newFontSize + "px");
			}
			if(newFontSize == FontResize.minimalFontSize) element.css("wordWrap", "break-word");
		}
	}
};

$(function(){
	FontResize.resize();
});

$(window).on("resize", function(){
	FontResize.resize();
});