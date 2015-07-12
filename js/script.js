$(document).ready(function(){
	$("#unban").hide()
	$("#rew").hide()
	$("#zakyp").hide()
	$("#search_steam").hide()
	$("#login_buy").hide()
	$("#tags").hide()
	//Диалоги начало
	$("#open_unban").click(function(){
		$( "#unban" ).dialog({
		  resizable: true,
		  title: 'Копируйте полный ID из нашего бан листа',
		  width: 900,
		  maxHeight: 600,
		  modal: true,
		  buttons: {
			Cancel: function() {
			  $( this ).dialog( "close" );
			}
		  }
		});
		$("#rew").hide()
		$("#zakyp").hide()
	});
	$("#open_rew").click(function(){
		$( "#rew" ).dialog({
		  resizable: true,
		  width: 550,
		  modal: true,
		  buttons: {
			Cancel: function() {
			  $( this ).dialog( "close" );
			}
		  }
		});
		$("#unban").hide()
		$("#zakyp").hide()
	});
	$("#open_zakyp").click(function(){
		$( "#zakyp" ).dialog({
		  resizable: true,
		  width: 550,
		  modal: true,
		  buttons: {
			Cancel: function() {
			  $( this ).dialog( "close" );
			}
		  }
		});
		$("#rew").hide()
		$("#unban").hide()
	});
	$("#open_tags").click(function(){
		$("#tags").dialog({
		  resizable: true,
		  width: 550,
		  modal: true,
		  buttons: {
			Cancel: function() {
			  $( this ).dialog( "close" );
			}
		  }
		});
		$("#rew").hide()
		$("#unban").hide()
	});
	//Диалоги конец
 	$("#open_sid").click(function(){
	if($("#sid").val() == ""){
		alert('Не заполнено поле');
	}
	else {
		$.ajax({
			type:"POST",
			url:"ajax.php",
			data: "sid="+$("#sid").val()+"&sid_return=1",
			success:function(result) {		
				$("#search_steam").show()			
				$("#search_steam").html(result);
				console.log(result);
			}
		});
	}
	});	
 	$("#login_submit").click(function(){
		$("#login_buy").show()
		$.ajax({
			type:"POST",
			url:"ajax.php",
			data: "login_name="+$("#login_name").val()+"&login_pass="+$("#login_pass").val()+"&renewal=1",
			success:function(result) {
				$("#status").html(result);
				console.log(result);
			}
		});		
	});	


	
	$("#submit_form").click(function(){
	if(
		$("#server_id :selected").val() == "" ||
		$("#username").val() == "" ||
		$("#pass").val() == "" ||
		$("#vk").val() == ""	
		){
		alert('Заполните все поля');
	}
	else {
		$.ajax({
			type:"POST",
			url:"ajax.php",
			data: "server_id="+$("#server_id").val()+"&cost="+$("#cost").val()+"&username="+$("#username").val()+"&pass="+$("#pass").val()+"&vk="+$("#vk").val()+"&date="+$("#date").val()+"&shop=1",
			success:function(result) {
				console.log(result);
				$("#res_zakyp").show();				
				$("#zakyp .table").hide();
				$("#res_zakyp").html(result);
				
			}
		});
	}
	});
	$("#back").click(function(){
		$("#res_zakyp").hide();
		$("#zakyp .table").show();
	});
	$("#cost").change(function(){
		$("#OutSum").val($("#cost").val());
	});
});