<?php exit('404');?>
{include header}
<!-- hook.admin_navbar_header -->
<div class="title">导航设置</div>
<div class="navbar">
	{foreach $navbarList}
	<div class="form-row">
		<!-- hook.admin_navbar_col_top -->
		<div class="form-col"><label>名称</label><input type="text" name="name[]" placeholder="名称" value="{$item.name}"/></div>
		<div class="form-col"><label>链接</label><input type="text" name="url[]" placeholder="链接" value="{$item.url}"/></div>
		<!-- hook.admin_navbar_col_form -->
		<div class="form-col"><label><input name="target[]" type="checkbox" {if $item.target}checked=""{/if} value="{$index}">新窗口打开</label></div>
		<div class="form-col">
			<a class="red" href="javascript:;" onclick="del(this)">删除</a>
			<!-- hook.admin_navbar_col_operate -->
		</div>
		<!-- hook.admin_navbar_col_bottom -->
	</div>
	{/foreach}
</div>
<div class="btn" onclick="add()">添加链接</div>
<div class="btn bg-blue" onclick="submit()">保存</div>
<script>
	function initTarget(){
		SX('[name="target[]"]').each(function(i){
			this.value = i;
		})
	}
	function del(el){
		SX(el.parentNode.parentNode).del();
		initTarget();
		// hook.admin_navbar_del_js
	}
	function add(){
		SX('.navbar').append(`<div class="form-row">
			<!-- hook.admin_navbar_col_top_js -->
			<div class="form-col"><label>名称</label><input type="text" name="name[]" placeholder="名称" value=""/></div>
			<div class="form-col"><label>链接</label><input type="text" name="url[]" placeholder="链接" value=""/></div>
			<!-- hook.admin_navbar_col_form_js -->
			<div class="form-col"><label><input name="target[]" type="checkbox" value="1">新窗口打开</label></div>
			<div class="form-col">
				<a class="red" href="javascript:;" onclick="del(this)">删除</a>
				<!-- hook.admin_navbar_col_operate_js -->
			</div>
			<!-- hook.admin_navbar_col_bottom_js -->
		</div>`);
		initTarget();
		// hook.admin_navbar_add_js
	}
	function submit(){
		SX.ajax('{url admin/navbar}',SX('.navbar').form()).then((res)=>{
			SX.pop(res.data);
			// hook.admin_navbar_submit_success_js
			!res.error && SX.pjax.render();
		});
	}
</script>
<!-- hook.admin_navbar_footer -->
{include footer}