<?php
//Multiple Addresses Mod
?>
 
<script>
//just need to check that all select boxes have values with $ signs

window.onload=function(){

the_form = document.forms['multiple_shipments_form'];

the_form.onsubmit=function(){
	
	var error=0;
	for(i=0;i<the_form.length;i++){
		
		inp = the_form[i];
		
		try{
		
			if(inp.name.indexOf('shipping')==0){
				
				if (inp.value==''){  //no value
					error=1;
				}
				
			}
			
		} catch(err){}
 
	} 
	
	if(error){
		alert('<?php echo ERROR_MULTIPLE_SHIPMENTS_SELECT; ?>')
		return false;
	} else {
		return true;
	}
	
}

}

</script>