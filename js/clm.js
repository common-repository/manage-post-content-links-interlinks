jQuery(document).ready(function($) {


	var ajaxcounter = 1 ;




			function drawProgress(percentage,ajaxcounter, time10=1){

				$("#counter").text(ajaxcounter*time10);
				$("#pageprocess").css('width', (parseInt(percentage * 100, 10)) + '%');
			}





	  $("#startsearch").click(function () {

	  	$(this).text("Checking Please wait until the end of the operation") ;
	  	$("#pprocess").show();

	  	var pageprocess = $("#pageprocess") ;
  		var perajax = Number(pageprocess.attr("data-ajax"));
  		var step =  Number(pageprocess.attr("data-step"));
  		drawProgress(.01,ajaxcounter,10);
      doajax_counter(perajax,step);


	  });


      $("body").on("click", '#editclm', function () {

		var  id_clm = $(this).attr("data-id");

		//var atag =$("#atag").val(); // ;
    atag = tinymce.activeEditor.getContent();



		$(this).text("Checking Please wait until the end of the operation") ;

		var pageprocess = $("#pageprocess") ;
		$("#pprocess").show() ;
		var perajax = Number(pageprocess.attr("data-ajax"));
		var step =  Number(pageprocess.attr("data-step"));

       var action = $("input[name='action']:checked").val();




		drawProgress(.01,ajaxcounter,1);

        edit_clm(id_clm,atag,action,perajax,step) ;

        

	})







	function someFunction(site)
{
    return site.replace(/\/$/, "");
}

function edit_clm(id_clm,atag,action,perajax,step)
{

 var data = {
            action: 'clm_edit',
            id_clm : id_clm,
            atag : atag,
            ajaxcounter:ajaxcounter ,
            actionform:action ,
            perajax:perajax


        };


        $.post(the_in_url.in_url, data, function(response) {
            var res = $.parseJSON(response);

              if (res.status==201) {alert(res.error)}
                if (res.status==200)
                {
                  drawProgress( step*ajaxcounter,ajaxcounter,1);
                  if (ajaxcounter >  perajax || action=="siglepost")
                  {
                  drawProgress(1,1,1);

	          	  $("#editclm").text("End of operation") ;
	  	          $("#pageprocess").hide();

                  }


                  if (ajaxcounter <= perajax)
                  {
                  	ajaxcounter=ajaxcounter+1;
                  	if (action=="allpost")
                  	{
                	 edit_clm(id_clm,atag,action,perajax,step) ;

                  	}
                  }

              }


         });

}




function doajax_counter(perajax,step)

{



var data = {
            action: 'clm_search_check',
            ajaxcounter: ajaxcounter

        };

        $.post(the_in_url.in_url, data, function(response) {
            var res = $.parseJSON(response);

            if (res.status==201) {alert(res.error)}
                if (res.status==200)
                {
                  drawProgress( step*ajaxcounter,ajaxcounter,10);

                  //console.log("ajaxcounter:"+ajaxcounter)
                  //console.log("perajax:"+perajax)


             if (ajaxcounter == perajax)
                  {
                  drawProgress(1,ajaxcounter,10);

	          	  $("#startsearch").text("End of operation") ;
	  	          $("#pageprocess").hide();

                  }


                  if (ajaxcounter < perajax)
                  {
                  	ajaxcounter=ajaxcounter+1;
                  	doajax_counter(perajax,step) ;
                  }





                }


         });




}




});
