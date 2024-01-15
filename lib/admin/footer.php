<?php exit('404');?>
		</div>
		<div class="footer">
			<!-- hook.admin_footer -->
			<div class="footer-bar">
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
		<!-- hook.admin_body_footer -->
	</div>
</body>
</html>