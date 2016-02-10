/*! add transition plugin for Cycle2; */
(function($) {
    "use strict";
    $.fn.cycle.transitions.scrollVert = {
        before: function(opts, curr, next, fwd) {
            opts.API.stackSlides(opts, curr, next, fwd);
            var height = opts.container.css('overflow', 'hidden').height();
            opts.cssBefore = {top: fwd ? -height : height, left: 0, opacity: 1, display: 'block', visibility: 'visible'};
            opts.animIn = {top: 0};
            opts.animOut = {top: fwd ? height : -height};
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
        }

    };
    $.fn.cycle.transitions.scrollUp = {
        before: function(opts, curr, next, fwd) {
            opts.API.stackSlides(opts, curr, next, fwd);
            var height = opts.container.css('overflow', 'hidden').height();
            opts.cssBefore = {top: height, left: 0, opacity: 1, display: 'block', visibility: 'visible'};
            opts.animIn = {top: 0};
            opts.animOut = {top: -height};
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
                
            });
        }

    };
    $.fn.cycle.transitions.scrollDown = {
        before: function(opts, curr, next, fwd) {
            opts.API.stackSlides(opts, curr, next, fwd);
            var height = opts.container.css('overflow', 'hidden').height();
            opts.cssBefore = {top: -height, left: 0, opacity: 1, display: 'block', visibility: 'visible'};
            opts.animIn = {top: 0};
            opts.animOut = {top: height};
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
        }
    };
    $.fn.cycle.transitions.scrollLeft = {
        before: function(opts, curr, next, fwd) {
            opts.API.stackSlides(opts, curr, next, fwd);
            var w = opts.container.css('overflow', 'hidden').width();
            opts.cssBefore = {left: -w, top: 0, opacity: 1, visibility: 'visible', display: 'block'};
            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0};
            opts.animIn = {left: 0, width: 'auto'};
            opts.animOut = {left: w, width: '100%'};
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
            $(next).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
        }
    };
    $.fn.cycle.transitions.scrollRight = {
        before: function(opts, curr, next, fwd) {
            opts.API.stackSlides(opts, curr, next, fwd);
            var w = opts.container.css('overflow', 'hidden').width();
            opts.cssBefore = {left: w, top: 0, opacity: 1, visibility: 'visible', display: 'block', width: '100%'};
            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0};
            opts.animIn = {left: 0, width: 'auto'};
            opts.animOut = {left: -w, width: '100%'};
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
            $(next).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
            
        }
    };
    $.fn.cycle.transitions.slideLeft =
            $.fn.cycle.transitions.slideRight =
            $.fn.cycle.transitions.slideTop =
            $.fn.cycle.transitions.slideBottom =
            $.fn.cycle.transitions.slideLeftTop =
            $.fn.cycle.transitions.slideLeftBottom =
            $.fn.cycle.transitions.slideRightTop =
            $.fn.cycle.transitions.slideRightBottom = {
                before: function(opts, curr, next, fwd) {
                    opts.API.stackSlides(opts, curr, next, fwd);
                    var w = opts.container.css('overflow', 'hidden').width();
                    var h = opts.container.css('overflow', 'hidden').height();

                    switch (opts.fx) {
                        case 'slideLeft':
                            opts.cssBefore = {left: -w, top: 0, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0};
                            opts.animOut = {left: -w, top: 0};
                            break
                        case 'slideRight':
                            opts.cssBefore = {left: w, top: 0, width: '100%', opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0, width: 'auto'};
                            opts.animOut = {left: w, top: 0, width: '100%'};
                            break
                        case 'slideTop':
                            opts.cssBefore = {left: 0, top: -h, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0};
                            opts.animOut = {left: 0, top: -h};
                            break
                        case 'slideBottom':
                            opts.cssBefore = {left: 0, top: h, width: '100%', opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0, width: 'auto'};
                            opts.animOut = {left: 0, top: h, width: '100%'};
                            break
                        case 'slideLeftTop':
                            opts.cssBefore = {left: -w, top: -h, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0};
                            opts.animOut = {left: -w, top: -h};
                            break
                        case 'slideLeftBottom':
                            opts.cssBefore = {left: -w, top: h, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0};
                            opts.animOut = {left: -w, top: h};
                            break
                        case 'slideRightTop':
                            opts.cssBefore = {left: w, top: -h, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0, width: 'auto'};
                            opts.animOut = {left: w, top: -h, width: '100%'};
                            break
                        case 'slideRightBottom':
                            opts.cssBefore = {left: w, top: h, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0, width: 'auto'};
                            opts.animOut = {left: w, top: h, width: '100%'};
                            break
                        default:
                            opts.cssBefore = {left: -w, top: -h, opacity: 1, visibility: 'visible', display: 'block'};
                            opts.cssAfter = {zIndex: opts._maxZ - 2, left: 0, top: 0, width: '100%'};
                            opts.animIn = {left: 0, top: 0};
                            opts.animOut = {left: -w, top: -h};
                    }


                },
                after: function(opts, curr, next, fwd) {
                    $(curr).css({
                        top: 0,
                        left: 0,
                opacity:1,
                transform: 'none'
                    });
                    $(next).css({
                        top: 0,
                        left: 0,
                opacity:1,
                transform: 'none'
                    });
                }

            };
