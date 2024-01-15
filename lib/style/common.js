/**
 * sx函数库
 * 官网：xueluo.cn
 * 创建：2019-11-05
 * 更新：2022-12-22
 * 版权声明：该版权完全归xueluo.cn官方所有，可转载使用和学习，但请务必保留版权信息
 */
'use strict';
window.SX = window.sx = function(str, obj){return new sx.init(str, obj)};
//加载完执行
sx.ready = (function(){
	var f = [], r = false, a = 'addEventListener';
	function handler(e){
		if (r || (e.type === 'onreadystatechange' && document.readyState !== 'complete')) return;
		for (var i = 0; i < f.length; i++) f[i].call(document);
		r = true, f = null;
	}
	document[a]('DOMContentLoaded', handler, false);
	window[a]('load', handler, false);
	return function ready(fn){ r ? fn.call(document) : f.push(fn) };
})();
/**
 * 选择器
 * @param string|function str 节点或者function(ready方式)
 * @param object obj 需要选择该对象下的节点
 * @return object|void
 */
sx.init = function(str, obj){
	this.length = 0;
	obj = obj ? obj : document;
	obj = Object.prototype.toString.call(obj) == '[object HTMLFormElement]' ? [obj] : obj;
	if (typeof str == 'string'){
		if (obj.length){
			var arr = [];
			for (var i = 0; i < obj.length; i++){
				var el = obj[i].querySelectorAll(str);
				if (el[0]){
					for (var s = 0; s < el.length; s++){
						arr.push(el[s]);
					}
				}
			}
			return sx.elThat(this, arr);
		} else {
			return sx.elThat(this, obj.querySelectorAll(str));
		}
	}
	if (typeof str == 'object') return sx.elThat(this, str);
	if (typeof str == 'function') return sx.ready(str);
	return sx.elThat(this, obj.querySelectorAll(str));
};
//继承方法
sx.init.prototype = {
	//遍历
	each(fn){
		return sx.each(this, fn);
	},
	//选择某个dom
	i(s){
		return sx(this[s]);
	},
	//获取value值
	val(v){
		return sx.val(this, v);
	},
	//获取或设置html
	html(v){
		return sx.html(this, v)
	},
	//获取表单
	form(){
		return sx.form(this);
	},
	//子元素的最后追加元素
	append: function(html) {
		return sx.append(this,html)
	},
	//子元素的前面追加元素
	prepend: function(html) {
		return sx.prepend(this,html)
	},
	//被选元素之前插入元素
	before: function(html) {
		return sx.before(this,html)
	},
	//被选元素之后插入元素
	after: function(html) {
		return sx.after(this,html)
	},
	//获取css设置的样式属性
	getCss(attr){
		return sx.getCss(this, attr)
	},
	//删除对象
	del(){
		return sx.del(this)
	},
	//获取对象距离屏幕的偏移量
	offset(){
		return sx.offset(this)
	},
	//设置或获取对象style的属性值
	style(name, v){
		return sx.style(this, name, v)
	},
	//设置或获取对象Attribute的属性值
	attr(name, v){
		return sx.attr(this, name, v)
	},
	//删除attr
	delAttr(name){
		return sx.delAttr(this, name)
	},
	//删除对象class
	delCss(name){
		return sx.delCss(this, name)
	},
	//克隆
	clone(){
		return sx.clone(this)
	},
	//给对象添加点击事件
	click(fn){
		return sx.click(this, fn)
	},
	//给对象添加事件
	on(type, fn, bool){
		return sx.on(this, type, fn, bool)
	},
	//方法返回被选元素的后代元素
	find(name){
		return sx.find(this, name);
	},
	//返回被选元素的所有直接子元素
	children: function(name) {
		return sx.children(this, name);
	},
	//返回被选元素的所有直接子元素
	childrens: function(name) {
		return sx.childrens(this, name);
	},
	//返回被选元素的所有兄弟元素
	siblings: function(name) {
		return sx.siblings(this, name);
	},
	//返回被选元素的上一个兄弟元素
	prev: function(name) {
		return sx.prev(this, name);
	},
	//返回被选元素的下一个兄弟元素
	next: function(name) {
		return sx.next(this, name);
	},
	//查找当前的祖先元素
	parent: function() {
		return sx.parent(this);
	},
	//查找所有的祖先元素
	parents: function() {
		return sx.parents(this);
	},
	//给对象添加class
	addCss(name){
		return sx.addCss(this, name)
	},
	//判断对象是否存在class
	hasCss(name){
		return sx.hasCss(this, name)
	},
	//如果对象存在指定的css，则删除，不存在则创建
	toggleCss(nameA, nameB, fnA, fnB){
		return sx.toggleCss(this, nameA, nameB, fnA, fnB)
	},
	//显示对象
	show(){
		return sx.show(this)
	},
	//隐藏对象
	hide(){
		return sx.hide(this)
	},
	//隐藏和显示对象
	toggle(fnA, fnB){
		return sx.toggle(this, fnA, fnB)
	},
	//隐藏和显示对象
	toggle(fnA,fnB) {
		return sx.toggle(this,fnA,fnB)
	},
	//淡入效果
	fadeIn(speed, callback) {
		return sx.fadeIn(this, speed, callback)
	},
	//淡出效果
	fadeOut(speed, callback) {
		return sx.fadeOut(this, speed, callback)
	},
	//淡入出开关
	fadeToggle(speed, callback) {
		return sx.fadeToggle(this, speed, callback)
	},
	//向下滑动显示
	slideDown(time, callback) {
		return sx.slideDown(this, time, callback)
	},
	//向上滑动隐藏
	slideUp(time, callback) {
		return sx.slideUp(this, time, callback)
	},
	//向上向下滑动隐藏
	slideToggle(time, callback) {
		return sx.slideToggle(this, time, callback)
	},
};
sx.__proto__ = {
	//判断是否为function对象
	isFunction(obj){
		return typeof obj === 'function' && typeof obj.nodeType !== 'number';
	},
	//判断是否为window对象
	isWindow(obj){
		return obj != null && obj === obj.window;
	},
	//判断是否为array对象
	isArrayLike(obj){
		var length = !!obj && 'length' in obj && obj.length, type = typeof obj;
		if (sx.isFunction(obj) || sx.isWindow(obj)) return false;
		return type === 'array' || length === 0 || typeof length === 'number' && length > 0 && (length - 1) in obj;
	},
	elThat(that, el){
		if (!el || (typeof el == 'object' && el.length === 0) || (typeof el == 'array' && el.length === 0)){
			return that;
		} else {
			if (el.tagName == 'SELECT' || el.tagName == 'FORM'){
				that.length = 1;
				that[0] = el;
			} else if (el.length){
				that.length = el.length;
				for (var i = 0; i < el.length; i++){
					that[i] = el[i];
				}
			} else {
				that.length = 1;
				that[0] = el;
			}
		}
		return that;
	},
	//遍历
	each(el, fn){
		if (sx.isWindow(el)){
			fn.call(el, 0, el);
			return el;
		}
		if (!el) return false;
		var length, i = 0;
		if (sx.isArrayLike(el)){
			length = el.length;
			for (; i < length; i++){
				if (fn.call(el[i], i, el[i], el, length) === false) break;
			}
		} else {
			for (i in el){
				if (fn.call(el[i], i, el[i], el, length) === false) break;
			}
		}
		return el;
	},
	//字符串转dom节点
	toDom(html){
		if (typeof html === 'string'){
			var temp = sx.c('div'), frag = document.createDocumentFragment();
			temp.innerHTML = html;
			while (temp.firstChild){
				frag.appendChild(temp.firstChild);
			}
			return frag;
		} else {
			return html;
		}
	},
	//创建节点
	c(name){
		return document.createElement(name);
	},
	//阻止冒泡
	sp(e){
		e ? e.stopPropagation() : (window.event ? window.event.stopPropagation() : '');
	},
	//阻止默认行为
	pd(e){
		e ? e.preventDefault() : (window.event ? window.event.preventDefault() : '');
	},
	//判断当前设备是否为移动端
	isMobile(){
		return /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent) ? true : false;
	},
	//获取网址get的值
	getUrl(name, url){
		url = url || 0;
		var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i'), r = url ? url.search.slice(1).match(reg) : window.location.search.slice(1).match(reg);
		if (r != null) return decodeURI(r[2]);
		return null;
	},
	//是否为json字符串
	isJson(str){
		if (typeof str == 'string'){
			try {
				JSON.parse(str);
				return true;
			} catch (e){ }
		}
		return false;
	},
	//AJAX
	ajax(json, data){
		if (typeof json == 'string'){
			json = { url: json };
			if (typeof data == 'string'){
				json.form = data;
			} else {
				json.data = data;
			}
		}
		if (!json || !json.url) return;
		json.type = json.type || 'post';
		json.timeout = json.timeout || 15000;
		json.async = json.async != undefined ? json.async : true;
		json.json = json.json != undefined ? json.json : true;
		return new Promise((resolve, reject) => {
			if (!json.data){
				if (json.form){
					json.data = sx(json.form).form();
				}
			}
			var json2url = function(json){
				var arr = [];
				function arrPush(k, v){ arr.push(k + '=' + encodeURIComponent(v)) }
				for (var name in json){
					if (Array.isArray(json[name])){
						if (typeof json[name][0] == 'object'){
							arrPush(name, sx.obj2str(json[name]));
						} else {
							for (var k in json[name]){
								arrPush(name, json[name][k]);
							}
						}
					} else {
						typeof json[name] == 'object' ? arrPush(name, sx.obj2str(json[name])) : arrPush(name, json[name]);
					}
				}
				return arr.join('&');
			}
			//创建
			var xhr = new XMLHttpRequest();
			//连接 和 发送 - 第二步
			switch (json.type.toLowerCase()){
				case 'post':
					xhr.open('POST', json.url, json.async);
					//设置表单提交时的内容类型
					xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
					if (json.data instanceof FormData == false) xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					if (json.header){
						for (var k in json.header){
							xhr.setRequestHeader(k, json.header[k]);
						}
					}
					xhr.send(json.data instanceof FormData ? json.data : json2url(json.data));
					break;
				default:
					var _data = json2url(json.data), _url = json.url + (_data.length ? (json.url.indexOf('?') > -1 ? '&' : '?') + _data : '');
					xhr.open('GET', _url, json.async);
					xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
					xhr.send();
					break;
			}
			//接收 - 第三步
			json.loading && json.loading();
			json.timer = setTimeout(function(){
				xhr.onreadystatechange = null;
				json.error && json.error('网络超时。');
				json.complete && json.complete(408);
			}, json.timeout);
			xhr.onreadystatechange = function(){
				if (xhr.readyState == 4){
					clearTimeout(json.timer);
					if (xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
						var res = '';
						if (xhr.responseText.length > 0){
							if (sx.isJson(xhr.responseText)){
								res = json.json ? JSON.parse(xhr.responseText) : xhr.responseText;
							} else {
								res = json.json ? { code: 200, data: xhr.responseText } : xhr.responseText;
							}
						}
						resolve && resolve(res);
						json.success && json.success(res);
						json.complete && json.complete(res);
					} else {
						reject && reject(xhr.status, xhr.responseText);
						json.error && json.error(xhr.status, xhr.responseText);
						json.complete && json.complete(xhr.status, xhr.responseText);
					}
				}
			}
		})
	},
	//获取对象距离窗口页面的顶部和左部的距离
	page(el) {
		el = this.arr2dom(el);
		if(!el) return false;
		var box = el.getBoundingClientRect(),
			doc = el.ownerDocument,
			body = doc.body,
			html = doc.documentElement,
			clientTop = html.clientTop || body.clientTop || 0,
			clientLeft = html.clientLeft || body.clientLeft || 0,
			y = box.top + (self.pageYOffset || html.scrollTop || body.scrollTop) - clientTop,
			x = box.left + (self.pageXOffset || html.scrollLeft || body.scrollLeft) - clientLeft;
		return {x: x,y: y};
	},
	//获取滚动条的偏移量
	scroll(a){
		var x = document.compatMode == 'CSS1Compat' ? (window.pageXOffset || document.documentElement.scrollLeft) :
			document.body.scrollLeft;
		var y = document.compatMode == 'CSS1Compat' ? (window.pageYOffset || document.documentElement.scrollTop) :
			document.body.scrollTop;
		if (a == 'x') {
			return x;
		} else if (a == 'y') {
			return y;
		} else if (!a) {
			return {
				x: x,
				y: y
			};
		}
		return obj;
	},
	//滚动条平滑滚动到该对象位置
	scrollAni(el, box=window, offset=0, duration=200) {
		var s = this.scroll().y;
		//结果大于0,说明目标在下方,小于0,说明目标在上方
		var distance = this.page(el).y - offset - s;
		var scrollCount = duration / 10; //10毫秒滚动一次,计算滚动次数
		var everyDistance = distance / scrollCount //滚动距离除以滚动次数计算每次滚动距离
		for (var index = 1; index <= scrollCount; index++) {
			setTimeout(function() {
				box.scrollBy(0, everyDistance)
			}, 10 * index);
		}
	},
	//按键回调
	keydown(key, callback){
		sx(document).on('keydown', (e) => {
			if (typeof key == 'function'){
				key.call(e, e.keyCode, e);
			} else {
				if ((e.keyCode == key) || (key == undefined)) callback && callback.call(e, e.keyCode, e);
			}
		})
	},
	//子元素的最后追加元素
	append(el, html){
		el = sx.dom2arr(el), html = sx.toDom(html);
		return sx.each(el, function(i,e){
			html = sx.dom2arr(html);
			sx.each(html, function(){
				if (e.nodeType === 1 || e.nodeType === 11 || e.nodeType === 9) e.appendChild(this);
			});
		});
	},
	//子元素的前面追加元素
	prepend(el,html) {
		el = sx.dom2arr(el);
		html = sx.toDom(html);
		return sx.each(el,function() {
			if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
				this.insertBefore(html,this.firstChild);
			}
		});
	},
	//被选元素之前插入元素
	before(el,html) {
		el = sx.dom2arr(el);
		html = sx.toDom(html);
		return sx.each(el,function() {
			if (this.parentNode) {
				this.parentNode.insertBefore(html,this);
			}
		});
	},
	//被选元素之后插入元素
	after(el,html) {
		el = sx.dom2arr(el);
		html = sx.toDom(html);
		return sx.each(el,function() {
			if (this.parentNode) {
				this.parentNode.insertBefore(html,this.nextSibling);
			}
		});
	},
	//获取表单
	form(el){
		el = sx.dom2arr(el);
		var arr = {};
		function init(e){
			if (e.name){
				//针对复选
				if (arr[e.name]){
					if (typeof arr[e.name] != 'object') arr[e.name] = [arr[e.name]];
					arr[e.name].push(e.value);
				} else {
					arr[e.name] = e.value;
				}
			}
		}
		sx.each(el, function(){
			sx('input', this).each(function(){
				if (this.type == 'checkbox'){
					if (this.checked) init(this);
				} else if (this.type == 'radio'){
					if (this.checked) init(this);
				} else {
					init(this);
				}
			});
			sx('textarea', this).each(function(){
				init(this);
			});
			sx('select', this).each(function(){
				if (this.name) arr[this.name] = sx(this).val();
			});
		})
		return arr;
	},
	//显示对象
	show(el){
		el = sx.dom2arr(el);
		return sx.each(el, function(){
			this.style.display = '';
			let d = sx.getCss(this, 'display'), o = sx.getCss(this, 'opacity');
			this.style.opacity = Number(o) == 0 ? 1 : this.style.opacity;
			this.style.display = d == 'none' ? 'initial' : d;
			if (sx.getCss(this, 'visibility') == 'hidden') this.style.visibility = 'visible';
		});
	},
	//隐藏对象
	hide(el){
		el = sx.dom2arr(el);
		return sx.each(el, function(){
			this.style.display = 'none';
		});
	},
	//显示隐藏的元素 或 隐藏显示的元素
	toggle(el, fnA, fnB){
		el = sx.dom2arr(el), fnA = fnA || function(){ }, fnB = fnB || function(){ };
		return sx.each(el, function(i, o){
			if (sx.getCss(this, 'display') == 'none' || sx.getCss(this, 'visibility') == 'hidden'){
				sx.show(this);
				fnA.call(o, i, o);
			} else {
				sx.hide(this);
				fnB.call(o, i, o);
			}
		});
	},
	/**
	 * 设置元素透明度
	 * Date 2017-11-08
	 * el {object} 对象
	 * s  {Number} 透明值(0-100)
	 */
	opacity(el, s) {
		el = ice.arr2dom(el);
		el.filters ? el.style.filter = 'alpha(opacity=' + s + ')' : el.style.opacity = s / 100;
	},
	/**
	 * 淡入效果
	 * Date 2017-11-08
	 * el       {object} 对象
	 * speed    {number} 淡入速度,(1秒=1000)
	 * opacity  {number} 淡入到指定透明值(0-100)
	 */
	fadeIn(el, speed, callback) {
		el = sx.dom2arr(el);
		callback = callback || function(){};
		speed = speed===true?20:(speed?speed:20);
		return sx.each(el,function(a,b){
			var display = sx.attr(b,'data-fade');
			if(display){
				b.style.display = display;
			}else{
				sx.setDisplay(b);
			}
			var s = 0;
			sx.opacity(b, s);
			+function t(){
				sx.opacity(b, s);
				s += 5;
				if (s <= 100) {
					setTimeout(t, speed);
				}else{
					callback.call(b, a, b);
				}
			}();
		});
	},
	/**
	 * 淡出效果
	 * Date 2017-11-08
	 * el       {object} 对象
	 * speed    {number} 淡出速度,(1秒=1000)
	 * callback {function} 回调函数
	 */
	fadeOut(el, speed, callback) {
		el = sx.dom2arr(el);
		callback = typeof speed == 'function' ? speed : callback;
		speed = speed===true?20:(speed?speed:20);
		return sx.each(el,function(a,b){
			if(sx.getCss(el,'display') == 'none'){
				return;
			}
			sx.setDisplay(b);
			var s = this.style.opacity ? this.style.opacity * 100 : (this.filters ? this.filters.alpha.opacity : 100);
			var display = sx.getDisplay(b);
			(function t(){
				sx.opacity(b, s);
				s -= 5;
				if (s >= 0) {
					setTimeout(t, speed);
				} else if (s < 0) {
					b.style.display = 'none';
					sx.attr(b,'data-fade',display);
					callback && callback.call(b, a, b);
				}
			})();
		});
	},
	/**
	 * 淡入出效果
	 * Date 2017-11-08
	 * el       {object} 对象
	 * speed    {number} 淡出速度,(1秒=1000)
	 * callback {function} 回调函数
	 */
	fadeToggle(el, speed, callback) {
		el = sx.dom2arr(el);
		callback = callback || function(){};
		speed = speed===true?20:(speed?speed:20);
		return sx.each(el,function(){
			if(sx.getCss(this,'display') == 'none'){
				sx.fadeIn(this, speed, callback);
			}else{
				sx.fadeOut(this, speed, callback);
			}
		});
	},
	//获取默认display
	getDisplay(el){
		el = sx.arr2dom(el);
		if(el.style.display == 'none'){
			el.style.display = null;
		}
		return sx.getCss(el,'display');
	},
	//设置默认display
	setDisplay(el){
		el = sx.arr2dom(el);
		let display = sx.getCss(el,'display');
		if(display == 'inline'){
			el.style.display = 'inline-block';
		}else if(display != 'none'){
			el.style.display = display;
		}else{
			el.style.display = null;
			let display = sx.getCss(el,'display');
			if(display == 'inline'){
				el.style.display = 'inline-block';
			}else if(display == 'none'){
				el.style.display = 'block';
			}else{
				el.style.display = display;
			}
		}
	},
	//判断style是否存在某条属性
	hasStyle(el,name){
		if(typeof el != 'string'){
			el = sx.arr2dom(el);
			var attr = sx.attr(el,'style');
		}else{
			var attr = el;
		}
		if(!attr) return false;
		//去掉所有空格
		attr = attr.replace(/\s/g,'');
		attr = attr.split(';');
		var i = attr.length;
		while(i--){
			if(attr[i].split(':')[0] == name) return true;
		}
		return false;
	},
	/**
	 * 向下滑动显示
	 * Date 2017-08-08
	 * el       {object} 对象
	 * speed    {number} 速度,(1秒=1000)
	 * callback {function} 回调函数
	 */
	slideDown(el, speed, callback) {
		el = sx.dom2arr(el);
		callback = callback || function(){};
		speed = speed===true?5:(speed?speed:5);
		return sx.each(el,function(i,obj){
			var display = ice(this).getCss('display');
			if (display != 'none') return;
			//设置元素样式的本来dispaly
			sx.setDisplay(this);
			var h=this.offsetHeight,a=0,s=1;
			this.style.overflow = 'hidden';
			this.style.height = '0px';
			(function down() {
				s += 0.6;
				a += 1+s;
				if (a < h) {
					obj.style.height = a + 'px';
					setTimeout(down, speed);
				} else {
					obj.style.overflow = null;
					obj.style.height = null;
					//还原之前的style样式
					sx.attr(obj,'style',sx.attr(obj,'ice-slide'));
					//删除备份的style样式
					sx.delAttr(obj,'ice-slide');
					//以防还原过来的style中的display为none
					sx.setDisplay(obj);
					callback.call(obj,i,obj);
				}
			})();
		});
	},
	/**
	 * 向上滑动隐藏
	 * Date 2017-08-08
	 * el    {object} 对象
	 * speed {number} 速度,(1秒=1000)
	 * func  {function} 回调函数
	 */
	slideUp(el, speed, callback) {
		el = sx.dom2arr(el);
		callback = callback || function(){};
		speed = speed===true?20:(speed?speed:20);
		return sx.each(el,function(i,obj){
			//备份style样式
			sx.attr(this,'ice-slide',sx.attr(this,'style'));
			if (this.style.display == 'none') return;
			//设置元素样式的本来dispaly
			sx.setDisplay(this);
			var h=this.offsetHeight,a=h,s=1;
			this.style.overflow = 'hidden';
			(function up() {
				s += 0.6;
				a -= 1+s;
				if (a > 10) {
					obj.style.height = a + 'px';
					setTimeout(up, speed);
				} else {
					obj.style.height = null;
					obj.style.display = 'none';
					obj.style.overflow = null;
					callback.call(obj,i,obj);
				}
			})();
		});
	},
	/**
	 * 上下滑动隐藏
	 * Date 2017-08-08
	 * el    {object} 对象
	 * speed  {number} 速度,(1秒=1000)
	 * func  {function} 回调函数
	 */
	slideToggle(el, speed, callback) {
		el = sx.dom2arr(el);
		callback = callback || function(){};
		speed = speed===true?20:(speed?speed:20);
		return sx.each(el,function(){
			if(sx.getCss(this,'display') == 'none'){
				sx.slideDown(this,speed, callback);
			}else{
				sx.slideUp (this,speed, callback)
			}
		});
	},
	//获取或设置html
	html(el, v){
		el = sx.dom2arr(el);
		if (typeof v == 'string' || typeof v == 'number'){
			return sx.each(el, function(){
				this.innerHTML = v;
			});
		} else if (typeof v == 'object' && v.tagName){
			return sx.each(el, function(){
				this.appendChild(v);
			});
		} else {
			return el[0] ? el[0].innerHTML : el.innerHTML;
		}
	},
	//对象转为字符串，包括function
	obj2str(obj){
		var a = JSON.stringify(obj, function(key, val){
			if (typeof val === 'function'){
				var str = '`' + val + '`';
				str = str.split('\n').join('FN\\n').split('\t').join('FN\\t');
				return str;
			}
			return val;
		});
		a = a.replace(/("`)|(`")/g, '').replace(/FN\\\\n/g, '\n').replace(/FN\\\\t/g, '\t');
		return a;
	},
	//字符串转为对象
	str2obj(str){
		return eval('(' + str + ')');
	},
	//获取对象距离屏幕的偏移量
	offset(el){
		el = sx.arr2dom(el);
		var e = el, x = e.offsetLeft, y = e.offsetTop;
		while (e = e.offsetParent){
			x += e.offsetLeft;
			y += e.offsetTop;
		}
		return { 'left': x, 'top': y };
	},
	//克隆
	clone(el){
		el = sx.dom2arr(el);
		var node = [];
		sx.each(el, function(){
			node.push(this.cloneNode(true));
		});
		return sx(node);
	},
	//获取css设置的样式属性
	getCss(el, attr){
		el = sx.arr2dom(el);
		return (el.currentStyle || getComputedStyle(el, false))[attr];
	},
	//删除对象
	del(el){
		el = sx.dom2arr(el);
		sx.each(el, function(){
			if (this.parentNode) this.parentNode.removeChild(this)
		});
	},
	//获取选择列表选中的值
	val(el, v){
		if (v != undefined){
			el = sx.dom2arr(el);
			sx.each(el, function(){
				this.selectedIndex = -1;
				if (this.value != undefined) this.value = v;
			});
		} else {
			el = sx.arr2dom(el);
			return el.value != undefined ? el.value : '';
		}
		return el;
	},
	//设置或获取对象style的属性值
	style(el, name, value){
		el = sx.dom2arr(el);
		if (arguments.length == 3 && value){
			//设置一个样式
			return sx.each(el, function(){
				this.style[name] = value;
			});
		} else {
			if (typeof name == 'string'){
				//获取样式
				var that = el;
				return sx.each(el, function(){
					return that.getCss(this, name);
				});
			}
			//批量设置样式
			var json = name;
			return sx.each(el, function(){
				for (var name in json){
					this.style[name] = json[name];
				}
			});
		}
	},
	//设置或获取对象Attribute的属性值
	attr(el, name, value){
		el = sx.dom2arr(el);
		if (value != undefined){
			return sx.each(el, function(){
				this.setAttribute(name, value);
			});
		} else {
			if (typeof name == 'string'){
				el = el[0] ? el[0] : el;
				var a = el.getAttribute ? el.getAttribute(name) : false;
				return a == null ? false : a;
			} else {
				var json = name;
				return sx.each(el, function(){
					for (var name in json){
						this.setAttribute(name, json[name]);
					}
				});
			}
		}
	},
	//删除attr
	delAttr(el, name){
		el = sx.dom2arr(el);
		return sx.each(el, function(){
			this.getAttribute(name) && this.removeAttribute(name);
		});
	},
	//给对象添加点击事件
	click(el, fn){
		el = sx.dom2arr(el);
		return sx.each(el, function(i, o){
			if (fn){
				o.onclick = (e) => fn.call(o, i, o, e);
			} else {
				o.click();
			}
		});
	},
	//给对象添加事件
	on(el, type, fn, bool){
		if (!sx.isWindow(el)) el = sx.dom2arr(el);
		bool = false || bool;
		return sx.each(el, function(i, o){
			o.addEventListener(type, function(e){
				fn.call(o, i, o, e);
			}, bool)
		});
	},
	//对象转为数组对象
	dom2arr(el){
		if (!el || (typeof el == 'object' && el.length === 0) || (typeof el == 'array' && el.length === 0)){
			return [];
		} else {
			if (!el[0]) el = [el];
		}
		return el;
	},
	//多个数组对象，转为第一个对象
	arr2dom(el){
		if (!el || (typeof el == 'object' && el.length === 0) || (typeof el == 'array' && el.length === 0)){
			return false;
		} else {
			if (el[0]) el = el[0];
		}
		return el;
	},
	//返回被选元素的后代元素
	find(el, name){
		el = sx.dom2arr(el);
		return sx(name, el);
	},
	//返回被选元素的所有直接子元素
	children(el, name) {
		el = sx.dom2arr(el);
		if(name) return sx(name, el);
		var arr = [];
		sx.each(el, function() {
			sx.each(this.children, function() {
				arr.push(this);
			});
		});
		return sx(arr);
	},
	//返回被选元素的所有子元素
	childrens(el) {
		el = sx.dom2arr(el);
		var arr = [];
		function get(obj){
			if(obj.children.length){
				sx.each(obj.children, function() {
					arr.push(this);
					get(this);
				});
			}
		}
		sx.each(el, function() {
			get(this);
		});
		return sx(arr);
	},
	//获得匹配集合中每个元素的兄弟节点
	siblings(el, name) {
		el = sx.dom2arr(el);
		var arr = [];
		sx.each(el, function() {
			if(this.parentNode){
				//如果name已定义，则遍历父级下的所有兄弟节点
				sx.each(name ? sx(name, this.parentNode) : this.parentNode.children, function() {
					arr.push(this);
				});
			}
		});
		return sx(arr);
	},
	//获取上一个兄弟节点
	prev(el, name) {
		el = sx.dom2arr(el);
		var arr = [];
		sx.each(el, function(i, o) {
			var prev = this.previousElementSibling;
			!name && prev && arr.push(prev);
			name && sx.each(sx(name, this.parentNode), function() {
				if(this === prev){
					arr.push(this);
				}
			});
		});
		return sx(arr);
	},
	//获取下一个兄弟节点
	next(el, name) {
		el = sx.dom2arr(el);
		var arr = [];
		sx.each(el, function() {
			var prev = this.nextElementSibling;
			!name && prev && arr.push(prev);
			name && sx.each(sx(name, this.parentNode), function() {
				if(this === prev){
					arr.push(this);
				}
			});
		});
		return sx(arr);
	},
	//查找当前的直接祖先元素
	parent(el) {
		el = sx.dom2arr(el);
		var arr = [];
		sx.each(el, function() {
			if(this.parentNode) arr.push(this.parentNode);
		});
		return sx(arr);
	},
	//查找所有的祖先元素
	parents(el) {
		var arr = [];
		el = sx.dom2arr(el);
		sx.each(el, function() {
			var p = this.parentNode;
			while (p !== document) {
				var o = p;
				arr.push(o);
				p = o.parentNode;
			}
		});
		return sx(arr);
	},
	//给对象添加class
	addCss(el, name){
		el = sx.dom2arr(el);
		return sx.each(el, function(){
			var c = this.className.split(/\s+/), n = name.split(/\s+/);
			for (var s of n) !c.includes(s) && c.push(s);
			this.className = c.join(' ');
		});
	},
	//删除对象class
	delCss(el, name){
		el = sx.dom2arr(el);
		return sx.each(el, function(){
			var c = this.className.split(/\s+/), n = name.split(/\s+/);
			for (var s of n) if (c.indexOf(s) > -1) c.splice(c.indexOf(s), 1);
			this.className = c.join(' ');
		});
	},
	//判断对象是否存在class
	hasCss(el, name){
		el = sx.dom2arr(el);
		for(var i=0;i<el.length;i++){
			if (!el[i].className) return false;
			var css = el[i].className.split(/\s+/), n = name.split(/\s+/);
			for (var s of n) if (!css.includes(s)) return false;
		}
		return true;
	},
	//如果对象存在指定的css，则删除，不存在则创建
	toggleCss(el, nameA, nameB, fnA, fnB){
		fnA = fnA || function(){ }, fnB = fnB || function(){ }, el = sx.dom2arr(el);
		return sx.each(el, function(i, o){
			var n = nameA.split(' ');
			for (var s of n){
				if (nameB){
					if (sx.hasCss(this, s)){
						sx.delCss(this, s);
						sx.addCss(this, nameB);
						fnB.call(o, i, o);
					} else {
						sx.addCss(this, s);
						sx.delCss(this, nameB);
						fnA.call(o, i, o);
					}
				} else {
					sx.hasCss(this, s) ? sx.delCss(this, s) : sx.addCss(this, s);
				}
			}
		});
	},
	//设置sessionStorage赋值完全等于取值，原生的sessionStorage实际只能存储字符串
	//sx.data() 获取整个sessionStorage
	//sx.data('t1') 获取t1值
	//sx.data('t1',123) 设置t1值
	//sx.data({t1:123,t2:456}) 设置t1和t2值
	//sx.data('t1',{t2:123,t3:456}) 设置t1值，获取后也是该对象
	data(name, value){
		return sx.storage(sessionStorage, name, value);
	},
	storage(type, name, value){
		if (name === undefined) return type;
		if (value === undefined){
			if (typeof name === 'object'){
				name.forEach((v, k) => {
					setData(k, v)
				})
			} else {
				return getData(name);
			}
		}
		if (value !== undefined) setData(name, value);
		function setData(k, v){
			let obj = type.getItem('_sxDataType');
			obj = obj ? sx.str2obj(obj) : {};
			obj[k] = typeof v;
			if (obj[k] === 'object') v = sx.obj2str(v);
			type.setItem(k, v);
			type.setItem('_sxDataType', sx.obj2str(obj));
		}
		function getData(k){
			let v = type.getItem(k), obj = type.getItem('_sxDataType');
			if (obj){
				obj = sx.str2obj(obj);
				if (obj[k] === 'number'){
					return Number(v);
				} else if (obj[k] === 'object'){
					return sx.str2obj(v);
				} else if (obj[k] === 'function'){
					return sx.str2obj(v);
				} else if (obj[k] === 'boolean'){
					return v.toLowerCase() === 'true' ? true : false;
				}
			}
			return v;
		}
	},
	//清除storage
	delStorage(type, name){
		if (name){
			type.removeItem(name);
		} else {
			for (var k in type){
				type.removeItem(k);
			}
		}
	},
	//清除sessionStorage
	delData(name){
		sx.delStorage(sessionStorage, name);
	},
	//设置localStorage赋值完全等于取值，原生的localStorage实际只能存储字符串
	localData(name, value){
		return sx.storage(localStorage, name, value);
	},
	//清除localStorage
	delLocalData(name){
		sx.delStorage(localStorage, name);
	},
	//加载js
	loadJs(url, callback, type='text/javascript') {
		if(!url) return false;
		var s = sx('script');
		for(var i=0;i<s.length;i++){
			if(s[i].src && s[i].src.indexOf(url) !== -1){
				return callback();
			}
		}
		var head = document.getElementsByTagName('head')[0];
		var script = document.createElement('script');
		if(type && type.length) script.type = type;
		script.src = url;
		script.onload  = function() {
			callback && callback();
		};
		head.appendChild(script);
	},
	//pjax无刷新
	pjax(json){
		json = json || {};
		json.el = json.el ? json.el : 'body';
		json.timeout = json.timeout || 5000;
		json.url = json.url ? json.url : window.location.href;
		if(!json.url || json.url== '#' || json.url.trim().toLowerCase().slice(0,11)== 'javascript:')return;
		json.pjaxRun = json.pjaxRun ? json.pjaxRun : false;
		//初始化链接
		var linkInit = function(el){
			sx(el).each(function(){
				if(!this.pjax){
					this.pjax = true;
					sx(this).on('click',function(){
						var url = sx(this).attr('href');
						var isPjax = sx(this).attr('data-pjax');
						var isDownload = sx(this).attr('download');
						if(isDownload || isPjax == 'false' || url== '#' || url.trim().toLowerCase().slice(0,11)== 'javascript:'){
							return;
						}
						sx.pd();
						if(sx(this).attr('target') === '_blank'){
							return window.open(url);
						}
						json.url = url;
						sx.pjax(json);
						return false;
					});
				}
			})
		};
		//重新加载
		window.sx.pjax.render = function() {
			sx.pjax(json);
		};
		//打开指定url
		window.sx.pjax.open = function(url){
			json.url = decodeURIComponent(url);
			sx.pjax(json);
		};
		//加载中动画
		var load = sx.c('div');
		load.innerHTML = json.loading ? json.loading : '<div class="loader"></div>';
		sx(json.el).append(load);
		
		if(!json.pjaxRun){
			json.pjaxRun = true;
			linkInit('a');
			window.addEventListener('popstate', function(e){
				json.url = window.location.pathname + window.location.search;
				json.popstate = true;
				sx.pjax(json);
			}, false);
		}

		if(!json.popstate) {
			history.pushState(null, null, json.url);
		}else{
			json.popstate = false;
		}

		//第一次进入页面不请求
		if(!json.first){
			load && sx(load).del();
			json.first = true;
			return;
		}

		var _url = json.url + (json.url.indexOf('?') > -1 ? '&' : '?') + '__v__='+new Date().getTime();

		//请求数据
		sx.ajax({
			url:_url,
			type:json.type ? json.type : 'get',
			ajaxConf:false,
			timeout:json.timeout,
			success(res){
				let temp = sx.c('div');
				// 纠正title
				var title = /<title>([\w\W]*)<\/title>/.exec(res.data);
				title && title.length>1 && sx('title').html(title[1]);
				// 纠正body
				var body = /<body.*?>([\w\W]*)<\/body>/.exec(res.data);
				temp.innerHTML = body && body.length > 1 ? body[1] : '';

				let content = sx(json.el,temp);
				if(!content.length) json.error && json.error(res);
				content = content.length?content[0].innerHTML:'';
				sx(json.el)[0].innerHTML = content;

                window.scrollTo(0,0);
                var jsObj = [], oldScript = sx('script'), script = sx('script',temp);
				temp = null;
				script.each(function(){
					jsObj.push({
						src: this.src ? 1 : 0,
						type: this.type,
						content: this.src ? this.src : this.innerHTML.trim(),
					})
				})
				!function runJs(callback,i=0){
					var s = jsObj[i++];
					if(i <= jsObj.length && s && s.content.length){
						if(s.src){
							//如果已存在该js，删掉重建，不然不执行里面的代码
							oldScript.each(function(){
								if(this.src == s.content) sx(this).del();
							})
							//重建js
							sx.loadJs(s.content,function(){
								runJs(callback,i);
							},s.type)
						}else{
							window.eval(s.content);
							runJs(callback,i);
						}
					}else{
						callback && callback();
					}
				}(function(){
					json.success && json.success(json.url, res);
				},0);
				linkInit(json.el+' a');
			},
			error(res){
				sx.pjax.open(json.empty);
				json.error && json.error(res);
			},
			complete(){
				load && sx(load).del();
				json.complete && json.complete();
			}
		});
	},
	//提示框
	pop(options, time = 2000){
		if (typeof options !== 'object'){
			options = { content: options };
			if (typeof time == 'function'){
				options.success = time, options.time = 2000;
			}
		}
		if (!options.content) return;
		if (options.time !== undefined) time = options.time;
		sx('.pop').del();
		var el = sx.c('div');
		el.className = 'pop ani-up-in';
		el.innerHTML = '<div class="pop-title">' + options.content + '</div>';
		document.body.appendChild(el);
		time > 0 && setTimeout(()=>{
			sx(el).addCss('ani-down-out');
			setTimeout(()=>{
				sx.del(el);
				if (options.url) location.href = options.url;
				options.success && options.success();
			}, 500);
		}, time);
	},
	//弹窗
	alert(options, yes, no){
		if (typeof options == 'string'){
			options = { content: options, btn: ['确认'] };
			if (yes || no) options.btn = 0;
			if (yes) options.yes = yes;
			if (no) options.no = no;
		}
		if (!options.content) return;
		var btn = options.btn || ['取消', '确认'];
		var el = sx.c('div');
		el.className = 'alert';
		document.body.appendChild(el);
		var btnHtml = '<div class="alert-confirm">确定</div><div class="alert-cancel">取消</div>';
		if (btn){
			if (btn[0]) btnHtml = `<div class="alert-confirm">${btn[0]}</div>`;
			if (btn[1]) btnHtml = `<div class="alert-cancel">${btn[0]}</div><div class="alert-confirm">${btn[1]}</div>`;
		}
		el.innerHTML = `<div class="alert-bg"></div>
		<div class="alert-box ani-up-in">
			<div class="alert-content">${options.content}</div>
			<div class="alert-footer">
				${btnHtml}
			</div>
		</div>`;
		sx('.alert-confirm', el).click(function(){
			sx(el).del();
			options.yes && options.yes();
		});
		sx('.alert-cancel', el).click(function(){
			sx(el).del();
			options.no && options.no();
		});
		sx(el).show();
	},
	//顶部对话框
	prompt(options, yes, no){
		if (typeof options == 'string'){
			options = { content: options, btn: ['知道了'] };
			if (yes || no) options.btn = 0;
			if (yes) options.yes = yes;
			if (no) options.no = no;
		}
		if (!options.content) return;
		var btn = options.btn || ['取消', '确认'];
		var title = options.title || '温馨提示';
		var el = sx.c('div');
		el.className = 'prompt';
		document.body.appendChild(el);
		var btnHtml = '<span class="prompt-confirm">确定</span><span class="prompt-cancel">取消</span>';
		if (btn){
			if (btn[0]) btnHtml = `<span class="prompt-confirm">${btn[0]}</span>`;
			if (btn[1]) btnHtml = `<span class="prompt-cancel">${btn[0]}</span><span class="prompt-confirm">${btn[1]}</span>`;
		}
		el.innerHTML = `<div class="prompt-box ani-up-in">
			<div class="prompt-close prompt-cancel">✕</div>
			<div class="prompt-title">「${title}」</div>
			<div class="prompt-content">${options.content}</div>
			<div class="prompt-footer">${btnHtml}</div>
		</div>`;
		sx('.prompt-confirm', el).click(function(){
			sx(el).del();
			options.yes && options.yes();
		});
		sx('.prompt-cancel', el).click(function(){
			sx(el).del();
			options.no && options.no();
		});
		sx(el).show();
	},
	//确认窗
	confirm(url, text){
		url = typeof (url) == 'object' ? url.href : url;
		sx.sp();
		sx.alert({
			content: text,
			yes(){
				window.location.href = url;
			}
		})
		return false;
	}
}

