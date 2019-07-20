jQuery(document).ready(function ($) {
console.log();
        var column = 1;
        var row = 2;
       $('#tablepress-2 > tbody > tr.row-' + row + ' > td.column-' + column).text(launchData[column-1][row-2]);

       for (row = 2; row <= 30; row++) {
            for (column = 1; column <= 9; column++) {
                if (column != 9){
                    $('#tablepress-2 > tbody > tr.row-' + row + ' > td.column-' + column).text(launchData[column-1][row - 2]);
                }
                else{
                    $('#tablepress-2 > tbody > tr.row-' + row + ' > td.column-' + column).html('<a href = "' + launchData[column-1][row - 2] + '" target="_blank"> More Info </a>');
                }
            }
        }

});