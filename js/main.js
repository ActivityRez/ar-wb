(function($){

cal = function(options){
	if(!options.element){
		console.log('send an element to your calendar');
		return null;
	}

	var self = {
			element: $(options.element),
			nav_date: options.date||new Date(),
			on_select: options.on_select||null,
			selected_date: null,
			valid_date: options.is_valid||function(date){ return true; }
		};

	self.element.find('.cal-header .date').on('click',function(){
		self.nav_date = new Date();
		self.redraw();
	});
	self.element.find('.cal-header .next').on('click',function(){
		self.nav_date.setMonth(self.nav_date.getMonth()+1);
		self.redraw();
	});
	self.element.find('.cal-header .prev').on('click',function(){
		self.nav_date.setMonth(self.nav_date.getMonth()-1);
		self.redraw();
	});

	self.date = function(_date){
		if(!arguments.length) return self.nav_date;
		self.nav_date = _date;
		self.redraw();
	}

	self.redraw = function(){
		var months = ['January','February','March','April','May','June','July','August','September','October','November','December'],
			ni,day,is_disabled;

		var year = self.nav_date.getFullYear(),
			month = self.nav_date.getMonth(),
			lastLastDate = (new Date(year,month,0)).getDate(),
			firstDay = (new Date(year,month,1)).getDay(),
			lastDate = (new Date(year,month+1,0)).getDate(),
			lastDay = 6 - (new Date(year,month+1,0)).getDay(),
			anchor = self.element.find('.body');

		self.element.find('.cal-header .month').html(months[month]);
		self.element.find('.cal-header .year').html(year);
		anchor.html('');

		for(ni = firstDay-1;ni >= 0;ni--){
			anchor.append('<div class="day disabled previous">' + (lastLastDate-ni) + '</div>');
		}
		for(ni = 0; ni < lastDate; ni++){
			is_disabled = !self.valid_date(new Date(year,month,ni+1));
			is_selected = (self.selected_date && self.selected_date.valueOf() == (new Date(year,month,ni+1)).valueOf());
			day = $('<div class="day' + (is_disabled?' disabled':'') + '' + (!is_disabled && is_selected?' selected':'') + '">' + (ni+1) + '</div>');
			anchor.append(day);
			
			if(is_disabled) continue;
			
			day.on('click',(select_wrap)(new Date(year,month,ni+1)));
		}
		for(ni = 0; ni < lastDay; ni++){
			anchor.append('<div class="day disabled previous">' + (ni+1) + '</div>');
		}
	};
	function select_wrap(date){
		return function(evt){
			if(self.selected_date && self.selected_date == date){
				self.selected_date = null;
			} else {
				self.selected_date = date;
			}
			self.redraw();
			if(typeof self.on_select === 'function'){
				self.on_select(self.selected_date);
			}
		};
	}

	self.redraw();
	return self;
};

cal.create = function(){
	return $([
		'<div class="calendar">',
			'<div class="cal-header">',
				'<div class="nav prev">&#8592;</div>',
				'<div class="nav next">&#8594;</div>',
				'<div class="date">',
					'<span class="month">December</span>',
					'<span class="year">2013</span>',
				'</div>',
			'</div>',
			'<div class="days clearfix">',
				'<div class="day">S</div>',
				'<div class="day">M</div>',
				'<div class="day">T</div>',
				'<div class="day">W</div>',
				'<div class="day">T</div>',
				'<div class="day">F</div>',
				'<div class="day">S</div>',
			'</div>',
			'<div class="body clearfix">',
				'<!-- <div class="day available disabled today"></div> -->',
			'</div>',
		'</div>'].join(''));
};

WebBooker.isPhone = $(window).width() <= 479?true:false,
WebBooker.comm_range = ko.observable("This Month"),
WebBooker.comm_avg = ko.observable(0),

cal_closer = function(e){
	if($(e.target).closest('.cal-holder').length){
		return;
	}
	e.preventDefault();
	$('.calendar').removeClass('show');
	$(window).off('click', cal_closer);
}

$(document).ready(function(){

	$('#location-change').change(function(e){
	    var my_location = $(this).find('option:selected').val();
	    
	    window.location = '/pleasantactivities/wb/' + my_location;
	});
	
	$('#location-change').click(function(){
		$('.location-wrapper .locations').slideToggle();
	});

	$('.bxslider').bxSlider({'auto': true});
	if($('#header').length){
		ko.cleanNode($('#header')[0]);
		ko.applyBindings(WebBooker, $('#header')[0]);
	}
	
	if($('#footer').length){
		ko.cleanNode($('#footer')[0]);
		ko.applyBindings(WebBooker, $('#footer')[0]);
	}
	
	//needed this extra stuff to make the processing modal work
	WebBooker.showProcessing = function(){
		var mobile = $(window).width() >= 980?'940px':'95%';
		
		$('#backdrop').addClass('show');
		$('body').addClass('no-scroll');
		$('#checkout-processing').removeClass('hide');
		$('#webbooker-modals').css({'width': mobile});
	};
	
	//this hide the processing modal if there is an error
	WebBooker.CheckoutNav.processing.subscribe(function(nval){
		if(nval == false){
			$('#webbooker-modals').removeAttr('style');
			$('#backdrop').removeClass('show');
			setTimeout(function(){
				$('body').removeClass('no-scroll');
				$('.modal').addClass('hide');
			}, 500);
		}
	});
	
	//this hides the processing modal when you are redirected to confirmation
	Path.map('#/Checkout').exit(function(){
		$('#webbooker-modals').removeAttr('style');
		$('#backdrop').removeClass('show');
		setTimeout(function(){
			$('body').removeClass('no-scroll');
			$('.modal').addClass('hide');
		}, 500);
	});
	
	//reinstantiate the slider when going from activity to home page
	Path.map('#/Home').to(function(){
		$('.bxslider').bxSlider({'auto': true});
	});
		
	//create calendars
	var sr,
		first_date = false,
		start_date = cal({
		element: cal.create(),
		on_select: function(beans){
			$('#home-date-start').val((function(){
				return (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
			})());
			$('#mini-date-start').val((function(){
				return (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
			})());
			setTimeout(function(){
				start_date.element.removeClass('show');
			}, 250);
			$(window).off('click', cal_closer);
		} }),
		
		comm_start = cal({
		element: cal.create(),
		on_select: function(beans){
			$('#comm-date-start').val((function(){
				beans = (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
				return beans;
			})());
			setTimeout(function(){
				comm_start.element.removeClass('show');
			}, 250);
			WebBooker.Dashboard.agentCommissionsStartDate(beans);
			$('.commission-range').removeClass('selected');
			WebBooker.comm_range("Custom");
			WebBooker.Dashboard.reloadAgentCommissionsChart();
			$(window).off('click', cal_closer);
		} }),
		
		end_date = cal({
		element: cal.create(),
		on_select: function(beans){
			$('#home-date-end').val((function(){
				return (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
			})());
			$('#mini-date-end').val((function(){
				return (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
			})());
			setTimeout(function(){
				end_date.element.removeClass('show');
			}, 250);
			$(window).off('click', cal_closer);
		}}),
		
		comm_end = cal({
		element: cal.create(),
		on_select: function(beans){
			$('#comm-date-end').val((function(){
				beans = (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
				return beans;
			})());
			setTimeout(function(){
				comm_end.element.removeClass('show');
			}, 250);
			WebBooker.Dashboard.agentCommissionsEndDate(beans);
			$('.commission-range').removeClass('selected');
			WebBooker.comm_range("Custom");
			WebBooker.Dashboard.reloadAgentCommissionsChart();
			$(window).off('click', cal_closer);
		} }),
		
		activity_date = cal({
			element: cal.create(),
			on_select: function(beans){
				WebBooker.MiniCart.date((beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear());
			},
			is_valid: function(date){
				var activity = WebBooker.MiniCart.activity(),
					bo = WebBooker.MiniCart.blackoutDays;
				
				if(!activity) return true;

				if('0000-00-00 00:00:00' == activity.date_start){
					var ds = '2001/01/01 00:00:00';
				}else{
					var ds = activity.date_start;
				}
				if('0000-00-00 00:00:00' == activity.date_end){
					var de = '2037/01/01 00:00:00';
				}else{
					var de = activity.date_end;
				}
				
				ds = ds.replace(/-/g, '/');
				de = de.replace(/-/g, '/');
				
				var weekday = [ 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday' ],
					times = activity.times,
					lifespanDateStart = new Date(ds),
					lifespanDateEnd = new Date(de),
					today = new Date(),
					ni, diff;
					
					
				diff = Math.floor( ( today.getTime() - date.getTime() ) / 86400000 );
		
				// reject old dates
				if(today > date && diff != 0)
					return false;
				// check lifespan dates
				if(lifespanDateStart && lifespanDateEnd ){
					if( lifespanDateStart.getTime() > date.getTime() || lifespanDateEnd.getTime() < date.getTime() ){
						return false;
					}
				}
		
				// check blackout days
				for(ni = 0; ni < (bo||[]).length; ni++){
					if(bo[ni].valueOf() != date.valueOf())
						continue;
					return false;
				}
		
				var _date = date.valueOf(),
					today = new Date(),
					cutoff_hrs = parseInt(activity.cutoff_hours || 0),
					cutoff_minutes = parseInt(activity.cutoff_minutes || 0),
					clean;
				//check the calendar date against all the days the activity is on
				for(ni = 0; ni < times.length; ni++){
		
					if(times[ni].startDayOfWeek && times[ni].startDayOfWeek != weekday[date.getDay()]){
						continue;
					}
					if(times[ni].startDate != "0000-00-00 00:00:00"){
						clean = times[ni].startDate.split('/');
						if(clean.length > 1 && clean[2].length == 2) clean[2] = '20'+clean[2];
						times[ni].startDate = clean.join('/');
					}
					if(times[ni].endDate != "0000-00-00 00:00:00"){
						clean = times[ni].endDate.split('/');
						if(clean.length > 1 && clean[2].length == 2) clean[2] = '20'+clean[2];
						times[ni].endDate = clean.join('/');
					}
					
					var time_diff = Math.floor( ( ( new Date( date.getFullYear() + '/' + (date.getMonth()+1) + '/' + date.getDate() + ' ' + times[ni].startTime ) ).getTime() - today.getTime() ) / 60000 ),
						cutoff_mins = ( cutoff_hrs * 60 ) + cutoff_minutes;
					
					if( time_diff <= 0 || ( cutoff_mins >= time_diff && !activity.cfa ) ){
						return false;
					}
					if(
						( times[ni].startDate == "0000-00-00 00:00:00" || ( new Date(times[ni].startDate.replace(/-/g, '/')) ).valueOf() <= _date ) &&
						( times[ni].endDate == "0000-00-00 00:00:00" || ( new Date(times[ni].endDate.replace(/-/g, '/')) ).valueOf() >= _date )
					){
						if(!first_date){
							first_date = true;
							this.selected_date = date;
							WebBooker.MiniCart.date((date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear());
						}
						return true;
					}
				}
				return false;
			}
		}),
		
		//closers for calendars, modals andimage popups
		
		closeModal = function(){
			if($('#checkout-processing').hasClass('hide')){
				$('#webbooker-modals').removeAttr('style');
				$('#backdrop').removeClass('show');
				setTimeout(function(){
					$('body').removeClass('no-scroll');
					$('.modal').addClass('hide');
				}, 500);
			}
		},
		
		closeImage = function(){
			$('.popup').remove();
			$('#backdrop').removeClass('show');
		};
		
	//forget what this one is honestly
	WebBooker.MiniCart.activity.subscribe(function(){
		activity_date.redraw();
	});
	
	//activity image popup
	$('.activity-image').click(function(){
		var image = $(this).clone();
		$(image).addClass('popup');
		$(image).attr('src', $(image).attr('src').replace('thumbnail/width/308', 'display'));
		$('#backdrop').after(image);
		$(image)[0].onload = function(){
			$(image).addClass('loaded');
			$(image).css({'top': $(window).scrollTop() + (jQuery(window).height()/2) - ($(image).outerHeight()/2), 'margin-left': -($(image).outerWidth()/2)});
			$('body').addClass('no-scroll');
		};
		$('#backdrop').addClass('show');
	});
	
	//attach calendars
	setTimeout(function(){
		$('#home-date-start').parent().append(start_date.element);
		$('#home-date-end').parent().append(end_date.element);
	}, 0);
	
	$('#mini-date-start').parent().append(start_date.element);
	$('#mini-date-end').parent().append(end_date.element);
	$('#comm-date-start').parent().append(comm_start.element);
	$('#comm-date-end').parent().append(comm_end.element);
	$('#activity-datepicker').append(activity_date.element);

	//show modals
	$('#home-date-start').click(function(){
		$('.calendar').removeClass('show');
		$(this).siblings(start_date.element).addClass('show');
		$(window).on('click', cal_closer);
	});
	
	$('#home-date-end').click(function(){
		$('.calendar').removeClass('show');
		$(this).siblings(end_date.element).addClass('show');
		$(window).on('click', cal_closer);
	});
	
	$('#mini-date-start').click(function(){
		$('.calendar').removeClass('show');
		$(this).siblings(start_date.element).addClass('show');
		$(window).on('click', cal_closer);
	});
	
	$('#mini-date-end').click(function(){
		$('.calendar').removeClass('show');
		$(this).siblings(end_date.element).addClass('show');
		$(window).on('click', cal_closer);
	});
	
	$('#comm-date-start').click(function(){
		$('.calendar').removeClass('show');
		$(this).siblings(comm_start.element).addClass('show');
		$(window).on('click', cal_closer);
	});
	
	$('#comm-date-end').click(function(){
		$('.calendar').removeClass('show');
		$(this).siblings(comm_end.element).addClass('show');
		$(window).on('click', cal_closer);
	});
	
	$('#privacy-policy-link').click(function(e){
		var mobile = $(window).width() >= 980?'940px':'95%';
		
		e.preventDefault();
		$('#backdrop').addClass('show');
		$('body').addClass('no-scroll');
		$('#reseller-privacy-policy').removeClass('hide');
		$('#webbooker-modals').css({'width': mobile});
	});
	
	$('#terms-and-conditions-link').click(function(e){
		var mobile = $(window).width() >= 980?'940px':'95%';
		
		e.preventDefault();
		$('#backdrop').addClass('show');
		$('body').addClass('no-scroll');
		$('#reseller-agreement').removeClass('hide');
		$('#webbooker-modals').css({'width': mobile});
	});
	
	$('#cancellation-link').click(function(e){
		var mobile = $(window).width() >= 980?'940px':'95%';
		
		e.preventDefault();
		$('#backdrop').addClass('show');
		$('body').addClass('no-scroll');
		$('#cancellation-policy').removeClass('hide');
		$('#webbooker-modals').css({'width': mobile});
	});
	
	//hide modals
	$('.modal button.close, #backdrop').click(function(e){
		closeModal();
		closeImage();
	});
	
	//slide the homepage tags
	setTimeout(function(){
		var tagsListPosition = 0,
			mobile = $(window).width() >= 980?6:4;
		if($(window).width() <= 479){
			mobile = 2;
		}
		$('#quick-tags .list-nav-right').click(function(){
			var newLeft = $('.list-wrap li')[tagsListPosition+mobile].offsetLeft,
				sw = $('.list-wrap ul')[0].scrollWidth - $('.list-wrap ul')[0].offsetWidth;
			if(newLeft > sw){
				newLeft = sw;
			} else {
				tagsListPosition += mobile;
			}
			$('.list-wrap ul').css('left', 0-newLeft);
		});
		
		$('#quick-tags .list-nav-left').click(function(){
			var newLeft;

			if(tagsListPosition == 0){
				newLeft = 0;
			} else {
				newLeft = $('.list-wrap li')[tagsListPosition-mobile].offsetLeft;
			}
			if(newLeft <= 0){
				newLeft = 0;
				tagsListPosition = 0;
			} else {
				tagsListPosition -= mobile;
			}
			$('.list-wrap ul').css('left', 0-newLeft);
		});
	},1);
	
	//slide the activity images
	setTimeout(function(){
		var galleryListPosition = 0,
			mobile = $(window).width() >= 980?3:2;
			
			if($(window).width() <= 479){
				mobile = 1;
			}
		
		$('#webbooker-activity-media .list-nav-right').click(function(){
			var newLeft = $('.gallery-wrap li')[galleryListPosition+mobile].offsetLeft,
				sw = $('.gallery-wrap ul')[0].scrollWidth - $('.gallery-wrap ul')[0].offsetWidth;
			if(newLeft > sw){
				newLeft = sw;
			} else {
				galleryListPosition += mobile;
			}
			$('.gallery-wrap ul').css('left', 0-newLeft);
		});
		
		$('#webbooker-activity-media .list-nav-left').click(function(){
			var newLeft;

			if(galleryListPosition == 0){
				newLeft = 0;
			} else {
				newLeft = $('.gallery-wrap li')[galleryListPosition-mobile].offsetLeft;
			}
			if(newLeft <= 0){
				newLeft = 0;
				galleryListPosition = 0;
			} else {
				galleryListPosition -= mobile;
			}
			$('.gallery-wrap ul').css('left', 0-newLeft);
		});
	},1);
	
	$('.commission-range').click(function(){
		$('.commission-range').removeClass('selected');
		$(this).addClass('selected');
	});
	
	$('#today').click(function(){
		var today = new Date();
		
		today.setHours(0,0,0,0);
		WebBooker.comm_range("Today");
		WebBooker.Dashboard.agentCommissionsStartDate(today);
		WebBooker.Dashboard.agentCommissionsEndDate(today);
		WebBooker.Dashboard.reloadAgentCommissionsChart();
	});
	
	$('#week').click(function(){
		var today = new Date(),
			start_day = new Date(),
			end_day = new Date();
		start_day.setDate(today.getDate() - today.getDay());
		end_day.setDate(start_day.getDate() + 6);
		
		WebBooker.comm_range("This Week");
		WebBooker.Dashboard.agentCommissionsStartDate(start_day);
		WebBooker.Dashboard.agentCommissionsEndDate(end_day);
		WebBooker.Dashboard.reloadAgentCommissionsChart();
	});
	
	$('#month').click(function(){
		var today = new Date(),
			start_day = new Date(),
			end_day = new Date();
		start_day.setDate(1);
		end_day.setMonth(start_day.getMonth() + 1);
		end_day.setDate(-1);
		
		WebBooker.comm_range("This Month");
		WebBooker.Dashboard.agentCommissionsStartDate(start_day);
		WebBooker.Dashboard.agentCommissionsEndDate(end_day);
		WebBooker.Dashboard.reloadAgentCommissionsChart();
	});
	
	$('#year').click(function(){
		var today = new Date(),
			start_day = new Date(),
			end_day = new Date();
		start_day.setDate(1);
		start_day.setMonth(0);
		end_day.setMonth(11);
		end_day.setDate(31);
		
		WebBooker.comm_range("This Year");
		WebBooker.Dashboard.agentCommissionsStartDate(start_day);
		WebBooker.Dashboard.agentCommissionsEndDate(end_day);
		WebBooker.Dashboard.reloadAgentCommissionsChart();
	});
	
	//show the main menu on phones
	$('.menu-menu-1-container').click(function(e){
		if($(window).width() <= 479){
			$('#header #main-header #menu .menu-menu-1-container ul').toggleClass('show');
		}
	});
	
});

})(jQuery);

//AR CHART BITCHES
;(function(dom){
	var ChartItem = function(data){
		var self = {
			element: null,
			unit: '',
			value: [],
			stacked: false,
			max: 0,
			min: 0
		};

		var staged_remove = [],
			staged_add = [],
			old_baseline = 0;

		self.draw = function(range,offset){
			dom(document).ready(function(){
				var anchor = self.element.find('.val-container'),
					height = anchor.height(),
					zero_line = (1-(offset/range))*height;

			function after_remove(){
				var numbers = ['zero','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen'],
					ni, na;

				self.element.find('.unit').html(self.unit);
				anchor[0].className = 'val-container';
				if(!self.stacked){
					anchor.addClass(numbers[self.value.length]);
				}

				if(!staged_add.length){
					after_add();
					return;
				}

				//give time for the vals to resize
				setTimeout(function(){
					var na;
					for(na = 0; na < staged_add.length; na++){
						staged_add[na].css({
							height: 0,
							bottom: zero_line + 'px'
						});
						anchor.append(staged_add[na]);
					}
					staged_add = [];
					setTimeout(function(){
						after_add();
					},15);
				},305);
			}
			function after_add(){
				var na = self.element.find('.val'),
					s_val = [],
					curr,
					ni,no;
				for(ni = 0; ni < self.value.length; ni++){
					s_val.push([ni,self.value[ni]]);
				}
				for(ni = 0; ni < s_val.length; ni++){
					curr = s_val[ni];
					for(no = ni-1; no > -1 && s_val[no][1] < curr[1]; no--){
						s_val[no+1] = s_val[no];
					}
					s_val[no+1] = curr;
				}

				for(ni = 0; ni < s_val.length; ni++){
					curr = s_val[ni][1]/range * height;
					if(s_val[ni][1] >= 0){
						na.eq(s_val[ni][0]).css({
							left: self.stacked?0:s_val[ni][0] * (100/s_val.length) + '%',
							'z-index': ni,
							height: curr + 'px',
							bottom: zero_line + 'px'
						});
					} else {
						na.eq(s_val[ni][0]).css({
							left: self.stacked?0:s_val[ni][0] * (100/s_val.length) + '%',
							'z-index': ni,
							height: -curr + 'px',
							bottom: zero_line + curr + 'px'
						});
					}
				}

				old_baseline = zero_line;
			}

			if(!staged_remove.length){
				after_remove();
				return;
			}

			var ni;
			for(ni = 0; ni < staged_remove.length; ni++){
				staged_remove[ni].css({
					height: 0,
					bottom: old_baseline + 'px'
				});
			}
			setTimeout(function(){
				for(ni = 0; ni < staged_remove.length; ni++){
					staged_remove[ni].remove();
				}
				staged_remove = [];
				after_remove();
			},305);
			});
		};
		self.load = function(_data){
			var values = self.element.find('.val'),
				ni,na,no,i;

			if(!_data){
				return;
			}

			if(_data.stacked){
				self.stacked = true;
			} else {
				self.stacked = false;
			}

			//normalize the data values
			if(!$ar.type(_data.value,'array')){
				_data.value = [ _data.value ];
			}
			no = [];

			self.max = null;
			self.min = null;

			for(ni = 0; ni < _data.value.length; ni++){
				na = parseFloat(_data.value[ni]);
				if(isNaN(na)){
					continue;
				}
				if(self.max === null || na > self.max){
					self.max = na;
				}
				if(self.min === null || na < self.min){
					self.min = na;
				}

				no.push(na);
			}
			_data.value = no;

			for(ni = 0; ni < self.value.length - _data.value.length; ni++){
				staged_remove.push(values.eq(values._len - ni - 1));
			}
			for(ni = 0; ni < _data.value.length - self.value.length; ni++){
				staged_add.push(dom('<div class="val"></div>'));
			}

			self.unit = _data.unit;
			self.value = _data.value;
		};

		dom(document).ready(function(){
			self.element = dom(
				'<div class="col hide">' +
					'<div class="val-container"></div>' +
					'<div class="unit"></div>' +
				'</div>');

			if(data){
				self.load(data);
			}
		});

		return self;
	};
	var Chart = function(element){
		var self = {
			element: null,
			max: 0,
			min: 0,
			items: []
		};

		self.add = function(val){
			var height = self.element.height(),
				item = ChartItem();

			self.items.push(item);
			dom(document).ready(function(){
				self.element.append(item.element);
			});
		};
		self.remove = function(){
			if(!self.items.length){
				return;
			}

			var item = self.items.splice(self.items.length-1,1)[0];

			dom(document).ready(function(){
				for(var ni = 0; ni < item.value.length; ni++){
					item.value[ni] = 0;
				}
				item.draw(self.element.height());
				setTimeout(function(){
					item.element.remove();
				},305);
			});
		};
		self.load = function(data, callback){
			var len = self.items.length,
				numbers = ['zero','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen'],
				ni = len - data.length,
				no;

			function after_remove(){
				if(data.length === len){
					after_add();
					return;
				}
				self.element[0].className = 'cols';
				self.element.addClass(numbers[data.length]);
				setTimeout(function(){
					after_add();
				},305);
			}
			function after_add(){
				for(ni = 0; ni < data.length; ni++){
					if(ni >= len){
						self.add(data[ni]);
					}
					self.items[ni].load(data[ni]);
				}
				var max = null,
					min = null,
					range, offset;

				for(ni = 0; ni < self.items.length; ni++){
					if(max === null || self.items[ni].max > max){
						max = self.items[ni].max;
					}
					if(min === null || self.items[ni].min < min){
						min = self.items[ni].min;
					}
				}
				if(min > 0){
					min = 0;
				}

				(function(inc){
					var _max = (Math.ceil(max/inc))*inc,
						_min = Math.floor(min/inc)*inc;
					range = _max - _min;
					offset = _max;

					self.max = _max;
					self.min = _min;
					self.inc = inc;

					if($ar.type(callback,'function')){
						callback(self);
					}
				})(
					(function(_max,_min){
						return (_max-_min)/8;
					})(max,min)
				);

				
				dom(document).ready(function(){
					for(ni = 0; ni < self.items.length; ni++){
						self.items[ni].draw(range,offset);
					}
				});

			}

			if(len - data.length <= 0){
				after_remove();
				return;
			}
			for(ni = len - data.length; ni > 0; ni--){
				self.remove();
			}
			setTimeout(function(){
				after_remove();
			},305);
		};

		dom('document').ready(function(){
			self.element = dom(element);
		});

		return self;
	};
	function money_clamp(inp){
		inp = Math.round(parseFloat(inp)*100);
		var before = Math.floor(inp/100),
			after = inp%100;

		return '$' + before + '.' + (after < 10? '0'+after:after);
	}
	var ScaledChart = function(element){
		var self = {
			element: null,
			chart: null,
			inc: 0
		};

		self.load = function(data){
			var max = self.chart.max,
				min = self.chart.min;


			self.chart.load(data,function(){
				var anchor = self.element.find('.scale'),
					range = self.chart.max - self.chart.min,
					ni, na, no, ne, old;

				old = self.element.find('.scale .unit').css({ opacity: 0 });
				setTimeout(function(){
					old.remove();
				},300);

				for(ni = range/self.chart.inc; ni >= 0; ni--){
					ne = self.chart.min + (self.chart.inc * ni);
					na = dom('<div class="unit"><span>' + money_clamp(ne) + '</span></div>');
					if(ne === 0){
						na.addClass('zero');
					} else if(ne < 0){
						na.addClass('negative');
					}
					if(old._len){
						old.eq(0).before(na);
					} else {
						anchor.append(na);
					}
					setTimeout((function(e,h){ return function(){ e.css({ 'min-height': h,opacity: 1 }); }; })(na,(self.chart.inc*100/range) + '%'),5);
				}
			});
		};

		dom('document').ready(function(){
			self.element = dom(element);
			self.chart = Chart(self.element.find('.cols'));
		});

		return self;
	};

	$ar.mixin({
		chart: Chart,
		scaled_chart: ScaledChart
	});
})(jQuery);

jQuery(document).ready(function(){
	var beans = $ar.scaled_chart('.chart');
	function make_data(nval){
		if(!nval || !nval.data){
			return;
		}
		var diff = nval.pointEnd - nval.pointStart,
			data = [],
			split,
			ni, curr, caca, poo = 0;

		function do_split(val,_curr){
			if(diff > 31*24*60*60*1000){
				if((new Date(val)).getMonth() > (new Date(_curr)).getMonth() || ((new Date(_curr)).getMonth() === 11 && (new Date(val)).getMonth() !== 11)){
					return true;
				}
				return false;
			}
			if(diff > 7*24*60*60*1000){
				if(val-_curr >= 7*24*60*60*1000){
					return true;
				}
				return false;
			}
			if(diff > 24*60*60*1000){
				if(val-_curr >= 24*60*60*1000){
					return true;
				}
				return false;
			}
			return false;
		}
		function getMahUnitReady(val){
			var _months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
				val = new Date(val),
				mobile = jQuery(window).width() >= 980?'Week of ':'';
			
			if(diff > 31*24*60*60*1000){
				return _months[val.getMonth()]; //+ " '" + (''+val.getFullYear()).substr(-2);
			}
			if(diff > 7*24*60*60*1000){
				return mobile + (val.getMonth()+1) + "/" + val.getDate();
			}
			if(diff > 24*60*60*1000){
				return(val.getMonth()+1) + "/" + val.getDate();
			}
			return 'Today';
		}

		curr = null;
		WebBooker.comm_avg(0);
		for(ni = 0; ni < nval.data.length; ni++){
			WebBooker.comm_avg(WebBooker.comm_avg() + nval.data[ni][1]);
			if(curr === null){
				curr = nval.data[ni][0];
				caca = {
					unit: getMahUnitReady(curr),
					value: [0]
				};
			}
			if(do_split(nval.data[ni][0],curr)){
				data.push(caca);
				curr = nval.data[ni][0];
				caca = {
					unit: getMahUnitReady(curr),
					value: [0]
				};
			}
			caca.value[0] += nval.data[ni][1];
		}
		WebBooker.comm_avg(WebBooker.comm_avg()/nval.data.length);
		data.push(caca);
		beans.load(data);
	}
	WebBooker.Dashboard.agentCommissionsData.subscribe(function(nval){
		make_data(nval);
	});
	
	//load charts when you get to the dashboard
	Path.map('#/Dashboard').enter(function(){
		setTimeout(function(){
			var comm_start = cal({
			element: cal.create(),
			on_select: function(beans){
				jQuery('#comm-date-start').val((function(){
					beans = (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
					return beans;
				})());
				setTimeout(function(){
					comm_start.element.removeClass('show');
				}, 250);
				WebBooker.Dashboard.agentCommissionsStartDate(beans);
				$('.commission-range').removeClass('selected');
				WebBooker.comm_range("Custom");
				WebBooker.Dashboard.reloadAgentCommissionsChart();
				$(window).off('click', cal_closer);
			} }),
			
			comm_end = cal({
			element: cal.create(),
			on_select: function(beans){
				jQuery('#comm-date-end').val((function(){
					beans = (beans.getMonth() + 1) + '/' + beans.getDate() + '/' + beans.getFullYear();
					return beans;
				})());
				setTimeout(function(){
					comm_end.element.removeClass('show');
				}, 250);
				WebBooker.Dashboard.agentCommissionsEndDate(beans);
				jQuery('.commission-range').removeClass('selected');
				WebBooker.comm_range("Custom");
				WebBooker.Dashboard.reloadAgentCommissionsChart();
				jQuery(window).off('click', cal_closer);
			} });
			
			jQuery('#comm-date-start').parent().append(comm_start.element);
			jQuery('#comm-date-end').parent().append(comm_end.element);
			
			jQuery('#comm-date-start').click(function(){
				jQuery('.calendar').removeClass('show');
				jQuery(this).siblings(comm_start.element).addClass('show');
				jQuery(window).on('click', cal_closer);
			});
			
			jQuery('#comm-date-end').click(function(){
				jQuery('.calendar').removeClass('show');
				jQuery(this).siblings(comm_end.element).addClass('show');
				jQuery(window).on('click', cal_closer);
			});
			
			beans = $ar.scaled_chart('.chart');
			make_data(WebBooker.Dashboard.agentCommissionsData());
			
			jQuery('.commission-range').click(function(){
				jQuery('.commission-range').removeClass('selected');
				jQuery(this).addClass('selected');
			});
			
			jQuery('#today').click(function(){
				var today = new Date();
				
				today.setHours(0,0,0,0);
				WebBooker.comm_range("Today");
				WebBooker.Dashboard.agentCommissionsStartDate(today);
				WebBooker.Dashboard.agentCommissionsEndDate(today);
				WebBooker.Dashboard.reloadAgentCommissionsChart();
			});
			
			jQuery('#week').click(function(){
				var today = new Date(),
					start_day = new Date(),
					end_day = new Date();
				start_day.setDate(today.getDate() - today.getDay());
				end_day.setDate(start_day.getDate() + 6);
				
				WebBooker.comm_range("This Week");
				WebBooker.Dashboard.agentCommissionsStartDate(start_day);
				WebBooker.Dashboard.agentCommissionsEndDate(end_day);
				WebBooker.Dashboard.reloadAgentCommissionsChart();
			});
			
			jQuery('#month').click(function(){
				var today = new Date(),
					start_day = new Date(),
					end_day = new Date();
				start_day.setDate(1);
				end_day.setMonth(start_day.getMonth() + 1);
				end_day.setDate(-1);
				
				WebBooker.comm_range("This Month");
				WebBooker.Dashboard.agentCommissionsStartDate(start_day);
				WebBooker.Dashboard.agentCommissionsEndDate(end_day);
				WebBooker.Dashboard.reloadAgentCommissionsChart();
			});
			
			jQuery('#year').click(function(){
				var today = new Date(),
					start_day = new Date(),
					end_day = new Date();
				start_day.setDate(1);
				start_day.setMonth(0);
				end_day.setMonth(11);
				end_day.setDate(31);
				
				WebBooker.comm_range("This Year");
				WebBooker.Dashboard.agentCommissionsStartDate(start_day);
				WebBooker.Dashboard.agentCommissionsEndDate(end_day);
				WebBooker.Dashboard.reloadAgentCommissionsChart();
			});
		}, 0);
	});
});
