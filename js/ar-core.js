/**------------------------------------------------------------------------**\

	THE NAMESPACE!
	Here's where we dump our global abstractions. You can extend it
	by checking out the mixin function. By default, it comes with:
		$ar.type: unified type checking
		$ar.mixin: extend the namespace
		$ar.init: delay functionality until the dom is ready

\**------------------------------------------------------------------------**/
WebBooker = {};
(function(){
	var self = {};

	// $ar.type:
	//		lets shrink some code. Calling without the type variable
	//		just returns the type, calling it with returns a boolean
	//		you can pass a comma seperated list of types to match against
	self.type = function(variable,type){
		var t = typeof variable,
			trap = false,
			more,ni;
		if(t == 'object'){
			more = Object.prototype.toString.call(variable);
			if(more == '[object Array]'){
				t = 'array';
			}else if(more == '[object Null]'){
				t = 'null';
			}else if(more == '[object Date]'){
				t = 'date';
			}else if(variable == window){
				t = 'node';
			}else if( variable && variable.nodeType){
				if(variable.nodeType == 1)
					t = 'node';
				else
					t = 'textnode';
			}
		}

		if(!type) return t;
		type = type.split(',');
		for(more = 0; more < type.length; more++)
			trap = trap || (type[more] == t);
		return t == type;
	};

	// $ar.clone:
	//		lets keep our data clean and seperated
	self.clone = function(obj){
		var type = $ar.type(obj),
			copy = {},
			ni;

		if(!/^(object||array||date)$/.test(type))
			return obj;
		if(type == 'date')
			return (new Date()).setTime(obj.getTime());
		if(type == 'array'){
			copy = obj.slice(0);
			for(ni = 0; ni < copy.length; ni++){
				copy[ni] = $ar.clone(copy[ni]);
			}
			return copy;
		}

		for(ni in obj) {
			if(obj.hasOwnProperty(ni))
				copy[ni] = $ar.clone(obj[ni]);
		}

		return copy;
	};

	// $ar.mixin:
	//		This is how we extend the namespace to handle new functionality
	//		it'll overwrite crap, so be carefull
	self.mixin = function(obj){
		if(!self.type(obj,'object'))
			throw new Error('$ar.mixin called with incorrect parameters');
		for(var ni in obj){
			if(/(mixin)/.test(ni))
				throw new Error('mixin isn\'t allowed for $ar.mixin');
			self[ni] = obj[ni];
		}
	};

	// $ar.init:
	//		Stores up function calls until the document.ready
	//		then blasts them all out
	self.init = (function(){
		var c = [], t, ni;
		t = setInterval(function(){
			if(!document.body || !window.ko || !window.$ar) return;
			clearInterval(t);
			t = null;
			for(ni = 0; ni < c.length; ni++)
				c[ni]();
		},800);
		return function(_f){
			if(!t)
				_f();
			else
				c.push(_f);
		};
	})();

	// $ar.expose
	//		Our distrobution mechanism smashes everything into an iife.
	//		This is a good idea in general, but doesn't play nicely with
	//		referencing models/etc from knockout. Chances are you don't
	//		need it.
	self.expose = function(variable,as){
		window[as] = variable;
	};

	self.expose(self,'$ar');
})();

