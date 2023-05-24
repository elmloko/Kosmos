(function($) {
    $.timer = function(func, time, autostart) { 
        this.set = function(func, time, autostart) {
            this.init = true;
            if(typeof func == 'object') {
                var paramList = ['autostart', 'time'];
                for(var arg in paramList) {
                    if(func[paramList[arg]] != undefined) {
                        eval(paramList[arg] + " = func[paramList[arg]]");
                    }
                };
                func = func.action;
            }
            if(typeof func == 'function') {
                this.action = func;
            }
            if(!isNaN(time)) {
                this.intervalTime = time; 
            }
            if(autostart && !this.isActive) {
                this.isActive = true;
                this.setTimer();
            }
            return this;
        };
        this.once = function(time) {
            var timer = this;
            if(isNaN(time)) {
                time = 0;
            }
            window.setTimeout(function() {timer.action();}, time);
            return this;
        };
        this.play = function(reset) {
            if(!this.isActive) {
                if(reset) {
                    this.setTimer();
                }
                else {
                    this.setTimer(this.remaining);} 
                    this.isActive = true;
            }
            return this;
        };
        this.pause = function() {
            if(this.isActive) {
                this.isActive = false;
                this.remaining -= new Date() - this.last;
                this.clearTimer();
            }
            return this;
        };
        this.stop = function() {
            this.isActive = false;
            this.remaining = this.intervalTime;
            this.clearTimer();
            return this;
        };
        this.toggle = function(reset) {
            if(this.isActive) {this.pause();}
            else if(reset) {this.play(true);}
            else {this.play();}
            return this;
        };
        this.reset = function() {
            this.isActive = false;
            this.play(true);
            return this;
        };
        this.clearTimer = function() {
            window.clearTimeout(this.timeoutObject);
        };
        this.setTimer = function(time) {
            var timer = this;
            if(typeof this.action != 'function') {return;}
            if(isNaN(time)) {time = this.intervalTime;}
            this.remaining = time;
            this.last = new Date();
            this.clearTimer();
            this.timeoutObject = window.setTimeout(function() {timer.go();}, time);
        };
        this.go = function() {
            if(this.isActive) {
                this.action();
                this.setTimer();
            }
        };
        
        if(this.init) {
            return new $.timer(func, time, autostart);
        } else {
            this.set(func, time, autostart);
            return this;
        }
    };
})(jQuery);

jQuery(document).ready(function( $ ) {
    var $wrapper = $('[data-pafe-sales-pop-list]');
    $('[data-pafe-sales-pop-close]').click(function() {
        $('[data-pafe-sales-pop-item]').remove();
    });

    if ($wrapper.length > 0) {
        var option = JSON.parse($wrapper.attr('data-pafe-sales-pop-option')),
            minTime = option.min_time,
            maxTime = option.max_time, 
            random = option.random,
            times = option.times, 
            minIndex = 1,
            randomTime = Math.floor(Math.random() * (maxTime - minTime + 1) + minTime),
            maxIndex = $('[data-pafe-sales-pop-list]').find('[data-pafe-sales-pop-item]').length;


        if (random == 'yes') {
            var timer = $.timer(function() {
                $('[data-pafe-sales-pop-list]').find('[data-pafe-sales-pop-item].active').remove();
                var randomIndex = Math.floor(Math.random() * (maxIndex - minIndex + 1) + minIndex);
                $('[data-pafe-sales-pop-item]').eq(randomIndex-1).fadeIn(400).addClass('active');
            });
            
            timer.set({ time : randomTime*1000, autostart : true });
        } else {
            var timer = $.timer(function() {
                $('[data-pafe-sales-pop-list]').find('[data-pafe-sales-pop-item].active').remove();
                var item = $('[data-pafe-sales-pop-item]').index();
                $('[data-pafe-sales-pop-item]').eq(item).fadeIn(400).addClass('active');
            });
           
            timer.set({ time : times*1000, autostart : true });
        }
    }
    $(document).on('mouseover','[data-pafe-sales-pop-item].active',function() {          
        timer.toggle();
    });

    $(document).on('mouseout','[data-pafe-sales-pop-item].active',function() { 
        timer.toggle();
    }); 
}); 
    