/////////////////////////////
    $.fn.cycle.transitions.cover = {
        transition: function(opts, currEl, nextEl, fwd, callback) {
            var uncover = false;
            if ((fwd && opts.coverEnterAnimation == 'uncover')
                    || (!fwd && opts.coverExitAnimation == 'uncover')) {
                uncover = true;
            }

            $(nextEl).css({
                display: 'block',
                visibility: 'visible'
            });
            opts.container.css('overflow', 'hidden');
            var width = opts.container.width();
            var height = opts.container.height();
            var speed = opts.speed;
            var element = uncover ? currEl : nextEl;

            opts = opts.API.getSlideOpts(uncover ? opts.currSlide : opts.nextSlide);
            var props1 = {left: -width, top: 0};
            if (fwd) {
                if (opts.coverEnterPosition == 'top') {
                    props1 = {left: 0, top: -height};
                }
                else if (opts.coverEnterPosition == 'right') {
                    props1 = {left: width, top: 0};
                }
                else if (opts.coverEnterPosition == 'bottom') {
                    props1 = {left: 0, top: height};
                }
            }
            else {
                props1 = {left: width, top: 0};
                if (opts.coverExitPosition == 'top') {
                    props1 = {left: 0, top: -height};
                }
                else if (opts.coverExitPosition == 'left') {
                    props1 = {left: -width, top: 0};
                }
                else if (opts.coverExitPosition == 'bottom') {
                    props1 = {left: 0, top: height};
                }
            }
            var props2 = {left: 0, top: 0};

            if (uncover) {
                $(element)
                        .animate(props1, speed, opts.easing, callback)
                        .queue('fx', $.proxy(reIndex, this))
                        .animate(props2, 0, opts.easing, callback);
            }
            else {
                $(element)
                        .css(props1)
                        .queue('fx', $.proxy(reIndex, this))
                        .animate(props2, speed, opts.easing, callback);
            }

            function reIndex(nextFn) {
                this.stack(opts, currEl, nextEl, fwd);
                nextFn();
            }
        },
        stack: function(opts, currEl, nextEl, fwd) {
            var i, z;

            var f = fwd;
            if ((fwd && opts.coverEnterAnimation == 'uncover')
                    || (!fwd && opts.coverExitAnimation == 'uncover')) {
                f = !fwd;
            }

            if (f) {
                z = 1;
                for (i = opts.nextSlide - 1; i >= 0; i--) {
                    $(opts.slides[i]).css('zIndex', z++);
                }
                for (i = opts.slideCount - 1; i > opts.nextSlide; i--) {
                    $(opts.slides[i]).css('zIndex', z++);
                }
                $(nextEl).css('zIndex', opts.maxZ);
                $(currEl).css('zIndex', opts.maxZ - 1);
            }
            else {
                opts.API.stackSlides(nextEl, currEl, fwd);
                $(currEl).css('zIndex', 1);
            }
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
        }


    };
    $.fn.cycle.transitions.shuffle = {
        preInit: function(optionHash) {
            console.log(optionHash.container[0]);
            jQuery(optionHash.container[0]).css('overflow', 'visible');
        },
        transition: function(opts, currEl, nextEl, fwd, callback) {
            $(nextEl).css({
                display: 'block',
                visibility: 'visible'
            });
            var width = opts.container.css('overflow', 'visible').width();
            var speed = opts.speed / 2; // shuffle has 2 transitions
            var element = fwd ? currEl : nextEl;

            opts = opts.API.getSlideOpts(fwd ? opts.currSlide : opts.nextSlide);
            var props1 = {left: -width, top: 15};
            var props2 = opts.slideCss || {left: 0, top: 0};

            if (opts.shuffleLeft !== undefined) {
                props1.left = props1.left + parseInt(opts.shuffleLeft, 10) || 0;
            }
            else if (opts.shuffleRight !== undefined) {
                props1.left = width + parseInt(opts.shuffleRight, 10) || 0;
            }
            if (opts.shuffleTop) {
                props1.top = opts.shuffleTop;
            }

            // transition slide in 3 steps: move, re-zindex, move
            $(element)
                    .animate(props1, speed, opts.easeIn || opts.easing)
                    .queue('fx', $.proxy(reIndex, this))
                    .animate(props2, speed, opts.easeOut || opts.easing, callback);

            function reIndex(nextFn) {
                /*jshint validthis:true */
                this.stack(opts, currEl, nextEl, fwd);
                nextFn();
            }
        },
        stack: function(opts, currEl, nextEl, fwd) {
            var i, z;

            if (fwd) {
                opts.API.stackSlides(nextEl, currEl, fwd);
                // force curr slide to bottom of the stack
                $(currEl).css('zIndex', 1);
            }
            else {
                z = 1;
                for (i = opts.nextSlide - 1; i >= 0; i--) {
                    $(opts.slides[i]).css('zIndex', z++);
                }
                for (i = opts.slideCount - 1; i > opts.nextSlide; i--) {
                    $(opts.slides[i]).css('zIndex', z++);
                }
                $(nextEl).css('zIndex', opts.maxZ);
                $(currEl).css('zIndex', opts.maxZ - 1);
            }
        },
        after: function(opts, curr, next, fwd) {
            $(curr).css({
                top: 0,
                left: 0,
                opacity:1,
                transform: 'none'
            });
        }

    };

    $.fn.cycle.transitions.tileSlideHorz =
            $.fn.cycle.transitions.tileBlindHorz =
            $.fn.cycle.transitions.tileSlide =
            $.fn.cycle.transitions.tileBlind = {
                before: function(opts, curr, next, fwd) {
                    opts.API.stackSlides(curr, next, fwd);
                    $(curr).css({
                        display: 'block',
                        visibility: 'visible'
                    });
                    opts.container.css('overflow', 'hidden');
                    // set defaults
                    opts.tileDelay = opts.tileDelay || opts.fx == 'tileSlide' ? 100 : 125;
                    opts.tileCount = opts.tileCount || 10;
                    opts.tileVertical = opts.tileVertical !== false;
                    if ((opts.fx == 'tileSlideHorz') || (opts.fx == 'tileBlindHorz')) {
                        opts.tileVertical = false;
                    }
                    if (!opts.container.data('cycleTileInitialized')) {
                        opts.container.on('cycle-destroyed', $.proxy(this.onDestroy, opts.API));
                        opts.container.data('cycleTileInitialized', true);
                    }
                },
                transition: function(opts, curr, next, fwd, callback) {
                    opts.slides.not(curr).not(next).css('visibility', 'hidden');

                    var tiles = $();
                    var $curr = $(curr), $next = $(next);
                    var tile, tileWidth, tileHeight, lastTileWidth, lastTileHeight,
                            num = opts.tileCount,
                            vert = opts.tileVertical,
                            height = opts.container.height(),
                            width = opts.container.width();

                    if (vert) {
                        tileWidth = Math.floor(width / num);
                        lastTileWidth = width - (tileWidth * (num - 1));
                        tileHeight = lastTileHeight = height;
                    }
                    else {
                        tileWidth = lastTileWidth = width;
                        tileHeight = Math.floor(height / num);
                        lastTileHeight = height - (tileHeight * (num - 1));
                    }

                    // opts.speed = opts.speed / 2;
                    opts.container.find('.cycle-tiles-container').remove();

                    var animCSS;
                    var tileCSS = {left: 0, top: 0, overflow: 'hidden', position: 'absolute', margin: 0, padding: 0};
                    if (vert) {
                        animCSS = (opts.fx == 'tileSlide' || opts.fx == 'tileSlideHorz') ? {top: height} : {width: 0};
                    }
                    else {
                        animCSS = (opts.fx == 'tileSlide' || opts.fx == 'tileSlideHorz') ? {left: width} : {height: 0};
                    }

                    var tilesContainer = $('<div class="cycle-tiles-container"></div>');
                    tilesContainer.css({
                        zIndex: $curr.css('z-index'),
                        overflow: 'visible',
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        direction: 'ltr' // #250
                    });
                    tilesContainer.insertBefore(next);

                    for (var i = 0; i < num; i++) {
                        tile = $('<div></div>')
                                .css(tileCSS)
                                .css({
                                    width: ((num - 1 === i) ? lastTileWidth : tileWidth),
                                    height: ((num - 1 === i) ? lastTileHeight : tileHeight),
                                    marginLeft: vert ? ((i * tileWidth)) : 0,
                                    marginTop: vert ? 0 : (i * tileHeight)
                                })
                                .append($curr.clone().css({
                                    position: 'relative',
                                    maxWidth: 'none',
                                    width: $curr.width(),
                                    margin: 0, padding: 0,
                                    marginLeft: vert ? -(i * tileWidth) : 0,
                                    marginTop: vert ? 0 : -(i * tileHeight)
                                }));
                        tiles = tiles.add(tile);
                    }

                    tilesContainer.append(tiles);
                    $curr.css('visibility', 'hidden');
                    $next.css({
                        opacity: 1,
                        display: 'block',
                        visibility: 'visible'
                    });
                    animateTile(fwd ? 0 : num - 1);

                    opts._tileAniCallback = function() {
                        $next.css({
                            display: 'block',
                            visibility: 'visible'
                        });
                        $curr.css('visibility', 'hidden');
                        tilesContainer.remove();
                        callback();
                    };

                    function animateTile(i) {
                        tiles.eq(i).animate(animCSS, {
                            duration: opts.speed,
                            easing: opts.easing,
                            complete: function() {
                                if (fwd ? (num - 1 === i) : (0 === i)) {
                                    opts._tileAniCallback();
                                }
                            }
                        });

                        setTimeout(function() {
                            if (fwd ? (num - 1 !== i) : (0 !== i)) {
                                animateTile(fwd ? (i + 1) : (i - 1));
                            }
                        }, opts.tileDelay);
                    }
                },
                // tx API impl
                stopTransition: function(opts) {
                    opts.container.find('*').stop(true, true);
                    if (opts._tileAniCallback)
                        opts._tileAniCallback();
                },
                // core API supplement
                onDestroy: function(e) {
                    var opts = this.opts();
                    opts.container.find('.cycle-tiles-container').remove();
                },
                after: function(opts, curr, next, fwd) {
                    $(curr).css({
                        top: 0,
                        left: 0,
                opacity:1,
                transform: 'none'
                    });
                }

            };

})(jQuery);
