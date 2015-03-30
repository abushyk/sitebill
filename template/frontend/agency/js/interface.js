$(document).ready(function(){
	$('#_lang').change(function(){
		$(this).parents('form').eq(0).submit();
	});
	
	$('#prettyLogin').on('hidden', function () {
		$(this).find('[type=text]').val('');
		$(this).find('[type=password]').val('');
		$(this).find('.error_mark').hide();
		$(this).find('.error').hide();
	})
	
	$('#prettyLogin #login_button').click(function(){
		var form=$(this).parents('form').eq(0);
		var login=form.find('[name=login]').val();
		var password=form.find('[name=password]').val();
		var rememberme=form.find('[name=rememberme]').prop('checked');
		var errorblock=form.find('.error');
		errorblock.hide();
		
		if(rememberme){
			rememberme=1;
		}else{
			rememberme=0;
		}
		
		
		if(login=='' || password==''){
			errorblock.show();
		}else{
			$.getJSON(estate_folder+'/js/ajax.php?action=ajax_login',{login:login, password:password, rememberme:rememberme},function(data){
				if(data.response.body=='Authorized'){
					document.location.href=document.location.href;
				}else{
					errorblock.show();
				}
			});
		}
		
		return false;
	});
	
	
	$('#prettyLogin #register_button').click(function(){
		var errors=false;
		
		var form=$(this).parents('form').eq(0);
		var errormsg=form.find('.error');
		var errorblock=form.find('.error_mark');
		var els=form.find('div.el:has(span.required)');
		errorblock.hide();
		errormsg.text('').hide();
		form.find('div.el:has(span.required)').each(function(){
			var field=$(this).find('input');
			if(field.val()==''){
				errors=true;
				$(this).find('.error_mark').show();
			}
			
		});
		
		var login=form.find('input[name=login]').val();
		
		if(login!=''){
			var re = /^([a-zA-Z0-9-_@\.]*)$/i;
			found = login.match(re);
			if(found===null){
				errors=true;
				errormsg.append($('<p>Логин может содержать только латинские буквы, цифры, подчеркивание, тире</p>')).show();
			}
		}
		


		var password=form.find('input[name=newpass]').val();
		var password_retype=form.find('input[name=newpass_retype]').val();
		if(password && password_retype){
			if(password!='' && password_retype!='' && password!=password_retype){
				errors=true;
				errormsg.append($('<p>Пароли не совпадают</p>')).show();
				form.find('input[name=newpass]').nextAll('.error_mark').eq(0).show();
				form.find('input[name=newpass_retype]').nextAll('.error_mark').eq(0).show();
			}
		}
		var email=form.find('input[name=email]').val();
		if(email!='' && !SitebillCore.isValidEmail(email)){
			errors=true;
			errormsg.append($('<p>Укажите правильный email</p>')).show();
		}
		
		
		
		if(!errors){
			var data=[];
			form.find('div.el').each(function(){
				var field=$(this).find('input').each(function(){
					data.push($(this).attr('name')+'='+$(this).val());
				});
				var field=$(this).find('select').each(function(){
					data.push($(this).attr('name')+'='+$(this).val());
				});
				
				
			});
			$.ajax({
				type: 'post',
				url: estate_folder+'/js/ajax.php?action=ajax_register',
				data: data.join('&'),
				success: function(text){
					if(text!='ok'){
						errormsg.append($('<p>'+text+'</p>')).show();
					}else{
						
						$('#prettyLogin').modal('hide');
						$('#prettyRegisterOk').modal('show');
					}
				}
			});
			
		}
		return false;
	});
	
	$('#prettyRegisterOk .let_me_login').click(function(){
		$('#prettyRegisterOk').modal('hide');
		$('#prettyLogin a[href="#profile"]').tab('show');
		$('#prettyLogin').modal('show');
	});
	
	if($('#search_forms_tabs').length>0){
		var active=$('#search_forms_tabs li.active');
		if(active.length==0){
			var first=$('#search_forms_tabs li:first');
			var idn=first.find('a').attr('href').replace('#','');
			first.addClass('active');
			$('#'+idn).addClass('active');
		}
	}
	
	var wh=$(window).height();
	$('#prettyLogin .tab-content').css({'max-height': (0.55*wh)+'px'});
});