//配置信息
sx.CONF = { URL: '/?' };

//跳转
sx.jump = function(url,bool){
	window.location.href=bool?url:sx.CONF.URL+url;
}

//文件上传
sx.upload = function(options={}){
	var el;
	if(typeof options == 'string'){
		el = options;
		options = {};
	}
	var accept = options.accept || '*';
	var url = options.url || sx.CONF.URL + 'upload';
	var input = sx.c('input');
	input.type = 'file';
	input.accept = accept;
	input.onchange = function(){
		var formData = new FormData();
		options.name && formData.append('name', options.name);
		options.path && formData.append('path', options.path);
		if(options.absolutePath !== undefined) formData.append('absolutePath', options.absolutePath);
		for (var i = 0; i < this.files.length; i++){
			formData.append('file[]', this.files[i]);
		}
		sx.pop('上传中，请稍等...', 0);
		sx.ajax(url, formData).then(res => {
			if (res.error){
				sx.pop(res.data);
				options.error && options.error(res);
			} else {
				sx.pop('上传成功', ()=>{
					if(el){
						sx(el).each(function(){
							if(this.tagName == 'INPUT'){
								this.value = res.data[0].url;
							}else if(this.tagName == 'IMG'){
								this.src = res.data[0].url;
							}
						})
					}
					options.success && options.success(res.data);
				});
			}
		})
	};
	input.click();
}

