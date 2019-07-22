function jsUcfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//functions to run when document is finished loading
jQuery(document).ready(function () {
    jQuery("#launchbutton").css({
        "font-size": "30px",
        "text-align": "center"

    });
    jQuery("#buttondiv").css({
        "text-align": "center"

    });


    dataToCells(launchdata);
});

//button click function
jQuery("#launchbutton").click(function () {

    table = jQuery("#launchbutton").text();
    if (table == "Upcoming") {
        dataToCells(launchdata);

        jQuery(this).button({
            label: jsUcfirst(prevlaunchdata[15].type.toString())
        });
        jQuery("#titlediv").html('<h2>' + jsUcfirst(table) + " Launches</h2>");
    } else {
        jQuery(this).button({
            label: jsUcfirst(launchdata[15].type.toString())
        });
        dataToCells(prevlaunchdata);
        jQuery("#titlediv").html('<h2>' + jsUcfirst(table) + " Launches</h2>");

    }


});

//main function to assign data to cells
function dataToCells(data) {
    var slug = 8;
    var start = 9;
    var end = 10;
    var mission = 11;
    var spacecraft = 12;
    var pad = 13;
    var color = 14;
    var column = 1;
    var row = 2;
    var table = launchdata[15].type;

    //assign title correct text
    jQuery("#titlediv").html(jsUcfirst('<h2>' + jsUcfirst(table.toString()) + " Launches</h2>"))
        .css("text-align", "center");

    //loop through cells
    for (row = 2; row <= 30; row++) {
        for (column = 1; column <= 9; column++) {
            if (column == 1) { //launch window & hyperlink to spacelaunch.me 
                jQuery('#tablepress-launches > tbody > tr.row-' + row + ' > td.column-' + column)
                    .html('<a href = "' + data[slug][row - 2] + '" target="_blank" title = "Launch Window: ' + data[start][row - 2] + 'thru ' + data[end][row - 2] + '">' + data[column - 1][row - 2] + '</a>');
            } else if (column == 3) { //mission description & colorize from status.
                jQuery('#tablepress-launches > tbody > tr.row-' + row + ' > td.column-' + column)
                    .html('<p id = "p" title = "' + data[mission][row - 2] + '">' + data[column - 1][row - 2] + '</p>')
                    .css({
                        "background-color": data[color][row - 2],
                        "color": "black"
                    });
            } else if (column == 7) { //spacecraft description
                jQuery('#tablepress-launches > tbody > tr.row-' + row + ' > td.column-' + column)
                    .html('<p id = "p" title = "' + data[spacecraft][row - 2] + '">' + data[column - 1][row - 2] + '</p>');
            } else if (column == 5) { //pad wiki
                jQuery('#tablepress-launches > tbody > tr.row-' + row + ' > td.column-' + column)
                    .html('<a href = "' + data[pad][row - 2] + '" target="_blank">' + data[column - 1][row - 2] + '</a>');
            } else { //no other markup
                jQuery('#tablepress-launches > tbody > tr.row-' + row + ' > td.column-' + column)
                    .html(data[column - 1][row - 2]);
            }
        }
    }

}