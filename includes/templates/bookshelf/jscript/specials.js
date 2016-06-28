$(document).ready(function(){
    get_quad = function(pt){
        q = '';
        if(pt.pageX > $(window).width()/2){

            if((pt.pageY-$(window).scrollTop()) > $(window).height()/2){
                q = 'br';
            } else {
                q = 'ur';
            }
            
        } else {

            if((pt.pageY-$(window).scrollTop()) > $(window).height()/2){
                q = 'bl';
            } else {
                q = 'ul';
            }
            
        }

        return q;
    };
    
    $('.displayitem').mouseenter(function(e){
          //alert(e.pageX+','+(e.pageY-$(window).scrollTop()));
           
            //alert($('#'+this.id+'_description').html());
            $('#abox').html($('#'+this.id+'_description').html());
                  
            q = get_quad(e);
            
            pX=e.pageX;
            pY=e.pageY;
            add_offset=0;

            if(q=='bl'){
                pX+=40;
                pY-=440;
                add_offset = 202;
            }
            if(q=='br'){
               pX-=400;
                pY-=440;
                //add_offset = -500;
                add_offset = -$('#abox').width()-14;
            }
            if(q=='ul'){
                pX+=40;
                pY+=20;
                add_offset = 202;
            }
            if(q=='ur'){
                pX-=400;
                pY+=20;
                //add_offset = -500;
                add_offset = -$('#abox').width()-14;
            }
           
          
             //$('#item_button_'+this.id+' input').fadeIn(200);
             // $('#button_'+this.id).attr({'style':'display:none'});
             //$('#button_'+this.id).fadeIn(200);
             
$('#'+this.id).parent().parent().parent().find('input').fadeIn(200);
             
             
            
            //display:none
             
            if($('#abox').css('display') == 'none'){
                $('#abox').attr({'style':'top:'+this.offsetTop+'px; left:'+(this.offsetLeft+add_offset)+'px; position:absolute; border:1px solid black; background-color:#fff; padding:6px; display:none;'});
                $('#abox').fadeIn(200);
             }
             /*
            $('#abox').attr({'style':'top:'+pY+'px; left:'+pX+'px; position:absolute;z-index:1;border:1px solid black; background-color:#fff; padding:5px;'});
            */
           
           //alert('why do you suck?');
    }); 
    
    $('.displayitem').mouseleave(function(e){     
        //console.log(e.pageX + ',' + e.pageY);
         p = $(this).position();
         //console.log(p.left + ',' + (p.top+280));
         
        //$('#'+this.id+' input').fadeOut(200);
        //$('#'+this.id).parent().parent().parent().find('input').fadeOut(200);
        
        if( ((e.pageX > p.left + 10) && (e.pageX < (p.left + 200))) &&
            ((e.pageY > p.top +5) && (e.pageY < (p.top + 280)))  ){
        
        } else {
            $('#'+this.id).parent().parent().parent().find('input').fadeOut(200);
             if($('#abox').css('display') != 'none'){
             $('#abox').fadeOut(200);
              $('#abox').attr({'style':'display:none'});
             }
        }
        //$('#abox').attr({'style':'display:none'});
    });
});