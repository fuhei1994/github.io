<?php exit('404');?>
{include header}
<div class="content">
	<form action="{url admin/login}" method="post" class="login">
		<div class="title">登录 · 请输入密码</div>
		<input type="password" name="password" placeholder="密码" />
		<input type="submit" class="btn" value="提交"/>
	</form>
</div>
{include footer}