{literal}
<script type="text/javascript">
$(document).ready(function(){

	$('.go_up').click(function(){
		var id=$(this).attr('alt');
		var tr=$(this).parents('tr').eq(0);
		$.getJSON(estate_folder+'/js/ajax.php?action=go_up&id='+id,{},function(data){
			if(data.response.body!=''){
				tr.find('td').eq(1).html(data.response.body);
				tr.parents('table').eq(0).find('tr.row3').eq(0).before(tr);
			}
		});
	});


	$('#search_toggle').click(function(){
		$('#search_form_block').toggle();
        $('#srch_date_from').datepicker({dateFormat:'yy-mm-dd'});
        $('#srch_date_to').datepicker({dateFormat:'yy-mm-dd'});
		
	});
	$('#reset').click(function(){
		$(this).parents('form').eq(0).find('input[type=text]').each(function(){
			this.value='';
		});
		$(this).parents('form').eq(0).find('input[type=checkbox]').each(function(){
			this.checked=false;
		});
		$(this).parents('form').submit();
	});
	
	
	$('#grid_control_panel select[name=cp_optype]').change(function(){
		
		var operation=$(this).val();
		if(operation!=''){
			$.ajax({
				url: estate_folder+'/js/ajax.php',
				data: {action: 'get_form_element',element:operation},
				dataType: 'html',
				success: function(html){
					$('#grid_control_panel_content').html(html);
					$('#grid_control_panel button#run').show();
				}
			});
		}
	});
	
	$('#grid_control_panel button#run').click(function(){
		
		var cp=$('#grid_control_panel');
		var action=$(this).attr('alt');
		var operation=cp.find('select[name=cp_optype]').val();
		
		if(operation!=''){
			var field=null;
			if(cp.find('#grid_control_panel_content select').length!=0){
				var field=cp.find('#grid_control_panel_content select');
			}else if(cp.find('#grid_control_panel_content input').length!=0){
				var field=cp.find('#grid_control_panel_content input');
				if(field.attr('type')=='checkbox' && field.is(':checked')){
					field.val('1');
				}
				
			}
			if(field!==null){
				var cat_id=field.val();
			}
			var checked=[];
			$('.grid_check_one:checked').each(function(){
				checked.push(this.value);
			});
			if(checked.length>0){
				window.location.replace(estate_folder+'/admin/index.php?action='+action+'&do=change_param&new_param_value='+cat_id+'&param_name='+operation+'&ids='+checked.join(','));
			}
			
		}
		return false;
	});
	
	$('.batch_update').click(function(){
		var ids=[];
		var action=$(this).attr('alt');
		$(this).parents('table').eq(0).find('input.grid_check_one:checked').each(function(){
			ids.push($(this).val());
		});
		window.location.replace(estate_folder+'/admin/index.php?action='+action+'&do=batch_update&batch_ids='+ids.join(','));
	});
	
	$('.duplicate').click(function(){
		var ids=[];
		var action=$(this).attr('alt');
		$(this).parents('table').eq(0).find('input.grid_check_one:checked').each(function(){
			ids.push($(this).val());
		});
		window.location.replace(estate_folder+'/admin/index.php?action='+action+'&do=duplicate&ids='+ids.join(','));
	});
	$('.tooltipe_block').popover({trigger: 'hover'});	
	
});
	
</script>
{/literal}
<div class="navbar">
  <div class="navbar-inner">
    <div class="container">

    <div class="nav pull-right">
		
{if $admin ne ''}
<div align="right"><a href="#search" id="search_toggle" class="btn btn-info"><i class="icon-white icon-search"></i> {$L_ADVSEARCH}</a></div>

