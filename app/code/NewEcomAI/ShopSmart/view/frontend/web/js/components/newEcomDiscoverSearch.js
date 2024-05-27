define([
    'jquery',
    'jquery/ui',
    'slick'
], function ($) {
    "use strict";
    var checkShowProduct = 1;
    return function discoverNewEcom(config) {
        var discoverUrl = config.discoverUrl;
        var productGridLayout = 3;
        var questionId = "";
        var contextId = "";
        var all_products = [];
        let currentSearchQuery = '';
        var questionItems = "";

        $("#NewEcomAi-search").click(function (e) {
            e.preventDefault();
            currentSearchQuery = "";
            all_products = [];
            let searchQuestion = $('#NewEcomAi-discover-question').val();
            discoverAPICall(searchQuestion,questionId);
        });
        function discoverAPICall(searchQuestion,questionId) {
            var url = discoverUrl + '?searchKey=' + searchQuestion + '&questionId=' + questionId;
            if (contextId !== "") {
                url += '&contextId=' + contextId;
            }

            $.ajax({
                url: url,
                type: "POST",
                success: function (response) {
                    all_products = [];
                    if (response.error == undefined) {
                        response.products.forEach(function(product) {
                            all_products.push(product);
                        });
                        console.log(all_products)
                        getProductGrid(all_products);
                        if(response.response.hasNext == true) {
                            let q_id = response.response.id;
                            discoverAPICall(searchQuestion,q_id);
                        }
                        if(response.response.contextId != undefined) {
                            contextId = response.response.contextId;
                        }
                    } else {
                        alert(response.error)
                    }

                },
                error: function (error, status) {
                    $('.error_msg').show();
                }
            })
        }
        function getProductGrid(responseData) {
            const searchInput = $('#NewEcomAi-discover-question').val().trim();
            if (searchInput !== currentSearchQuery) {
                // If the search query has changed, create a new slide
                currentSearchQuery = searchInput;
                addNewSlide(responseData);
            } else {
                appendProductsToExistingSlide(responseData);
            }
        }

        function addNewSlide(responseData) {
            const searchInput = $('#NewEcomAi-discover-question').val().trim();
            initializeSlick();
            $('.js-newcom-popup-content').addClass('newcom-full-width');
            $('.js-newcom-popup-content-inner').addClass('newcom-post-search');
            if ($('.js-newcom-popup-content-inner').hasClass('newcom-post-search')) {
                $(".js-newcom-heading").html("Refine your need");
            }

            // Add the search input as a slide to the carousel container
            const carouselSlide = $('<div></div>').addClass('question-item slick-slide').html(`<div class="NewEcomAi__product-box__search-query">${searchInput}</div>`);
            const stackedSlide = $('<div></div>').addClass('stack-item slick-slide');
            // Add the new slide
            $('#stackedQuestion').slick('slickAdd', carouselSlide, true);
            $('#stackedQuestion').slick('slickGoTo', $('#stackedQuestion').slick('slickCurrentSlide') - 1);

            const productCount = $('<div class="NewEcomAi__product-box__product-count">').text(checkShowProduct);
            carouselSlide.append(productCount);

            questionItems = $('<div id="productList" class="NewEcomAi__product-box__productList product-list js-newcom-product-list"></div>');

            responseData.forEach(product => {
                let colors = [];
                let sizes = [];
                if ($.isArray(product.color)) {
                    for (const [key, value] of Object.entries(product.color)) {
                        colors.push(value)
                    }
                } else {
                    colors = product.color;
                }
                if ($.isArray(product.size)) {
                    for (const [key, value] of Object.entries(product.size)) {
                        sizes.push(value)
                    }
                } else {
                    sizes = product.size;
                }

                const productColors = ($.isArray(colors) && colors.length > 0)
                    ? `<div class="NewEcomAi__product-box__variant__type product-variant-color">
                             <label>Color</label>
                             <select name="color" class="NewEcomAi__product-box__color-select-box">
                               ${colors.map(color => `<option value="${color}">${color}</option>`).join('')}
                             </select>
                           </div>`
                    : `<div class="NewEcomAi__product-box__variant__type product-variant-color">
                             <label>Color</label>
                             <div class="NewEcomAi__product-box__color-select-box">
                             <strong>${product.color}</strong>
                             </div>
                           </div>`;

                const productSizes = ($.isArray(sizes) && sizes.length > 0)
                    ? `<div class="NewEcomAi__product-box__variant__type product-variant-size obj">
                             <label>Size</label>
                             <select name="size" class="NewEcomAi__product-box__size-select-box">
                             ${sizes.map(size => `<option value="${size}">${sizes}</option>`).join('')}
                             </select>
                           </div>`
                    : `<div class="NewEcomAi__product-box__variant__type product-variant-size simple">
                             <label>Size</label>
                             <div class="NewEcomAi__product-box__color-select-box">
                             <strong>${product.size}</strong>
                             </div>
                           </div>`;

                const productText = $(`
                    <div class="products-item">
                        <div class="NewEcomAi__product-box__info product-info">
                            <div class="NewEcomAi__product-box__details product-details">
                                <div class="NewEcomAi__product-box__image product-image"><a href="${product.productUrl}" target="_blank"><img loading="lazy" src="${product.imageUrl}" alt="${product.title}"></a></div>
                                <div class="NewEcomAi__product-box__title product-title">
                                    <div class="title">
                                        <a href="${product.productUrl}" target="_blank">${product.title}</a>
                                    </div>
                                </div>
                                <div class="NewEcomAi__product-box__price product-price">$${product.price}</div>
                                <div class="NewEcomAi__product-box__variant product-variant-container">
                                ${productColors}
                                ${productSizes}
                                </div>
                            </div>
                            <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="1" name="quantity" min="1"></div>
                            <div class="NewEcomAi__product-box__add-cart">
                                <button class="NewEcomAi__popup-content__button" onclick="addToCart('${product.title}', ${product.price})">Add to cart</button>
                            </div>
                        </div>
                    </div>`);
                questionItems.append(productText);
            });
            const feedbackLine = $('<div class="NewEcomAi__product-box__feedback">A white cotton poplin shirt would complement your black jeans well.</div>');
            stackedSlide.append(feedbackLine, questionItems);
            $('#stackedList').slick('slickAdd', stackedSlide, true);
            $('#stackedList').slick('slickGoTo', $('#stackedList').slick('slickCurrentSlide') - 1);

            // Initialize slick carousel for newly added question items
            questionItems.slick({
                slidesToShow: productGridLayout,
                slidesToScroll: 1,
                autoplay: false,
                autoplaySpeed: 1000,
                infinite: false,
                draggable: false,
                arrows: true,
                speed: 400,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1.3,
                        slidesToScroll: 1,
                        speed: 300,
                        draggable: true,
                        infinite: false
                    }
                },]
            });
        }


        function appendProductsToExistingSlide(responseData) {
            // Create new product items from the response data
            const newProductItems = createProductItems(responseData);

            // Iterate over each new product item and add it as a new slide
            newProductItems.each(function() {
                const newSlide = $('<div></div>').addClass('products-item slick-slide').append($(this).html());
                $('#productList').slick('slickAdd', newSlide);
            });
        }

        function createProductItems(responseData) {
            const productItems = $('<div></div>'); // Create a container for product items

            responseData.forEach(product => {
                let colors = [];
                let sizes = [];
                if ($.isArray(product.color)) {
                    for (const [key, value] of Object.entries(product.color)) {
                        colors.push(value)
                    }
                } else {
                    colors = product.color;
                }
                if ($.isArray(product.size)) {
                    for (const [key, value] of Object.entries(product.size)) {
                        sizes.push(value)
                    }
                } else {
                    sizes = product.size;
                }

                const productColors = ($.isArray(colors) && colors.length > 0)
                    ? `<div class="NewEcomAi__product-box__variant__type product-variant-color">
                 <label>Color</label>
                 <select name="color" class="NewEcomAi__product-box__color-select-box">
                   ${colors.map(color => `<option value="${color}">${color}</option>`).join('')}
                 </select>
               </div>`
                    : `<div class="NewEcomAi__product-box__variant__type product-variant-color">
                 <label>Color</label>
                 <div class="NewEcomAi__product-box__color-select-box">
                 <strong>${product.color}</strong>
                 </div>
               </div>`;

                const productSizes = ($.isArray(sizes) && sizes.length > 0)
                    ? `<div class="NewEcomAi__product-box__variant__type product-variant-size obj">
                 <label>Size</label>
                 <select name="size" class="NewEcomAi__product-box__size-select-box">
                 ${sizes.map(size => `<option value="${size}">${size}</option>`).join('')}
                 </select>
               </div>`
                    : `<div class="NewEcomAi__product-box__variant__type product-variant-size simple">
                 <label>Size</label>
                 <div class="NewEcomAi__product-box__color-select-box">
                 <strong>${product.size}</strong>
                 </div>
               </div>`;

                const productText = $(`
            <div class="products-item">
                <div class="NewEcomAi__product-box__info product-info">
                    <div class="NewEcomAi__product-box__details product-details">
                        <div class="NewEcomAi__product-box__image product-image"><a href="${product.productUrl}" target="_blank"><img loading="lazy" src="${product.imageUrl}" alt="${product.title}"></a></div>
                        <div class="NewEcomAi__product-box__title product-title">
                            <div class="title">
                                <a href="${product.productUrl}" target="_blank">${product.title}</a>
                            </div>
                        </div>
                        <div class="NewEcomAi__product-box__price product-price">$${product.price}</div>
                        <div class="NewEcomAi__product-box__variant product-variant-container">
                        ${productColors}
                        ${productSizes}
                        </div>
                    </div>
                    <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="1" name="quantity" min="1"></div>
                    <div class="NewEcomAi__product-box__add-cart">
                        <button class="NewEcomAi__popup-content__button" onclick="addToCart('${product.title}', ${product.price})">Add to cart</button>
                    </div>
                </div>
            </div>`);
                productItems.append(productText);
            });

            return productItems.children(); // Return the individual product items
        }

        // Function for slick Initialization
        function initializeSlick() {
            if (!$('#stackedQuestion').hasClass('slick-initialized')) {
                // Initialize slick carousel for the question list
                $('#stackedQuestion').slick({
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    asNavFor: '#stackedList',
                    focusOnSelect: true,
                    infinite: false,
                    draggable: false,
                    arrows: false,
                });
            }

            if (!$('#stackedList').hasClass('slick-initialized')) {
                // Initialize slick carousel for the stacked list
                $('#stackedList').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: false,
                    autoplaySpeed: 1000,
                    infinite: false,
                    draggable: false,
                    swipe: false,
                    asNavFor: '#stackedQuestion',
                    arrows: false,
                });

            }

            if (!$('#productList').hasClass('slick-initialized')) {
                // Initialize slick carousel for the product list
                $('#productList').each(function () {
                    $(this).slick({
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        autoplay: false,
                        autoplaySpeed: 1000,
                        infinite: false,
                        draggable: false,
                        arrows: true,
                        speed: 400,
                        responsive: [{
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1.3,
                                slidesToScroll: 1,
                                speed: 300,
                                draggable: true,
                                infinite: false
                            }
                        },]
                    });
                });
            }
        }

        // Function to update settings based on slide count of question list
        function updateSliderSettings() {
            var $newcomSlider = $('#stackedQuestion');
            var $newcomSlideCount = $newcomSlider.find('.slick-slide').length;
            var windowWidth = $(window).width();

            if (windowWidth < 786) {
                $newcomSlider.slick('slickSetOption', 'slidesToShow', 1, true);
            } else if (windowWidth > 786 && $newcomSlideCount === 1) {
                $newcomSlider.slick('slickSetOption', 'slidesToShow', 1, true);
            } else {
                $newcomSlider.slick('slickSetOption', 'slidesToShow', 2, true);
            }
        }

        // Function to unslick the slider seetings when we clicked on back/cross button
        $('#NewEcomAi-reset-button, #newComclose').click(function () {
            $('.js-newcom-popup-content').removeClass('newcom-full-width');
            $('.js-newcom-popup-content-inner').removeClass('newcom-post-search');
            $(".js-newcom-heading").html("AI‑Powered Shopping Assistant – What are you looking for?");
            $('#stackedQuestion, #stackedList, #productList').slick('unslick');
            $('#stackedQuestion, #stackedList').empty();
        });
    }
});
