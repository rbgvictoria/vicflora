/* 
 * Copyright 2016 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$(function() {
    swipe();
});

var swipe = function() {
    $('body').append(pswpElement());
    var $pswp = $('.pswp')[0];
    var image = [];
    
    $('.thumbnail-row, .glossary-image-gallery').each( function() {
        var $pic     = $(this);
        
        var getItems = function() {
            var items = [];
            $pic.find('a').each(function() {
                var $href   = $(this).attr('href'),
                    $size   = $(this).data('size').split('x'),
                    $width  = $size[0],
                    $height = $size[1];
                var title = $(this).attr('data-caption');

                var item = {
                    src : $href,
                    w   : $width,
                    h   : $height,
                    title: title
                };

                items.push(item);
            });
            return items;
        };

        var items = getItems();
        console.log(items);
        $.each(items, function(index, value) {
            image[index]     = new Image();
            image[index].src = value['src'];
        });

        $pic.on('click', 'figure, .glossary-image', function(event) {
            event.preventDefault();
            
            var $index = $(this).index();
            var options = {
                index: $index,
                bgOpacity: 0.85,
                showHideOpacity: true
            };

            var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
            lightBox.init();
        });
    });
};

var pswpElement = function() {
    return heredoc(function() {/*
        <!-- Root element of PhotoSwipe. Must have class pswp. -->
        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

            <div class="pswp__bg"></div>
            <div class="pswp__scroll-wrap">

                <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                </div>

                <div class="pswp__ui pswp__ui--hidden">

                    <div class="pswp__top-bar">

                        <div class="pswp__counter"></div>

                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                        <!--button class="pswp__button pswp__button--share" title="Share"></button-->
                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                              <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div> 
                    </div>

                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                    </button>

                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                    </button>

                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>
                </div>
            </div>
        </div>                    
    */})
}

var heredoc = function(f) {
    return f.toString().match(/\/\*\s*([\s\S]*?)\s*\*\//m)[1];
};



