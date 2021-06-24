document.addEventListener("DOMContentLoaded", function() {
    //document.getElementById("ctrl_hotspot_links").innerHTML += 
             // "<h3 id='update_helper'>Helper Image Goes Here</h3>";


});

var lastSrc ="";

var intervalId = window.setInterval(function(){
    
    var picture = document.getElementsByClassName("gimage");
    var toAppend = document.getElementById("pal_slide_legend");
    
    var hiddenField = document.getElementById("ctrl_slide_image_url");
    
    var title = picture[0].title;
    var src = title.substr(0,title.indexOf(' ')); 
    
    
    // if we have hiddenField use that, if not do our original process
    
    if(hiddenField.value != '' || hiddenField.value != lastSrc)
    {
        if(lastSrc != hiddenField.value )
        {
            lastSrc = hiddenField.value;
            var deleteOld = document.getElementById("modal_helper");
            if(deleteOld != null)
            deleteOld.remove();
            toAppend.insertAdjacentHTML("afterend", "<div id='modal_helper' class='clr widget' style='padding-top:5px;'><h3>Modal Coordinate Helper</h3><img id='hotspot_image' src='" + hiddenField.value + "' width='600px'><br><p class='tl_help tl_tip'>Click on the image to get X/Y coordinates in percentages, use them below for where you'd like the modal link to show.</p><br><p><strong>Clicked Hotspot_X:</strong><span id='x'></span></p><p><strong>Clicked Hotspot_Y:</strong><span id='y'></span></p></div>");

            var myImg = document.getElementById("hotspot_image");
            myImg.onmousedown = GetCoordinates;
        }
        
    }
    else
    {
    
        if(src != '') {
            if(lastSrc != src )
            {
                lastSrc = src;
                var deleteOld = document.getElementById("modal_helper");
                if(deleteOld != null)
                    deleteOld.remove();
                toAppend.insertAdjacentHTML("afterend", "<div id='modal_helper' class='clr widget' style='padding-top:5px;'><h3>Modal Coordinate Helper</h3><img id='hotspot_image' src='" + src + "' width='600px'><br><p class='tl_help tl_tip'>Click on the image to get X/Y coordinates in percentages, use them below for where you'd like the modal link to show.</p><br><p><strong>Clicked Hotspot_X:</strong><span id='x'></span></p><p><strong>Clicked Hotspot_Y:</strong><span id='y'></span></p></div>");

                var myImg = document.getElementById("hotspot_image");
                myImg.onmousedown = GetCoordinates;

                // add the image full address to the hidden field so we can access it every time
                hiddenField.value = src;


                //stop running once we have found our image
                //clearInterval(intervalId) 
            }
        }
        
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
  document.getElementById("y").innerHTML = (PosY / myImg.height) * 100;
}
