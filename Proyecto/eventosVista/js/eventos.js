$(document).ready(function(){
    $('#CalendarioWeb').fullCalendar({
        header:{
            left:'title ',
            right:' today,prev,next'
        },
        dayClick:function(date, jsEvent, view){
            window.location.href = "./registroEvento.php?date="+ date.format('Y-m-d\TH:i');
        },
        events: "./eventosJSON.php",
        
        eventClick:function(calEvent, jsEvent, view){
            var id = calEvent.id;
            window.location.href = "./evento.php?id="+id;
        }
    });
});