//导入溯雪源
sx.import = function(type='system'){
	var el = sx.c('input');
	el.type = 'file';
	el.accept = '.sx';
	el.onchange = function(){
		var formData = new FormData();
		for (var i = 0; i < this.files.length; i++){
			formData.append('file', this.files[i]);
		}
		sx.pop('导入中，请稍等...', 0);
		sx.ajax(sx.CONF.URL + 'import/'+type, formData).then(res => {
			if (res.error){
				sx.pop(res.data);
			} else {
				sx.pop('导入成功', ()=>{
					location.reload();
				});
			}
		})
	};
	el.click();
}

//删除文章
sx.delArticle = function(id){
	id = typeof id == 'string' ? [id] : id;
	sx.alert({
		content: '确定要删除吗？',
		yes(){
			sx.ajax(sx.CONF.URL + 'admin/article/delete', { 'id[]': id }).then(()=>{
				sx.pop('删除成功', ()=>{
					location.href = sx.CONF.HOME;
				})
			})
		}
	})
}

//留言回复
sx.reply = function(key){
	sx('input[name=pid]')[0].value = key;
	sx('.comment-replys')[0].innerHTML = '回复：' + sx('.ip-' + key)[0].innerHTML + '<span onclick="sx.cancelReply()">取消</span>';
}

