// The Contao 5 back end navigates with Turbo, so DOMContentLoaded only fires on a full page load
document.addEventListener("DOMContentLoaded", initHelper);
document.addEventListener("turbo:load", initHelper);

/**
 * Coordinate helper for the slide edit form.
 *
 * Flow:
 * 1. Insert the helper at the top of the Hotspot section, above the hotspot rows
 * 2. Show the image selected in Slide Image, redrawing whenever the picker changes it
 * 3. Track the selected hotspot row; clicking the image writes X/Y into that row
 * 4. Draw a numbered marker for every row that has coordinates, kept in sync as rows change
 */
function initHelper()
{
    // Only run on the slide edit form, and only once per page
    var slideLegend = document.getElementById("pal_slide_legend");
    var hotspotLegend = document.getElementById("pal_hotspot_legend");
    if (slideLegend == null || hotspotLegend == null || hotspotLegend.dataset.helperInit)
        return;
    hotspotLegend.dataset.helperInit = "1";

    // Sit outside the widget group so redrawing the helper doesn't retrigger the row observer
    var widgets = hotspotLegend.querySelector(".widget-group");
    var helper = document.createElement("div");
    helper.id = "modal_helper";
    helper.className = "clr widget";
    widgets.parentNode.insertBefore(helper, widgets);

    var lastSrc = null;
    var activeRow = null;

    function getRows()
    {
        return Array.from(hotspotLegend.querySelectorAll("#ctrl_hotspot_links > tbody > tr"));
    }

    function getField(row, key)
    {
        return row.querySelector("input[name$='[" + key + "]']");
    }

    function readPercent(input)
    {
        var value = (input != null) ? parseInt(input.value, 10) : NaN;
        return (isNaN(value) || value < 0 || value > 100) ? null : value;
    }

    function updateImage()
    {
        // Contao 5 puts the file path in the preview's alt (it used to be the title)
        var preview = slideLegend.querySelector("#ctrl_slide_image ~ .selector_container img.gimage");
        var src = (preview != null) ? preview.alt : "";

        // Skip if the selected image hasn't changed
        if (src == lastSrc)
            return;
        lastSrc = src;

        // Nothing selected yet, so there is nothing to click on
        if (src == "")
        {
            helper.innerHTML = "";
            return;
        }

        helper.innerHTML = "<h3>Modal Coordinate Helper</h3><div class='mg_stage'><img id='hotspot_image' alt=''><div class='mg_markers'></div></div><p class='tl_help tl_tip'>Click a hotspot row below to select it, then click the image to place it. The X and Y fields fill in automatically.</p><p class='mg_readout'></p>";

        var img = helper.querySelector("#hotspot_image");
        img.src = src;
        img.onmousedown = placeHotspot;

        updateMarkers();
    }

    function updateMarkers()
    {
        var rows = getRows();

        // Default to the first row still missing coordinates, so a new slide is ready to click
        if (activeRow == null || !activeRow.isConnected)
            activeRow = rows.find(function(row) { return readPercent(getField(row, "hotspot_x")) == null; }) || rows[0] || null;

        var html = "";
        rows.forEach(function(row, i) {
            row.classList.toggle("mg_active", row == activeRow);

            var x = readPercent(getField(row, "hotspot_x"));
            var y = readPercent(getField(row, "hotspot_y"));
            if (x == null || y == null)
                return;

            html += "<span class='mg_marker" + (row == activeRow ? " mg_active" : "") + "' style='left:" + x + "%;top:" + y + "%'>" + (i + 1) + "</span>";
        });

        var layer = helper.querySelector(".mg_markers");
        if (layer != null)
            layer.innerHTML = html;
    }

    function placeHotspot(e)
    {
        if (activeRow == null)
            return;

        // Measure against the rendered image so scrolling and scaling don't skew the result
        var rect = e.currentTarget.getBoundingClientRect();
        var x = Math.min(100, Math.trunc(((e.clientX - rect.left) / rect.width) * 100));
        var y = Math.min(100, Math.trunc(((e.clientY - rect.top) / rect.height) * 100));

        setValue(getField(activeRow, "hotspot_x"), x);
        setValue(getField(activeRow, "hotspot_y"), y);

        helper.querySelector(".mg_readout").textContent = "Hotspot " + (getRows().indexOf(activeRow) + 1) + " placed at X " + x + ", Y " + y;
        updateMarkers();
    }

    function setValue(input, value)
    {
        input.value = value;
        input.dispatchEvent(new Event("change", { bubbles: true }));
    }

    // Select a row when anything in it is clicked or focused
    function selectRow(e)
    {
        var row = e.target.closest("#ctrl_hotspot_links > tbody > tr");
        if (row != null && row != activeRow)
        {
            activeRow = row;
            updateMarkers();
        }
    }
    hotspotLegend.addEventListener("focusin", selectRow);
    hotspotLegend.addEventListener("click", selectRow);

    // Move markers as X/Y are typed
    hotspotLegend.addEventListener("input", updateMarkers);

    // Draw the helper for the saved image, then follow picker changes and added/removed rows
    updateImage();
    new MutationObserver(updateImage).observe(slideLegend, { childList: true, subtree: true });
    new MutationObserver(updateMarkers).observe(widgets, { childList: true, subtree: true });
}
