var searchUserUrl = prefix + "://" + host + "/api/userInfo/";

function searchUser(){
	var toSearch = $(".searchInput").val();
	var url = searchUserUrl + "?username=" + toSearch;
	var x = $(".searchInput").offset().left;
	var y = $(".searchInput").offset().top + $(".searchInput").outerHeight();
	var width = $(".searchInput").outerWidth();
	var element = '<div class="searchResults" style="\
		width: ' + width + 'px;\
		top: ' + y + 'px;\
		left: ' + x + 'px;">\
		</div>';
	$.get(url, function(data){
		$("body").append(element);
		if(data.response.hasData){
			text = data.response.username;
			var userUrl = prefix + "://" + host + "/u/" + text;
			var resultEl = '<a href="' + userUrl +'" target="_blank"><div class="searchResult"><img class="searchProfileImage" src="' + data.response.profileImageUrl + '"/>' + text + '</div></a>';
		}else{
			var resultEl = '<div class="searchResult">Sorry this user does not exist. :(</div>';
		}
		$(".searchResults").append(resultEl);
	});
}

$(function(){

	$(".searchBtn").click(function(){
		searchUser();
	});

	$(".searchInput").keypress(function(e){
		if(e.which == 13) searchUser();
	});

	$(".searchInput").change(function(){
		$(".searchResults").remove();
	});

});
