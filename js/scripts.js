$(document).ready(function() {
	
	// Function from Jason on Stack Overflow, via this thread:
	// http://stackoverflow.com/questions/985272/jquery-selecting-text-in-an-element-akin-to-highlighting-with-your-mouse
	
	function selectText(element) {
	    var doc = document;
	    var text = doc.getElementById(element);    

	    if (doc.body.createTextRange) { // ms
	        var range = doc.body.createTextRange();
	        range.moveToElementText(text);
	        range.select();
	    } else if (window.getSelection) { // moz, opera, webkit
	        var selection = window.getSelection();            
	        var range = doc.createRange();
	        range.selectNodeContents(text);
	        selection.removeAllRanges();
	        selection.addRange(range);
	    }
	}

	$(".content").hide();
	
	$(".learnmore").click(function() {
		$(this).next(".content").slideToggle(400);
	});
	
	$('.copybutton').click(function(){
	    selectText('results');
	  });
	
});