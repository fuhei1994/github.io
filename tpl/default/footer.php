<?php exit('404');?>
	</div>
	<div class="footer">
		<div class="line"></div>
		<div class="footer-intro">
			<div class="p">
				<span class="tag">链接</span>
				<ul class="link">
				{foreach $linkList}
					<li><a href="{$item.url}" target="{if $item.target}_blank{/if}">{$item.name}</a></li>
				{/foreach}
				</ul>
			</div>
			<div class="p">
				<span class="tag">心情</span>
				{$conf.mood}
			</div>
		</div>
		<div class="footer-bottom">
			<div class="footer-left">
				<span>浏览量 - {$conf.views}</span>
				<span>开源系统 - <a href="https://xueluo.cn/sxlog" target="_blank">溯雪v{#V}</a></span>
				<span><a href="http://beian.miit.gov.cn" class="icp" target="_blank">{$conf.icp}</a></span>
			</div>
			<div class="footer-right">
				<span>RunTime: {#getRunTime()} s</span>
				<span>Memory: {#getMemory()} kb</span>
			</div>
		</div>
		{$conf.js}
	</div>
	<!-- hook.body_footer -->
</body>
</html>