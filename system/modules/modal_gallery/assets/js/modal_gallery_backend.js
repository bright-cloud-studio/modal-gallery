document.addEventListener("DOMContentLoaded", function() {
    //document.getElementById("ctrl_hotspot_links").innerHTML += 
             // "<h3 id='update_helper'>Helper Image Goes Here</h3>";


});

var intervalId = window.setInterval(function(){
    
    var picture = document.getElementsByClassName("gimage");
    var toAppend = document.getElementById("pal_slide_legend");
    var title = picture[0].title;
    var src = title.substr(0,title.indexOf(' ')); 
    
    if(src != '') {

       toAppend.insertAdjacentHTML("afterend", "<img id='hotspot_image' src='" + src + "' width='600px'><br><p>X:<span id='x'></span></p><p>Y:<span id='y'></span></p>");
        
        var myImg = document.getElementById("hotspot_image");
        myImg.onmousedown = GetCoordinates;
        
        //stop running once we have found our image
        clearInterval(intervalId) 
    }
}, 1000);



function FindPosition(oElement)
{
  if(typeof( oElement.offsetParent ) != "undefined")
  {
    for(var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent)
    {
      posX += oElement.offsetLeft;
      posY += oElement.offsetTop;
    }
      return [ posX, posY ];
    }
    else
    {
      return [ oElement.x, oElement.y ];
    }
}

function GetCoordinates(e)
{
  var PosX = 0;
  var PosY = 0;
  var ImgPos;
  var myImg = document.getElementById("hotspot_image");
  ImgPos = FindPosition(myImg);
  if (!e) var e = window.event;
  if (e.pageX || e.pageY)
  {
    PosX = e.pageX;
    PosY = e.pageY;
  }
  else if (e.clientX || e.clientY)
    {
      PosX = e.clientX + document.body.scrollLeft
        + document.documentElement.scrollLeft;
      PosY = e.clientY + document.body.scrollTop
        + document.documentElement.scrollTop;
    }
  PosX = PosX - ImgPos[0];
  PosY = PosY - ImgPos[1];
    
  document.getElementById("x").innerHTML = (PosX / myImg.width) * 100;
  document.getElementById("y").innerHTML = (PosY / myImg.height) * 100;;
}
