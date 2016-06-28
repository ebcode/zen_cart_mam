<html>
    <head>
        <script src="./includes/templates/bookshelf/jscript/jquery-latest.min.js"></script>
    </head>
    <body>
        
<div style="float:left;clear:left;">.</div>
<textarea name="msg_26_0" style=""></textarea>
<br>
recipient's phone:
<font size="smaller" color="red">*</font>
<input type="text" value="" name="phn_26_0">

        <input type="button" onclick="process_form()">
        <span id="log"></span>
    </body>
    <script>
        $(document).ready(function(){
         process_form = function(){
             submit = {}; 
            $('textarea').each(function(){
                    submit[this.name] = this.value;
                   //submit.push({nm:vl}); 
            });
            $('input:text').each(function(){
                submit[this.name] = this.value;
               //submit.push({nm:vl}); 
            });
            
            request = $.ajax({type:'POST', url:'./save_msg.php', data:submit, async:true});
            
            request.done(function( msg ) {
$( "#log" ).html( msg );
});         
            request.fail(function( jqXHR, textStatus ) {
alert( "Request failed: " + textStatus );
});

         }
        });
    </script>
</html>