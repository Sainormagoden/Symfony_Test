function ajaxMeteo(){
    that = $(this);
    $.ajax({
        url:'{{ (path('api_test_meteoX')) }}',
        type: "POST",
        dataType: "json",
        data: {
            "meteo_actuel": "pif"
        },
        async: true,
        success: function (data)
        {
            $('#meteo-ajax').html("");
            for (var i = 0; i < 5; i++){
                $('#meteo-ajax').append( "<tr><td>" + data[i].date.day + " " + data[i].date.monthname + " " + data[i].date.year + "</td>" +
                    "<td>" + data[i].high.celsius + "</td>" +
                    "<td>" + data[i].low.celsius + "</td></tr>");
            }
            console.log("switch");
        }
    });
    return false;

}