<div id="search_form_block" {if $smarty.request.submit_search_form_block eq ''}style="display:none;"{/if} class="spacer-top">
<form class="form-horizontal" action="?action=data" method="get">
	<div class="control-group">
		<label class="control-label">{$L_WORD}</label>
		<div class="controls">
			<input type="text" name="srch_word" value="{$smarty.request.srch_word}" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">{$L_PHONE}</label>
		<div class="controls">
			<input type="text" name="srch_phone" value="{$smarty.request.srch_phone}" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">{$L_ID}</label>
		<div class="controls">
			<input type="text" name="srch_id" value="{$smarty.request.srch_id}" />
		</div>
	</div>
	<!-- 
	<div class="control-group">
		<label class="control-label">Только экспорт в ЦИАН</label>
		<div class="controls">
			<input type="checkbox" name="srch_export_cian" {if isset($smarty.request.srch_export_cian) && ($smarty.request.srch_export_cian=='on' || $smarty.request.srch_export_cian=='1')} checked="checked"{/if} />
		</div>
	</div>
	  -->
	{if $show_uniq_id}
	<div class="control-group">
		<label class="control-label">UNIQ_ID</label>
		<div class="controls">
			<input type="text" name="uniq_id" value="{$smarty.request.uniq_id}" />
		</div>
	</div>
	{/if}
	<div class="control-group">
		<label class="control-label">{$L_DATE} {$L_FROM}</label>
		<div class="controls">
			<input type="text" name="srch_date_from" id="srch_date_from" value="{$smarty.request.srch_date_from}" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">{$L_DATE} {$L_TO}</label>
		<div class="controls">
			<input type="text" name="srch_date_to" id="srch_date_to" value="{$smarty.request.srch_date_to}" />
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<input type="submit" name="submit_search_form_block" value="{$L_GO_FIND}" class="btn btn-primary" />
			<input type="button" id="reset" value="{$L_RESET}" class="btn btn-warning" /></td></tr>
		</div>
	</div>
	
</form>
</div>

</div>


{/if}
		
