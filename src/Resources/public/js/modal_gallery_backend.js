document.addEventListener("DOMContentLoaded", function() {

    // Only run on the slide edit form
    var legend = document.getElementById("pal_slide_legend");
    if (legend == null)
        return;

    var lastSrc = null;

    function updateHelper()
    {
        // Contao 5 puts the file path in the preview's alt (it used to be the title)
        var preview = legend.querySelector("#ctrl_slide_image ~ .selector_container img.gimage");
        var src = (preview != null) ? preview.alt : "";

        // Skip if the selected image hasn't changed
        if (src == lastSrc)
            return;
        lastSrc = src;

        var deleteOld = document.getElementById("modal_helper");
        if (deleteOld != null)
            deleteOld.remove();

        // Nothing selected yet, so there is nothing to click on
        if (src == "")
            return;

        legend.insertAdjacentHTML("afterend", "<div id='modal_helper' class='clr widget' style='padding-top:5px;'><h3>Modal Coordinate Helper</h3><img id='hotspot_image' alt=''><br><p class='tl_help tl_tip'>Click on the image to get X/Y coordinates in percentages, use them below for where you'd like the modal link to show.</p><div class='clicked_wrapper'><p><strong>Clicked Hotspot X:</strong><span id='x'></span></p><p><strong>Clicked Hotspot Y:</strong><span id='y'></span></p></div></div>");

        var myImg = document.getElementById("hotspot_image");
        myImg.src = src;
        myImg.onmousedown = GetCoordinates;
    }

    // Draw the helper for the saved image, then redraw whenever the picker swaps the preview
    updateHelper();
    new MutationObserver(updateHelper).observe(legend, { childList: true, subtree: true });
});

function GetCoordinates(e)
{
    // Measure against the rendered image so scrolling and scaling don't skew the result
    var rect = e.currentTarget.getBoundingClientRect();

    document.getElementById("x").innerHTML = " " + Math.trunc(((e.clientX - rect.left) / rect.width) * 100);
    document.getElementById("y").innerHTML = " " + Math.trunc(((e.clientY - rect.top) / rect.height) * 100);
}
