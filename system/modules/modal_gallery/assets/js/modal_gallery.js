var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
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

function triggerModal(id) {
  
  console.log("trigger_id: " + id);
  var hoverInv = document.getElementsByClassName("modal_wrapper");
  for (var i=0; i<hoverInv.length; i++){
       hoverInv[i].style.display = "none";
  }
  
  var modal = document.getElementById("modal_"+id);
    modal.style.display = "block";
  return;
}

// close the modal when the X is clicked
function closeModal(id) {
  
  console.log("close_id: " + id);
  var hoverInv = document.getElementsByClassName("modal_wrapper");
  for (var i=0; i<hoverInv.length; i++){
       hoverInv[i].style.display = "none";
  }
  return;
}