//取消留言回复
sx.cancelReply = function(){
	sx('input[name=pid]')[0].value = '';
	sx('.comment-replys')[0].innerHTML = '';
}

sx(function(){
	sx('#pjax-content').length && sx.pjax({
		el:'#pjax-content',
		success(){
			//返回上一页
			sx('.back').click(()=>{
				window.history.back();
			})
			//选项卡
			sx('.tab').each(function(){
				var menu = sx('.tab-menu a', this);
				var content = sx('.tab-content', this);
				if (content.length){
					menu.click((i, el) => {
						menu.delCss('active');
						sx(el).addCss('active');
						content.delCss('active');
						content.i(i).addCss('active');
						sx.pd();
						return false;
					})
				}
			})
		},
		error(){
			window.location.reload();
		}
	});

	//获取配置信息
	if (sx.data('sx.CONF')){
		sx.CONF = sx.data('sx.CONF');
	} else {
		sx.ajax(location.href, { 'getConf': 1 }).then(res => {
			sx.CONF = res.data;
			sx.data('sx.CONF', sx.CONF);
		})
	}
	//后台侧栏收缩
	sx('.toggle-menu').click(()=>{
		sx.sp();
		sx('.toggle-menu').toggleCss('open');
		sx('.sidebar').toggleCss('sidebar-open');
	})
	sx('body').click(()=>{
		sx('.toggle-menu').delCss('open');
		sx('.sidebar').delCss('sidebar-open');
	})
	//菜单按钮高亮显示
	sx('.menu a').each((i, el) => {
		el.className = (el.href == window.location.href || window.location.href.indexOf(el.href+'/') > -1) ? 'active' : '';
	})
	sx('.menu a').on('click',function(){
		sx('.menu a').delCss('active');
		sx(this).addCss('active');
	})
});