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
		if (n > slides.length) {slideIndex = 1}
		if (n < 1) {slideIndex = slides.length}
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
		}
		for (i = 0; i < dots.length; i++) {
			dots[i].className = dots[i].className.replace(" active", "");
			dots[i].parentElement.className = dots[i].parentElement.className.replace(" active", "");
		}
		slides[slideIndex-1].style.display = "block";
		dots[slideIndex-1].className += " active";
		dots[slideIndex-1].parentElement.className += " active";
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

	var modal = document.getElementById("modal_"+id);
	modal.style.display = "block";
	
	
	// ADJUSTING OVERFLOW POSITIONING
	// our compiled width and height
	var modalWidth = (modal.offsetLeft + modal.scrollWidth);
	var modalHeight = (modal.offsetTop + modal.scrollHeight);

	// tell if outside width
	if(modalWidth > modal.parentElement.clientWidth )
	{
		var offsetWidth = (modalWidth - modal.parentElement.clientWidth);
		var overX = Math.trunc(((modal.offsetLeft - offsetWidth) / modal.parentElement.clientWidth) * 100);
		modal.style.left = (overX-1) + "%";
	}
		
	// tell if outside height
	if(modalHeight > modal.parentElement.clientHeight )
	{
		var offsetHeight = (modalHeight - modal.parentElement.clientHeight);
		var overY = Math.trunc(((modal.offsetTop - offsetHeight) / modal.parentElement.clientHeight) * 100);
		modal.style.top = (overY-1) + "%";
	}
	
	// add onclick function to modals_container
	var modals_container = document.getElementsByClassName("modals_container");
	modals_container.onclick = closeModal(999);
	
	return;
}

// close the modal when the X is clicked
function closeModal(id) {
	
	if(id==999)
	{
		var modals_container = document.getElementsByClassName("modals_container");
		modals_container.onclick = "";
	}
	
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
