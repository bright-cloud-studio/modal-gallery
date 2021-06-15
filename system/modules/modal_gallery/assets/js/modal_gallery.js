// Modal Gallery JS
var slideIndex = 1;
showSlides(slideIndex);


function plusSlides(n) {
	showSlides(slideIndex += n);
}

function currentSlide(n) {
	showSlides(slideIndex = n);
}

function showSlides(n) {
	var noOpenModals = true;
	
	var hoverInv = document.getElementsByClassName("modal_wrapper");
		for (var i=0; i<hoverInv.length; i++){
			console.log("id_" + i + ": " + hoverInv[i].style.display);
			if(hoverInv[i].style.display == "block")
				noOpenModals = false;
		}
	if(noOpenModals == true) {
		var i;
		var slides = document.getElementsByClassName("mySlides");
		var dots = document.getElementsByClassName("demo");
		var captionText = document.getElementById("caption");
		if (n > slides.length) {slideIndex = 1}
		if (n < 1) {slideIndex = slides.length}
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
		}
		for (i = 0; i < dots.length; i++) {
			dots[i].className = dots[i].className.replace(" active", "");
		}
		slides[slideIndex-1].style.display = "block";
		dots[slideIndex-1].className += " active";
		captionText.innerHTML = dots[slideIndex-1].alt;
	}
}

function triggerModal(id) {

	//make all images opaque by half
	var i, j;
	var slideImageContainer = document.getElementsByClassName("main_container");
	for (i = 0; i < slideImageContainer.length; i++) {
		var slideImages = slideImageContainer[i].getElementsByTagName("img");
		for (j = 0; j < slideImages.length; j++) {
			slideImages[j].style.opacity = 0.5;
		}
	}

	console.log("trigger_id: " + id);
	var hoverInv = document.getElementsByClassName("modal_wrapper");
	for (var i=0; i<hoverInv.length; i++){
		hoverInv[i].style.display = "none";
	}
	
	// if the modal is outside the parent push it back inside
	
	// get the divs position
	
	function isOverflown(element) {
		return element.scrollHeight > element.clientHeight || element.scrollWidth > element.clientWidth;
	}

	var els = document.getElementById("modal_"+id);
	for (var i = 0; i < els.length; i++) {
		var el = els[i];
		el.style.borderColor = (isOverflown(el) ? 'red' : 'green');
		console.log("Element #" + i + " is " + (isOverflown(el) ? '' : 'not ') + "overflown.");
	}
	
	
	// get the parent position
	
	// if outside move it back inside
	

	var modal = document.getElementById("modal_"+id);
	modal.style.display = "block";

	return;
}

// close the modal when the X is clicked
function closeModal(id) {
	// remove all opacity
	var i, j;
	var slideImageContainer = document.getElementsByClassName("main_container");
	for (i = 0; i < slideImageContainer.length; i++) {
		var slideImages = slideImageContainer[i].getElementsByTagName("img");
		for (j = 0; j < slideImages.length; j++) {
			slideImages[j].style.opacity = 1;
		}
	}


	console.log("close_id: " + id);
	var hoverInv = document.getElementsByClassName("modal_wrapper");
	for (var i=0; i<hoverInv.length; i++){
		hoverInv[i].style.display = "none";
	}
	return;
}