</div>
</div>
</div>



		
		
		

		
<table class="table table-hover" cellspacing="2" cellpadding="2">
	<tr  class="row_head">
		<td width="1%" class="row_title"><input type="checkbox" class="grid_check_all" /></td>
		<td width="1%" class="row_title">{$L_ID}</td>
		{if $show_uniq_id}
		<td width="1%" class="row_title">UNIQ_ID</td>
		{/if}
		
		{if $app_geodata_mode==1}
		<td width="1%" class="row_title">X/Y</td>
		{/if}
		<td width="1%" class="row_title">{$L_DATE}</td>
		<td width="1%" class="row_title">{$L_PHOTO}</td>
		<td width="70" class="row_title">{$L_TYPE}&nbsp;<a href="{$url}&order=type&asc=asc">&darr;</a>&nbsp;<a href="{$url}&order=type&asc=desc">&uarr;</a></td>
		<td width=13% class="row_title">{$L_CITY}&nbsp;<a href="{$url}&order=city&asc=asc">&darr;</a>&nbsp;<a href="{$url}&order=city&asc=desc">&uarr;</a></td>
		<td width=13% class="row_title">{$L_DISTRICT}&nbsp;<a href="{$url}&order=district&asc=asc">&darr;</a>&nbsp;<a href="{$url}&order=district&asc=desc">&uarr;</a></td>
		<td width=13% class="row_title">{$L_STREET}&nbsp;<a href="{$url}&order=street&asc=asc">&darr;</a>&nbsp;<a href="{$url}&order=street&asc=desc">&uarr;</a></td>
        <td width=13% class="row_title">Дом</td>
		<td class="row_title">{$L_PRICE}&nbsp;<a href="{$url}&order=price&asc=asc">&darr;</a>&nbsp;<a href="{$url}&order=price&asc=desc">&uarr;</a></td>
		{if $grid_items[0].company != ''}
		<td class="row_title">{$L_COMPANY}</td>
		{else}
        <td class="row_title">{$L_USER}</td>
		{/if}
		{if $admin !=''}
		<td class="row_title"></td>
		{/if}
	</tr>
	{section name=i loop=$grid_items}

	<tr valign="top" {if $grid_items[i].hot}class="row3hot"{else}class="row3"{/if} {if $admin == ''}	onClick="document.location='{$estate_folder}/realty{$grid_items[i].id}.html'" {/if}	{if $grid_items[i].active == 0}style="color: #ff5a5a;"{/if}>
		<td><input type="checkbox" class="grid_check_one" value="{$grid_items[i].id}" /></td>
		<td><b><a href="{$estate_folder}/realty{$grid_items[i].id}.html">{$grid_items[i].id}</a></b></td>
		{if $show_uniq_id}
		<td>{$grid_items[i].uniq_id}</td>
		{/if}
		
		{if $app_geodata_mode==1}
		<td align="center">
			
				{if isset($grid_items[i].geo_lat) && isset($grid_items[i].geo_lng) && $grid_items[i].geo_lat!='' && $grid_items[i].geo_lng!=''}
					<img src="{$estate_folder}/apps/admin/admin/template/img/map.png">
				{else}
					<img src="{$estate_folder}/apps/admin/admin/template/img/map_notactive.png">
				{/if}
			
			
		
		</td>
		{/if}
		<td>{$grid_items[i].date}</td>
		<td align="center">
		{if isset($grid_items[i].img) && $grid_items[i].img != ''} 
		<a href="{$estate_folder}/realty{$grid_items[i].id}.html"><img src="{$estate_folder}/img/data/{$grid_items[i].img[0].preview}" width="50" class="prv"></a>
		{/if}
		</td>
		<td><b>{$grid_items[i].type_sh}</b></td>
		<td>{$grid_items[i].city}</td>
		<td>{$grid_items[i].district}</td>
		<td>{$grid_items[i].street}</td>
        <td>{$grid_items[i].number}</td>
		<td nowrap><b>{$grid_items[i].price|number_format:0:'':' '}</b></td>
		<td>
		{if $grid_items[i].company != ''}
		{$grid_items[i].company}
		{else}
        	<a href="javascript:void(0);" rel="popover" class="tooltipe_block" data-placement="top" data-content="Информация о пользователе" data-original-title="" title="">{$grid_items[i].user}</a>
		{/if}
		</td>
		{if $admin !=''}
		<td nowrap>
		{if isset($show_contacts_enable) && $show_contacts_enable}
			<script>
			
			{literal}
				jQuery(document).ready(function(){
					
				});
			{/literal}
			</script>
			{if $grid_items[i].show_contact eq 0}
				<img src="{$estate_folder}/img/contact_delete_16x16.gif" alt="{$L_CONTACTS_ARE_HIDE}" title="{$L_CONTACTS_ARE_HIDE}" border="0" width="16" height="16" />
			{else}
				<img src="{$estate_folder}/img/contact-new.png" alt="{$L_CONTACTS_ARE_SHOWED}" title="{$L_CONTACTS_ARE_SHOWED}" border="0" width="16" height="16" />
			{/if}
		
		{/if}
		{if isset($sms_enable) && $sms_enable}
		<a onclick="return confirm({literal}'{$L_MESSAGE_REALLY_WANT_SMS}'{/literal});" href="{$estate_folder_control}?do=structure&subdo=sms&id={$grid_items[i].id}"><img src="{$estate_folder}/img/sms16x16.png" alt="{$L_SENDSMS_LC}" title="{$L_SENDSMS_LC}" border="0" width="16" height="16" /></a>
		{/if}
		{if isset($show_up_icon) && $show_up_icon}
		
		<a class="btn btn-warning go_up" alt="{$grid_items[i].id}" href="#grow_up"><i class="icon-white icon-circle-arrow-up"></i></a>
		{/if}
		<a href="{$estate_folder_control}?do=edit&id={$grid_items[i].id}" class="btn btn-info"><i class="icon-white icon-pencil"></i></a>
		<a onclick="return confirm('{$L_MESSAGE_REALLY_WANT_DELETE}');" href="{$estate_folder_control}?{if $topic_id != ''}topic_id={$topic_id}&{/if}do=delete&id={$grid_items[i].id}" class="btn btn-danger"><i class="icon-white icon-remove"></i></a>
		
		</td>
		{/if}
	</tr>
	{/section}
	<tr>
		<td colspan="14">
			<button alt="data" class="delete_checked btn btn-danger"><i class="icon-white icon-remove"></i> {$L_DELETE_CHECKED}</button> 
			<button alt="data" class="batch_update btn btn-inverse"><i class="icon-white icon-th"></i> Пакетная обработка <sup>(beta)</sup></button> 
			<button alt="data" class="duplicate btn btn-inverse"><i class="icon-white icon-th"></i> Дублировать <sup>(beta)</sup></button>
		</td>
	</tr>
	{if $pager != ''}
	<tr>
		<td colspan="14" class="pager"><div align="center">{$pager}</div></td>
	</tr>
	{/if}
</table>