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
  
  
  //make all images opaque by half
  var i, j;
  var slideImageContainer = document.getElementsByClassName("main_container");
  for (i = 0; i < slideImageContainer.length; i++) {
    var slideImages = slideImageContainer[i].getElementsByTagName("image");
       for (j = 0; j < slideImageContainer.length; j++) {
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
  return;
}

// close the modal when the X is clicked
function closeModal(id) {
  
    //make all images opaque by half
  var i, j;
  var slideImageContainer = document.getElementsByClassName("main_container");
  for (i = 0; i < slideImageContainer.length; i++) {
    var slideImages = slideImageContainer[i].getElementsByTagName("image");
       for (j = 0; j < slideImageContainer.length; j++) {
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