//here's some util functions
$ar.mixin({
	// $ar.extend:
	//		throw a bunch of objects in and it smashes
	//		them together from right to left
	//		returns a new object
	extend : function(){
		if(!arguments.length)
			throw new Error('$ar.extend called with too few parameters');

		var out = {},
			ni,no;

		for(ni = 0; ni < arguments.length; ni++){
			if(!$ar.type(arguments[ni],'object'))
				continue;
			for(no in arguments[ni])
				out[no] = arguments[ni][no];
		}

		return out;
	},

	// $ar.load:
	//		Load some content dynamically
	load : function(path,callback){
		var d = document,
			html = /\.(htm|html|php)$/.test(path),
			js = /\.js$/.test(path),
			elem;

		if(html) throw new Error('I still need to be written');
		if(!/\.(js|css)$/.test(path)) return;
		elem = d.createElement(js?'script':'link');
		elem[js?'src':'href'] = path;
		if(!js) elem.rel = 'stylesheet';

		if(typeof callback == 'function')
		elem.onload = callback;

		d.body.appendChild(elem);
	}
});
$ar.mixin({
	dom : (function(){
		//querySelectorAll Polyfill
		document.querySelectorAll = document.querySelectorAll||function(selector){
			var doc = document,
			head = doc.documentElement.firstChild,
			styleTag = doc.createElement('style');
			head.appendChild(styleTag);
			doc._qsa = [];

			styleTag.styleSheet.cssText = selector + "{x:expression(document._qsa.push(this))}";
			window.scrollBy(0, 0);

			return doc._qsa;
		};
		//matchesSelector Polyfill
		Element.prototype.matchesSelector = Element.prototype.webkitMatchesSelector || Element.prototype.mozMatchesSelector || function(selector){
			var els = document.querySelectorAll(selector),
				ni,len;
			for(ni=0, len=els.length; ni<len; ni++ ){
				if(els[ni] == this)
					return true;
			}
			return false;
		};

		var cleanSelector = function(selector,_context){
			if(!selector.length)
				return [];
			var sels = selector.split(','),
				context = _context||document,
				res = [],
				ni,idpos,ctx;
			for(ni = 0; ni < sels.length; ni++){
				idpos = sels[ni].lastIndexOf('#');
				ctx = context;
				if(idpos > 0){
					ctx = document.getElementById(sels[ni].substr(idpos).match(/^#[^\s]*/)[0]);
					sels[ni] = sels[ni].substr(idpos).replace(/^#[^\s]*[\s]*/,'');
				}
				if(!sels[ni].length) continue;
				var f = ctx.querySelectorAll(sels[ni]);
				var list = Array.prototype.slice.call(_ietoArray(f),0);
				
				res = res.concat(list);
			}

			return res;
		};
		
		//gay IE 8 workaround
		var _ietoArray = function(obj) {
			var array = [];
			// iterate backwards ensuring that length is an UInt32
			for (var i = obj.length >>> 0; i--;) { 
				array[i] = obj[i];
			}
			return array;
		}

		var cssCap = function(a,x){ return x.toUpperCase(); };

		var events = {},
			fn = {
				css: function(dom,obj){
					if($ar.type(obj,'string'))
						return dom[0].style[obj.replace(/-(\w)/g,cssCap)];

					var ni,no;
					for(ni = 0; ni < dom._len; ni++){
						for(no in obj)
							dom[ni].style[no.replace(/-(\w)/g,cssCap)] = obj[no];
					}
					return dom;
				},
				addClass: function(dom,selector){
					var sels = selector.split(','),
						len = dom._len,
						ni,no;

					dom.removeClass(selector);

					for(ni = 0; ni < len; ni++){
						for(no = 0; no < sels.length; no++)
							dom[ni].className += ' ' + sels[no].replace(/(^\s*|\s*$)/g,'');
					}

					return dom;
				},
				removeClass: function(dom,selector){
					var sels = selector.split(','),
						len = dom._len,
						ni,no,cname;
					for(ni = 0; ni < len; ni++){
						cname = ' ' + dom[ni].className.replace(/\s+/g,' ');
						for(no = 0; no < sels.length; no++)
							cname = cname.replace(new RegExp('\\s' + sels[no].replace(/(^\s*|\s*$)/g,''),'g'),'');
						dom[ni].className = cname.slice(1);
					}

					return dom;
				},
				matches: function(dom,selector){
					var ni,no;
					if(!$ar.type(selector,'string'))
						selector = $ar.dom(selector);

					for(ni = 0; ni < dom._len; ni++){
						if(dom[ni] == window){
							if(($ar.type(selector,'string') && selector == 'window') || selector[0] == window)
								return true;
						} else if(selector.hasOwnProperty('_len')){
							for(no = 0; no < selector._len;no++){
								if(dom[ni] != selector[no])
									continue;
								return true;
							}
						} else if(!!dom[ni].matchesSelector(selector)){
							return true;
						}
					}
					return false;
				},
				next: function(dom,selector){
					if(!dom[0]){
						return DomObj();
					}
					var curr = dom[0].nextSibling;
					while(curr){
						if(!$ar.type(curr,'node') || (selector && !DomObj(curr).matches(selector))){
							curr = curr.nextSibling;
							continue;
						}

						return DomObj(curr);
					}

					return DomObj();
				},
				nextAll: function(dom,selector){
					var out = DomObj(),
						curr = dom.next(selector);
					while(curr._len){
						out[out._len] = curr[0];
						out._len ++;
						curr = curr.next(selector);
					}
					return out;
				},
				prev: function(dom,selector){
					if(!dom[0]){
						return DomObj();
					}
					var curr = dom[0].previousSibling;
					while(curr){
						if(!$ar.type(curr,'node') || (selector && !DomObj(curr).matches(selector))){
							curr = curr.previousSibling;
							continue;
						}

						return DomObj(curr);
					}

					return DomObj();
				},
				prevAll: function(dom,selector){
					var out = DomObj(),
						curr = dom.prev(selector);
					while(curr._len){
						out[out._len] = curr[0];
						out._len++;
						curr = curr.prev(selector);
					}
					return out;
				},
				find: function(dom,selector){
					return DomObj(selector,dom);
				},
				__closest: function(dom,selector){
					var elems = [],
						cap = document.documentElement,
						ni,no,len,curr,depth,found;

					if(typeof selector != 'string' && !selector.hasOwnProperty('_len'))
						throw new Error('invalid selector passed to $ar.dom.closest');

					for(ni = 0; ni < dom._len; ni++){
						curr = dom[ni];
						depth = 0;
						while(curr != cap){
							if(typeof selector != 'string'){
								found = false;
								for(no = 0; no < selector._len; no++){
									if(selector[no] != window && curr != selector[no])
										continue;
									found = true;
									break;
								}
								if(found) break;
							} else if(curr.matchesSelector(selector)) break;
							if(!curr.parentNode){
								curr = cap;
								break;
							}
							depth++;
							curr = curr.parentNode;
						}
						if(curr == cap) continue;
						elems.push({ elem: curr, depth: depth });
					}
					len = elems.length;
					for(ni = 0; ni < len; ni++){
						for(no = ni+1; no < len;no++){
							if(elems[ni].elem!=elems[no].elem) continue;
							elems.splice(no--,1);
							len--;
						}
					}
					return elems;
				},
				closest: function(dom,selector){
					var elems = fn.__closest(dom,selector),
						len, ni;

					curr = DomObj(null,dom);

					if(!elems.length)
						return curr;

					len = curr._len = elems.length;
					for(ni = 0; ni < len; ni++)
						curr[ni] = elems[ni].elem;
					return curr;
				},
				remove: function(dom){
					var ni,len;
					for(ni = 0, len = this._len; ni < len; ni++){
						if(!dom[ni].parentNode) continue;
						dom[ni].parentNode.removeChild(dom[ni]);
					}
					return dom;
				},
				before: function(dom,elem){
					var ni, no;
					if(!elem.hasOwnProperty('_len'))
						elem = $ar.dom(elem);
					for(ni = 0; ni < dom._len; ni++){
						if(!dom[ni].parentNode) continue;
						for(no = 0; no < elem._len; no++){
							dom[ni].parentNode.insertBefore(elem[no],dom[ni]);
						}
					}
					return dom;
				},
				after: function(dom,elem){
					var ni, no;
					if(!elem.hasOwnProperty('_len'))
						elem = $ar.dom(elem);
					for(ni = 0; ni < dom._len; ni++){
						if(!dom[ni].parentNode) continue;
						for(no = 0; no < elem._len;no++)
							dom[ni].parentNode.insertBefore(elem[no],dom[ni].nextSibling);
					}
				},
				clone: function(dom){
					var newDom = DomObj(),
						ni,no,temp,attr;
					newDom._selector = dom._selector;
					newDom._len = dom._len;
					for(ni = 0; ni < dom._len; ni++){
						temp = document.createElement(dom[ni].nodeName);
						attr = dom[ni].attributes;
						for(no = 0; no < attr.length; no++){
							temp.setAttribute(attr[no].name,attr[no].value);
						}
						temp.innerHTML = dom[ni].innerHTML;
						newDom[ni] = temp;
					}

					return newDom;
				},
				measure: function(dom){
					var box;
					if(dom[0].getBoundingClientRect){
						box = dom[0].getBoundingClientRect();
					}else{
						return { top: 0, left: 0, width: 0, height: 0 };
					}
					if(!box)
						return { top: 0, left: 0, width: 0, height: 0 };
						
					var _doc = document.defaultView || document.parentWindow;
					
					if(!_doc.getComputedStyle){
					    _doc.getComputedStyle = function(el, pseudo) {
					        this.el = el;
					        this.getPropertyValue = function(prop) {
					            var re = /(\-([a-z]){1})/g;
					            if (prop == 'float') prop = 'styleFloat';
					            if (re.test(prop)) {
					                prop = prop.replace(re, function () {
					                    return arguments[2].toUpperCase();
					                });
					            }
					            return el.currentStyle[prop] ? el.currentStyle[prop] : null;
					        }
					        return this;
					    }
				    }

				
					var body = dom[0].ownerDocument.body,
						clientTop  = document.documentElement.clientTop  || body.clientTop  || 0,
						clientLeft = document.documentElement.clientLeft || body.clientLeft || 0,
						scrollTop  = window.pageYOffset || document.documentElement.scrollTop  || body.scrollTop,
						scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || body.scrollLeft,
						top  = box.top  + scrollTop  - clientTop,
						left = box.left + scrollLeft - clientLeft,
						styles = _doc.getComputedStyle(dom[0]),

						p_top = parseFloat(styles.getPropertyValue('padding-top')),
						p_bottom = parseFloat(styles.getPropertyValue('padding-bottom')),
						p_left = parseFloat(styles.getPropertyValue('padding-left')),
						p_right = parseFloat(styles.getPropertyValue('padding-right')),

						b_top = parseFloat(styles.getPropertyValue('border-top-width')),
						b_bottom = parseFloat(styles.getPropertyValue('border-bottom-width')),
						b_left = parseFloat(styles.getPropertyValue('border-left-width')),
						b_right = parseFloat(styles.getPropertyValue('border-right-width'));

					return {
						top: top,
						left: left,
						width: box.right - box.left,
						height: box.bottom - box.top,
						innerWidth: box.right - box.left - (b_left + b_right + p_left + p_right),
						innerHeight: box.bottom - box.top - (b_top + b_bottom + p_top + p_bottom)
					};
				},
				get: function(dom,index){
					if(index < 0 || index > dom._len)
						return;
					return DomObj(dom[index],dom);
				},
				length: function(dom){ return dom._len; },
				html: function(dom,str){
					if($ar.type(str,'undefined'))
						return dom[0].innerHTML||'';
					for(var ni = 0; ni < dom._len; ni++)
						dom[ni].innerHTML = str;
					return dom;
				},
				append: function(dom,elem){
					var ni,no;
					elem = $ar.dom(elem);
					for(ni = 0; ni < dom._len; ni++){
						for(no = 0; no < elem._len; no++)
							dom[ni].appendChild(elem[no]);
					}
					return dom;
				},
			on: function(dom,evt,fun){
				if(/^(focus|blur)$/.test(evt)){
					var _evt = window.addEvent?'on'+evt:evt,
						ni;
					if(window.addEvent){
						_list = 'addEvent';
					} else if (window.addEventListener) {
						_list = 'addEventListener';
					} else if (window.attachEvent) {
						_list = 'attachEvent';
					}
					for(ni = 0; ni < dom._len; ni++){
						dom[ni][_list](_evt,fun);
					}

					return dom;
				}

				if(!events[evt]){
					events[evt] = (function(){
						var s = {
							evt: evt,
							fun: null,
							routes: []
						};
						s.fun = function(_e){
							/*
							if(s.evt === 'scroll'){
								var delta = 0,
									deltaX = 0,
									deltaY = 0,
									absDelta = 0;
								if('detail'      in _e ){
									deltaY = _e.detail * -1;
								}
								if('wheelDelta'  in _e ){
									deltaY = _e.wheelDelta;
								}
								if('wheelDeltaY' in _e ){
									deltaY = _e.wheelDeltaY;
								}
								if('wheelDeltaX' in _e ){
									deltaX = _e.wheelDeltaX * -1;
								}
								if('axis' in _e && _e.axis === _e.HORIZONTAL_AXIS){
									deltaX = deltaY * -1;
									deltaY = 0;
								}

								delta = deltaY === 0 ? deltaX : deltaY;

								if('deltaY' in _e){
									deltaY = _e.deltaY * -1;
									delta = deltaY;
								}
								if('deltaX' in _e){
									deltaX = _e.deltaX;

									if(deltaY === 0){
										delta = deltaX * -1;
									}
								}
								_e.deltaX = deltaX;
								_e.deltaY = deltaY;
							}
							*/
							var t = $ar.dom(_e.target),
								ni,na;
							for(ni = 0; ni < s.routes.length; ni++){
								na = t.closest(s.routes[ni].dom);
								if(!na.hasOwnProperty('_len')||!na._len){
									continue;
								}
								s.routes[ni].callback(_e);
							}
						};
						return s;
					})();

					if(window.addEvent){
						if(evt === 'scroll'){
							window.addEventListener('onmousewheel',events[evt].fun,false);
						} else {
							window.addEvent('on'+evt, events[evt].fun);
						}
					} else if(window.addEventListener){
						if(evt === 'scroll'){
							window.addEventListener('wheel',events[evt].fun,false);
						} else {
							window.addEventListener(evt,events[evt].fun,false);
						}
					} else if (window.attachEvent){
						if(evt === 'scroll'){
							window.attachEvent('wheel',events[evt].fun,false);
						} else {
							window.attachEvent(evt,events[evt].fun,false);
						}
					}
				}

				events[evt].routes.push({ dom: dom, callback: fun });
				return dom;
			},
			off: function(dom,evt,fun){
				if(!events[evt] || !events[evt].routes.length){
					return;
				}

				var r = events[evt].routes,
					ni,found=false;
				for(ni = r.length; ni > 0;){
					if(!dom.matches(r[--ni].dom)){
						continue;
					}
					if(fun && r[ni].callback !== fun){
						continue;
					}
					found = true;
					r.splice(ni,1);
				}

				return dom;
			},
				each: function(dom,callback){
					for(var ni = 0; ni < dom._len; ni++){
						_callback(dom.get(ni),ni);
					}
					return dom;
				},
				focus: function(dom){
					for(var ni = 0; ni < dom._len; ni++){
						if(dom[ni].nodeName == 'INPUT' || dom[ni].nodeName == 'SELECT' || dom[ni].nodeName == 'TEXTAREA' || dom[ni].nodeName == 'BUTTON' || dom[ni].nodeName == 'ANCHOR'){
							dom[ni].focus();
							
							return dom;
						}
					}
					
					return dom;
				}
			};

		var DomObj = function(selector, context){
			var self = {
				_back: null,
				_len: 0,
				_selector: ''
			};

			//some static functions
			var ni;
			for(ni in fn){
				(function(dom,index){
					dom[index] = function(){
						var args = Array.prototype.slice.call(arguments);
						args.unshift(dom);
						return fn[index].apply(dom,args);
					};
				})(self,ni);
			}

			self._back = context;

			if(!selector) return self;

			if($ar.type(selector,'node')){
				self[0] = selector;
				self._len = 1;
				return self;
			}

			if(/^[^<]*(<[\w\W]+>)[^>]*$/.test(selector)){
				var elem = document.createElement('div'),
					no,c;
				elem.innerHTML = selector.replace(/(^\s*|\s*$)/g,'');
				c = elem.childNodes;
				self._len = c.length;
				for(no = 0; no < self._len; no++){
					self[no] = c[no];
				}
				return self;
			}

			//need to add ability to create element or take normal element
			self._selector = selector;

			if(!selector) return self;

			var res = [],ni;
			if(context && context._len){
				for(ni = 0; ni < context._len; ni++){
					res = res.concat(cleanSelector(selector,context[ni]));
				}
			} else {
				res = cleanSelector(selector);
			}
			for(ni = 0; ni < res.length; ni++){
				self[ni] = res[ni];
			}
			self._len = res.length;

			return self;
		};

		var ret_func = function(selector){
			if($ar.type(selector,'object') && selector.hasOwnProperty('_len'))
				return selector;
			return DomObj(selector);
		};

		ret_func.atPoint = function(x,y){
			return $ar.dom(document.elementFromPoint(x,y));
		};

		return ret_func;
	})()
});
$ar.mixin({
	cache : {
		read: function(key,inLocalStorage){
			if(!inLocalStorage){
				return ((new RegExp("(?:^" + key + "|;\\s*"+ key + ")=(.*?)(?:;|$)", "g")).exec(document.cookie)||[null,null])[1];
			}
			throw new Error('nobody has written me yet');
		},
		write: function(key, value, expires, inLocalStorage){
			if(!inLocalStorage){
				document.cookie = key + "=" + escape(value) + ";path=/;domain=" + window.location.host;
				return;
			}
			throw new Error('nobody has written me yet');
		},
		remove: function(key, inLocalStorage){
			if(!inLocalStorage){
				if(!$ar.cache.read(key)) return;
				$ar.cache.write(key, "");
				return;
			}
			throw new Error('nobody has written me yet');
		}
	}
});// $ar.config
//		config di for central access
$ar.mixin({
	config: (function(){
		var conf = {};
		return function(to_add){
			if(!arguments.length)
				return conf;
			if(!to_add) return conf;

			var ni;
			for(ni in to_add){
				if(!to_add[ni]){
					if(conf[ni])
						delete(conf[ni]);
					continue;
				}
				conf[ni] = to_add[ni];
			}
		};
	})()
});
// $ar.api:
//		Here we normalize how we talk to the backend
//		You can change options for the api calls by manipulating
//		$ar.api.config
// requires $ar.extend, $ar.cache
$ar.mixin({
	api : (function(){
		var config = $ar.config(),
			self = {
				token: $ar.cache.read(config['cache_key']),
			};

		function postString(obj, prefix){
			var str = [], p, k, v;
			if($ar.type(obj,'array')){
				if(!prefix)
					throw new Error('Sorry buddy, your object is wrong');
				for(p = 0; p < obj.length; p++){
					k = prefix + "[" + p + "]";
					v = obj[p];
					str.push(typeof v == "object"?postString(v,k):encodeURIComponent(k) + "=" + encodeURIComponent(v));
				}
				return str.join('&');
			}
			for(p in obj) {
				if(prefix)
					k = prefix + "[" + p + "]";
				else
					k = p;
				v = obj[p];
				str.push(typeof v == "object"?postString(v,k):encodeURIComponent(k) + "=" + encodeURIComponent(v));
			}
			return str.join("&");
		}

		self.raw = (function(){
			function createStandardXHR(){ try { return new window.XMLHttpRequest(); } catch(e){} }
			function createActiveXHR(){ try { return new window.ActiveXObject("Microsoft.XMLHTTP"); } catch(e){} }
			function createJSONP(){
				function randomer(){
					var s=[],itoh = '0123456789ABCDEF',i;

					for(i = 0; i < 16; i++){
						s[i] = i==12?4:Math.floor(Math.random()*0x10);
						if(i==16) s[i] = (s[i]&0x3)|0x8;
						s[i] = itoh[s[i]];
					}
					return s.join('');
				}

				var ret = {
					_options: {
						key: '',
						url: '',
						script: null,
						mime: 'json'
					},
					readyState: 0,
					onreadystatechange: null,
					response: null,
					responseText: null,
					responseXML: null,
					responseType: '',

					status: null,
					statusText: '',
					timeout: 0,

					upload: null
				};

				ret.abort = function(){
					if(ret.readyState != 3) return;
					ret._options.script.parentNode.removeChild(ret._options.script);
					$ar.api[ret._options.key] = function(){
						delete $ar.api[ret._options.key];
					};

					ret.readyState = 1;
					if(typeof ret.onreadystatechange == 'function')
						ret.onreadystatechange();
				};
				ret.getAllResponseHeaders = function(){};
				ret.getResponseHeader = function(header){};
				ret.open = function(method,url,async,user,pass){
					//method is always get, async is always true, and user/pass do nothing
					//they're still there to provide a consistant interface
					ret._options.url = url;
					ret._options.script = document.createElement('script');
					ret._options.script.type = 'text/javascript';
					ret.readyState = 1;
					if(typeof ret.onreadystatechange == 'function')
						ret.onreadystatechange();

					document.head.appendChild(ret._options.script);
				};
				//this does nothing
				ret.overrideMimeType = function(mime){};
				ret.send = function(data){
					ret._options.key = 'jsonp_'+randomer();

					var _data = postString(data),
						url = ret._options.url;
					if(url.indexOf('?') == -1)
						url += '?callback=$ar.api.'+ret._options.key;
					else
						url += '&callback=$ar.api.'+ret._options.key;

					if(_data.length)
						url += '&'+_data;

					$ar.api[ret._options.key] = function(data){
						if(!$ar.type(data,'string'))
							data = JSON.stringify(data);
						ret.responseText = data;
						ret.response = data;

						/*

						if(ret.responseType === '' || ret.responseType == 'text'){
							ret.response = data;
						}
						if(ret.responseType == 'arraybuffer'){
							if(!base64DecToArr) throw new Error('arraybuffer not supported in jsonp mode');
							ret.response = base64DecToArr(data).buffer;
						}
						if(ret.responseType == 'blob'){
							if(!Blob) throw new Error('blob not supported in jsonp mode');
							ret.response = new Blob([data],{ 'type': 'text/plain' });
						}
						if(ret.responseType == 'document'){
							throw new Error('document not supported in jsonp mode');
						}
						if(ret.responseType == 'json'){
							ret.response = JSON.parse(data);
						}
						*/

						ret.readyState = 4;
						ret.status = 200;
						if(typeof ret.onreadystatechange == 'function')
							ret.onreadystatechange();
						ret._options.script.parentNode.removeChild(ret._options.script);

						delete $ar.api[ret._options.key];
					};

					ret.readyState = 3;
					if(typeof ret.onreadystatechange == 'function')
						ret.onreadystatechange();

					ret._options.script.src = url;
				};
				//this does nothing
				ret.setRequestHeader = function(header, value){};

				return ret;
			}
			function xhr(options){
				var origin, parts, crossDomain, _ret;

				try {
					origin = location.href;
				} catch(e){
					origin = document.createElement( "a" );
					origin.href = "";
					origin = origin.href;
				}

				origin = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/.exec(origin.toLowerCase());
				options.url = (( options.url ) + "").replace(/#.*$/, "").replace(/^\/\//, origin[1] + "//");
				parts  = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/.exec(options.url.toLowerCase());
				origin[3] = origin[3]||(origin[1]=='http:'?'80':'443');
				parts[3] = parts[3]||(parts[1]=='http:'?'80':'443');

				crossDomain = !!(parts &&
					( parts[1] !== origin[1] ||
						parts[2] !== origin[2] ||
						parts[3] != origin[3]
					)
				);

				_ret = window.ActiveXObject ?
					function() {
						return !/^(?:about|app|app-storage|.+-extension|file|res|widget):$/.test(origin[1]) && createStandardXHR() || createActiveXHR();
					} : createStandardXHR;
				_ret = _ret();

				//if(!_ret || (crossDomain && !_ret.hasOwnProperty('withCredentials')))
					_ret = createJSONP();

				return _ret;
			}

			function ajax(params){
				params = $ar.extend({
					url: '',
					method: 'GET',
					type: 'json',
					async: 'true',
					jsonp: 'callback',	//currently unused
					timeout: 0,
					data: null,

					succes: null,
					error: null
				},params);

				var _xhr = xhr(params);
				if(params.method == 'GET'){
					var getParam = postString(params.data);
					if(getParam.length)
						params.url += '?' + getParam;
				}
				_xhr.open(params.method,params.url,params.async);
				_xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				_xhr.responseType = params.type;
				_xhr.onreadystatechange = function(){
					if(_xhr.readyState != 4) return;
					if(_xhr.status != 200 && typeof params.error == 'function')
						params.error(_xhr.response);
					if(_xhr.status == 200 && typeof params.success == 'function')
						params.success(_xhr.response);
				};
				_xhr.send(params.method=='POST'?postString(params.data):null);
			}

			return function(url,data,callback,method){
				ajax({
					method: method||'GET',
					url: url,
					data: data,
					success: function(result){
						if(typeof callback == 'function')
							callback(result);
					}
				});
			};
		})();
		self.download = function(url,data,callback){
			window.open(url +'?'+postString(data),'_blank');
			if(typeof callback == 'function')
				callback();
		};
		self.request = function(service,action,params,callback,method){
			self.raw(config['api'], $ar.extend(params||{},{
				service: service,
				action: action,
				token: self.token||'NEW'
			}), function(result){
				try {
					result = JSON.parse(result);
				} catch(e){
					console.log('you have and error on the server');
					return;
				}
				if(result.token && result.token != self.token){
					$ar.cache.write(config['cache_key'],result.token);
					self.token = result.token;
				}

				if(typeof callback == 'function')
					callback(result);
			},method||'GET');
		};
		// if you're going to be using pre and post filters on the data
		// make sure you return true to continue the chain, or return
		// false to cancel it
		self.route = function(name,service,action,pre,post,method){
			name = name.trim();
			pre = pre||function(data){ return true; };
			post = post||function(data){ return true; };

			if(/^(route|raw|request|config)$/.test(name))
				throw new Error('invalid name sent to $ar.api.route');

			self[name] = function(params,callback){
				if(!pre(params)) return;
				self.request(service,action,params,function(data){
					if(post(data) && typeof callback == 'function')
						callback(data);
				},method||'GET');
			};
		};

		return self;
	})()
});// $ar.model is teh bones of the application
$ar.mixin({
	model : (function(){
		// stuff to exclude from the serialization
		var blacklist = /^(_.*|def|pre|post|serialize|extend|map|type|watch|errors|validate)$/;

		// lets only add clean data to our models
		function _cleanNumbers(obj){
			var type = $ar.type(obj),
				ni;

			if(/^(b.*|nu.*|f.*)$/.test(type))
				return obj;

			if(type == 'string'){
				if(!obj || obj == 'null')
					obj = null;
				else if(!isNaN(parseFloat(obj)) && isFinite(obj))
					return parseFloat(obj);

				return obj;
			}

			if(type == 'array'){
				for(ni = 0; ni < obj.length; ni++)
					obj[ni] = _cleanNumbers(obj[ni]);
			}

			if(type == 'object'){
				for(ni in obj)
					obj[ni] = _cleanNumbers(obj[ni]);
			}

			return obj;
		}

		// something needed to normalize knockout stuff
		function _cleanRead(model,key){
			if(model.def[key].observable)
				return model[key]();
			return model[key];
		}
		// something needed to normalize knockout stuff
		function _cleanWrite(model,key,val){
			if(model.def[key].observable)
				model[key](val);
			else
				model[key] = val;
		}

		// does the heavy lifting for importing an object into a model
		function _sin(model,data){
			var ni, na, no, a;
			if(!data){
				// reset to default values
				for(ni in model.def)
					_cleanWrite(model,ni,model.def[ni]['default']);
				return model;
			}

			if($ar.type(model._pre,'array')){
				for(ni = 0; ni < model._pre.length; ni++)
					model._pre[ni](data);
			}

			for(ni in model.def){
				na = model.def[ni].external?model.def[ni].external:ni;
				if(!data.hasOwnProperty(na)){
					if(!data.hasOwnProperty(ni))
						continue;
					na = ni;
				}

				a = null;
				if(!model.def[ni].type){
					_cleanWrite(model,ni,_cleanNumbers(data[na]));
					continue;
				}
				if(!$ar.type(model.def[ni]['default'], 'array')){
					_cleanWrite(model,ni, new model.dev[ni].type(data[na]));
					continue;
				}

				a = [];
				data[na] = data[na]||[];
				for(no = 0; no < data[na].length; no++)
					a.push(new model.def[ni].type(data[na][no]));

				_cleanWrite(model,ni,a);
			}

			return model;
		}

		// does the same as _sin, but for exporting
		function _sout(model){
			var obj = {},
				uwrap = window.ko?ko.utils.unwrapObservable:function(a){ return a; },
				tmp, ni, na, no, a;
			for(ni in model.def){
				if(blacklist.test(ni))
					continue;

				tmp = uwrap(model[ni]);

				na = 'external' in model.def[ni]?model.def[ni].external:ni;

				//gotta look for models WITHIN models
				if(!tmp){
					obj[na] = tmp;
				} else if(tmp.hasOwnProperty('serialize')){
					obj[na] = tmp.serialize();
				} else if($ar.type(tmp,'array')){
					obj[na] = [];
					for(no = 0; no < tmp.length; no++){
						a = uwrap(tmp[no]);
						if($ar.type(a,'function')) continue;
						if($ar.type(a,'object') && a.hasOwnProperty('serialize'))
							a = a.serialize();
						obj[na].push(a);
					}
				} else if($ar.type(tmp,'object')){
					obj[na] = {};
					for(no in tmp){
						a = uwrap(tmp[no]);
						if($ar.type(a,'function')) continue;
						if($ar.type(a,'object') && a.hasOwnProperty('serialize'))
							a = a.serialize();
						obj[na][no] = a;
					}
				} else {
					if($ar.type(tmp,'function')) continue;
					obj[na] = tmp;
				}
			}

			if($ar.type(model._post,'array')){
				for(ni = 0; ni < model._post.length; ni++)
					model._post[ni](obj);
			}

			return obj;
		}

		// mmmmmm factory
		return function(def){
			var self = {
				_pre: [],
				_post: [],
				errors: [],
				def: {}
			};

			// all these functions chain!!!! GO NUTS!
			self.serialize = function(data){
				// no arguments, you export data from the model
				// with an object, you import
				if(arguments.length === 0)
					return _sout(self);
				return _sin(self,data);
			};
			self.extend = function(_def){
				// use models to make bigger models!
				var ni;
				for(ni in _def){
					if(blacklist.test(ni))
						continue;
					if(ni in self.def)
						continue;
					if(!$ar.type(_def[ni],'object'))
						_def[ni] = { 'default': _def[ni] };

					self.def[ni] = $ar.extend({
						'default':'',
						validation: []
					},_def[ni]);

					self[ni] = _def[ni]['default'];
				}

				return self;
			};
			self.map = function(_maps){
				// internal name on the left side, external on the right
				// for keeping your clean data model in sync with your ugly api
				for(var ni in _maps){
					if(!self.def.hasOwnProperty(ni)) continue;
					self.def[ni].external = _maps[ni];
				}
				return self;
			};
			self.type = function(_types){
				// to have hierarchical chains of models, we need to be able
				// to specify a model type for those properties 
				for(var ni in _types){
					if(!self.def.hasOwnProperty(ni)) continue;
					self.def[ni].type = _types[ni];
				}
				return self;
			};
			self.pre = function(filter){
				// here we add filters that edit the json data before it enters
				self._pre.push(filter);
				return self;
			};
			self.post = function(filter){
				// here we add filters that edit the json data before it leaves
				self._post.push(filter);
				return self;
			};
			self.watch = function(_map){
				var ni;
				//make all the things observable!
				if(!arguments.length){
					_map = {};
					for(ni in self.def)
						_map[ni] = true;
				}
				// this bad boy controls which properties are observable
				var pass_through = function(val){ return val; };
				for(ni in _map){
					if(!self.def.hasOwnProperty(ni)) continue;
					self.def[ni].observable = _map[ni];
					self[ni] = (_map[ni]?ko['observable' + ($ar.type(self.def[ni]['default'],'array')?'Array':'')]:pass_through)(ko.utils.unwrapObservable(self[ni]));
				}
				return self;
			};
			self.validate = function(_map){
				var ni,no,v,e;
				if(!arguments.length){
					self.errors = [];

					for(ni in self.def){
						v = self.def[ni].validation||[];
						for(no = 0; no < v.length; no++){
							e = v[no](_cleanRead(self,ni));
							if(!$ar.type(e,'array')) continue;
							self.errors = self.errors.concat(e);
						}
					}
					if(!self.errors.length)
						return true;
					return false;
				}

				for(ni in _map){
					self.def[ni].validation.push(_map[ni]);
				}

				return self;
			};
			self.sync = function(opt){
				if(opt.channel){
					$ar.sub(opt.channel, function(obj){
						if(!opt.key || !obj.hasOwnProperty(opt.key) || !self.hasOwnProperty(opt.key))
							return;
						var o_id = _cleanRead(obj,opt.key),
							s_id = _cleanRead(self,opt.key);
						if(!o_id || !s_id || o_id != s_id)
							return;
						self.serialize(obj.serialize());
					});
				}

				var _pre = [],
					_post = [];
				self.loading = ko.observable(false);
				self.pre_save = function(fun){
					if(!$ar.type(fun,'function'))
						return;
					_pre.push(fun);
					return self;
				};
				self.save = function(){
					var ni, fine = true;
					for(ni = 0; ni < _pre.length; ni++)
						fine =  fine && _pre[ni]();
					if(!fine) return;

					if(!opt.api && !opt.channel)
						return;
					if(!opt.api){
						$ar.pub(opt.channel,self);
						return;
					}
					self.loading(true);
					opt.api(self.serialize(),function(resp){
						self.loading(false);

						var ni;
						for(ni = 0; ni < _post.length; ni++)
							_post[ni](resp);

						$ar.pub(opt.channel,self);
					});
				};
				self.post_save = function(fun){
					if(!$ar.type(fun,'function'))
						return;
					_post.push(fun);
					return self;
				};

				return self;
			};

			//initialization
			return self.extend(def);
		};
	})()
});
(function(){
	function registry(heading){
		var repo = {},
			self = {};
		self.register = function(name,constructor){
			repo[name] = constructor;
		};
		self.create = function(name,data){
			if(!repo.hasOwnProperty(name))
				throw new Error('$ar.' + heading + '.create: ' + name + ' not declared');
			return new repo[name](data);
		};
		self.reference = function(name){
			if(!repo.hasOwnProperty(name))
				throw new Error('$ar.' + heading + '.reference: ' + name + ' not declared');
			return repo[name];
		};

		return self;
	}
	$ar.mixin({
		models: registry('models'),
		views: registry('views')
	});
})();
//$ar.clean
//		cleans html characters out of a string
$ar.mixin({
	clean: function(str){
		if(!str || typeof str != 'string') return '';
		return str.replace('&amp;','&')
					.replace('&quot;','"')
					.replace('&lt;','<')
					.replace('&gt;','>')
					.replace('&nbsp;',' ')
					.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '')
					.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '').trim();
	}
});
$ar.models.register('price',function(data){
	return new $ar.model({
		amount: 0,
		currency: 'USD',
		r1: 0,
		r2: 0,

		type_id: 0,
		type: '',

		notes: '',
		sku: ''
	}).serialize(data);
});$ar.models.register('paged',function(data){
	var self = $ar.model({
		api: null,

		items: [],

		page: 0,
		count: 25,
		loading: false,
		total: 0,

		update: null
	}).watch({
		loading: true,
		items: true,
		page: true,
		total: true
	}).serialize(data);

	self.generateParams = function(){
		return {
			page: self.page(),
			count: self.count
		};
	};
	self.grab = function(){
		self.loading(true);
		if(!$ar.type(self.api,'function')){
			self.loading(false);
			return;
		}
		self.api(self.generateParams(), function(resp){
			self.loading(false);
			var i = self.def.items.external?self.def.items.external:'items',
				t = self.def.total.external?self.def.total.external:'total';
			self.serialize({
				items: self.items().concat(resp[i]||[]),
				total: resp[t]||0
			});
			if(!$ar.type(self.update,'function')) return;
			self.update(self);
		});
	};

	return self;
});
$ar.models.register('sort',function(data){
	return $ar.model({
		id: 0,
		sort: '',
		sort_dir: '',
		label: '',
		selected: false
	}).watch({
		selected: true
	}).serialize(data);
});
$ar.models.register('taxonomy',function(data){
	return $ar.model({
		id: 0,
		slug: '',
		name: '',
		selected: false
	}).map({
		id: 'term_id'
	}).watch({
		selected: true
	}).serialize(data);
});
$ar.models.register('destination',function(data){
	return $ar.model({
		id: 0,
		parent: 0,
		name: '',
		selected: false
	}).watch({
		selected: true
	}).serialize(data);
});
$ar.models.register('activity',function(data){
	var self = $ar.model({
		id: 0,
		activityID: 0,
		title: '',
		slug: '',
		i18n: 'en_US',
		destination: '',
		destinationID: 0,
		shortDesc: null,
		duration: null,
		tags: [],
		prices: [],
		r2: 0,
		times: [],
		thumbnail_url: '',
		display_price: 0,
		child_price: 0
	}).type({
		prices: $ar.models.reference('price')
	}).watch({
		thumbnail_url: true,
		slug: true
	}).pre(function(_data){
		if(_data.hasOwnProperty('json_input')){
			_data.id = _data.json_input.id;
			_data.prices = _data.json_input.prices;
			_data.times = _data.json_input.times;
		}
		_data.title = $ar.clean(_data.title);

		if(_data.prices && _data.prices.length){
			_data.r2 = _data.prices[0].r2;
			var out, ni;
			for(ni = 0; ni < _data.prices.length; ni++){
				if(parseInt(_data.prices[ni].display_price,10)){
					out = _data.prices[ni].amount;
					break;
				}
				if(_data.prices[ni].amount === 0)
					continue;
				if(!out){
					out = _data.prices[ni].amount;
					continue;
				}
				if(_data.prices[ni].amount < out)
					out = _data.prices[ni].amount;
			}
			_data.display_price = out;
			for(ni = 0; ni < _data.prices.length; ni++) {
				if (_data.prices[ni].type_id == 673991 || _data.prices[ni].type.includes("child") || _data.prices[ni].type.includes("Child")) {
					_data.child_price = _data.prices[ni].amount;
				}
			}
		}

		if(_data.json_input && _data.json_input.media){
			var media = _data.json_input.media,
				conf = $ar.config(),
				na;
			for(na = 0; na < media.length; na++){ 
				if(media[na].type != 'image') continue;
				_data.thumbnail_url = 'https://media.activityrez.com/media/'+media[na].hash+'/thumbnail/height/'+$ar.config().thumbnailHeight;
				break;
			}
		}

		return true;
	});

	self.url = ko.computed(function(){
		return $ar.config()['url'] + '/' + self.slug();
	});

	self.serialize(data);
	return self;
});$ar.mixin({
	view: function(_model){
		var self = {
			model: _model
		};
		var type = {};

		self.watch = function(fields){
			var out = {}, ni;
			for(ni = 0; ni < fields.length; ni++){
				out[fields[ni]] = true;
			}
			self.model.watch(out);
			return self;
		};
		self.type = function(fields){
			var ni;
			for(ni in fields){
				type[ni] = fields[ni];
			}
			return self;
		};
		self.expose = function(fields){
			var ni,no, out;
			for(ni = 0; ni < fields.length; ni++){
				self[fields[ni]] = (function(key){
					return ko.computed({
						read: function(){
							return ko.utils.unwrapObservable(self.model[key]);
						},
						write: function(val){
							if(ko.isObservable(self.model[key]))
								self.model[key](val);
							else
								self.model[key] = val;
						}
					});
				})(fields[ni]);
			}

			return self;
		};

		return self;
	}
});$ar.api.route('searchActivities','lookup','activities',function(data){
	var conf = $ar.config();
	data.data = data.data||{};
	data.data.showInWB = conf['pos'];
	data.data.showChildren = conf['children'];
	data.data.reseller2ID = conf['r2']||0;
	data.data.reseller2_userID = conf['user']||0;
	return true;
});
$ar.models.register('activity_search',(function(){
	function getSelected(obj,field,key){
		var out = [],
			ugh = ko.utils.unwrapObservable(obj[field]),
			ni;
		if(!ugh) return out;
		for(ni = 0; ni < ugh.length; ni++){
			if(!ugh[ni].selected()) continue;
			if(key)
				out.push(ugh[ni][key]);
			else
				out.push(ugh[ni]);
		}
		return out;
	}

	return function(data){
		var self = $ar.models.create('paged').extend({
			keywords: '',

			destinations: [],
			categories: [],
			tags: [],
			moods: [],
			sorts: [],
			showChildren: true,
			
			start_date: null,
			end_date: null,
			
			featured: false
		}).watch({
			keywords: true,
			start_date: true,
			end_date: true,
			lang: true,
			
			destinations: true,
			tags: true,
			categories: true,
			moods: true,
			featured: true
		}).type({
			items: $ar.models.reference('activity'),
			destinations: $ar.models.reference('destination'),
			categories: $ar.models.reference('taxonomy'),
			tags: $ar.models.reference('taxonomy'),
			moods: $ar.models.reference('taxonomy'),
			sorts: $ar.models.reference('sort'),
		}).map({
			items: 'data'
		}).serialize(data);

		self.generateParams = function(){
			var obj = {
				page: self.page()+1,
				count: self.count
			}, out;

			obj.s = self.keywords();
			//obj.showInWB = self.wb_id();

			out = getSelected(self,'destinations','name');
			if(out.length)
				obj.des = out;

			out = getSelected(self,'categories','name');
			if(out.length)
				obj.category = out;

			out = getSelected(self,'tags','name');
			if(out.length)
				obj.tag = out;

			out = getSelected(self,'moods','name');
			if(out.length)
				obj.moods = out;

			out = getSelected(self,'sorts');
			obj.sort = out[0].sort;
			obj.sortDir = out[0].sort_dir;
			
			if(self.featured())
				obj.featured = true;

			return { data:obj };
		};

		return self;
	};
})());
$ar.views.register('search_result',function(model){
	var self = new $ar.view(model).watch([
		'times',
		'prices'
	]).expose([
		'title',
		'shortDesc',
		'destination',
		'duration',
		'r2'
	]);

	self.minPrice = ko.computed(function(){
		var prices = self.model.prices()||[],
			lowest, ni;

		for(ni = 0; ni < prices.length; ni++){
			if(prices[ni].amount <= 0) continue;
			if(!lowest || prices[ni].amount < lowest)
				lowest = prices[ni].amount;
		}

		return lowest || 0;
	});
	self.active_days = ko.computed(function(){
		var times = self.model.times();
		if(!times || !times.length)
			return '';

		var active_days = '',
			days = {},
			d = {
				'Sunday': 0,
				'Monday': 1,
				'Tuesday': 2,
				'Wednesday': 3,
				'Thursday': 4,
				'Friday': 5,
				'Saturday': 6
			},
			out = [],
			ni;
		for(ni = 0; ni < times.length; ni++){
			if(!days.hasOwnProperty(times[ni].start.day))
				days[times[ni].start.day] = times[ni].start.day.substr(0,3);
		}
		//sort on key
		for(ni in days) {
			out.push(days[ni]);
		}
		out.sort( function( a, b ) {
			return d[a.name] > d[b.name];
		} );

		if( out.length == 7 ) {
			active_days = 'Every day';
		} else {
			active_days = out.join(', ');
		}

		return active_days;
	});
	self.link = function(){
		window.href = self.model.url();
	};

	return self;
});$ar.views.register('basic_search',function(model){
	var self = new $ar.view(model).expose([
		'keywords',
		'items',
		'loading'
	]).type({
		items: $ar.views.reference('search_result')
	});

	self.remaining = ko.computed(function(){
		return self.model.total() - self.items().length;
	});

	self.clear = function(){
		var ni, no;
		self.keywords('');

		self.items([]);
		self.model.page(0);
		self.model.grab();
	};
	self.search = function(){
		self.items([]);
		self.model.page(0);
		self.model.grab();
	};
	self.more = function(){
		if(self.remaining() === 0)
			return;
		self.model.page(self.model.page()+1);
		self.model.grab();
	};

	/*self.loading(true);
	$ar.init(function(){
		self.model.grab();
	});*/

	return self;
});
$ar.views.register('destination_specific_search',function(model){
	var self = $ar.views.create('basic_search',model);

	self.setDestination = function(des){
		des.selected = true;
		self.model.serialize({
			destinations: [des]
		});
		
		return self;
	};

	return self;
});

$ar.config({
    //api
    api: 'https://secure.activityrez.com/wp-content/plugins/flash-api/wsrv.php',
    cache_key: 'ACTIVITYREZ',
	children: true,
    //search
    pos: 0,
    url: '',
    timthumb:'https://media1.activityrez.com/images/',
    thumbnailHeight: 400
});