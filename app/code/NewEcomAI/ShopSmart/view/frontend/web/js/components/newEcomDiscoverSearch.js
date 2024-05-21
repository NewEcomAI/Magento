define([
    'jquery',
    'jquery/ui',
    'slick'
], function ($) {
    "use strict";
    var responseData;
    var check = true;
    return function discoverNewEcom(config) {
        var discoverUrl = config.discoverUrl;
        var productGridLayout = config.productGridLayout;
        $(document).ready(function () {
            $("#NewEcomAi_popup_discover_search_form").submit(function (e) {
                e.preventDefault();
                let searchQuestion = $('#NewEcomAi-discover-question').val();
                var questionId = "";
                if (questionId === "") {

                    discoverAPICall(questionId);
                }

                function discoverAPICall(questionId) {
                    $.ajax({
                        url: discoverUrl + '?searchKey=' + searchQuestion + '&questionId=' + questionId,
                        showLoader: false,
                        type: "POST",
                        success: function (response) {
                            if (response) {
                                responseData = response;
                                if (responseData.response.hasNext) {
                                    discoverAPICall(responseData.response.id);
                                }
                                if (check) {
                                    getProductGrid(responseData);
                                }
                            } else {
                                $('.custom_error').text(response.message)
                            }
                            $('#NewEcomAi-discover-question').val('');
                        },
                        error: function (error, status) {
                            $('.error_msg').show();
                        }
                    })
                }
            });

            function getProductGrid(responseData) {
                check = false;
                const searchInput = $('#NewEcomAi-discover-question').val().trim();

                if (searchInput === '') {
                    alert('Write your question');
                } else {
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

                    // Get related products based on search query
                    const relatedProducts = getRelatedProducts(responseData);
                    // Create a new slide in the stacked list for the related products
                    const productCount = $('<div class="NewEcomAi__product-box__product-count">').text(relatedProducts.length);
                    carouselSlide.append(productCount);

                    const questionItems = $('<div id="productList" class="NewEcomAi__product-box__productList product-list js-newcom-product-list"></div>');

                    relatedProducts.forEach(product => {
                        const productColors = (Array.isArray(product.colors) && product.colors.length > 0)
                            ? `<div class="NewEcomAi__product-box__variant__type product-variant-color">
                                 <label>Color</label>
                                 <select name="color" class="NewEcomAi__product-box__color-select-box">
                                   ${product.colors.map(color => `<option value="${color}">${color}</option>`).join('')}
                                 </select>
                               </div>`
                            : `<div class="NewEcomAi__product-box__variant__type product-variant-color">
                                 <label>Color</label>
                                 <div name="color" class="NewEcomAi__product-box__color-select-box">
                                 <strong>${product.colors}</strong>
                                 </div>
                               </div>`;

                        const productSizes = (Array.isArray(product.sizes) && product.sizes.length > 0)
                            ? `<div class="NewEcomAi__product-box__variant__type product-variant-size">
                                 <label>Size</label>
                                 <select name="size" class="NewEcomAi__product-box__size-select-box">
                                     ${product.sizes.map(size => `<option value="${size}">${size}</option>`).join('')}
                                 </select>
                               </div>`
                            : `<div class="NewEcomAi__product-box__variant__type product-variant-size">
                                 <label>Size</label>
                                 <div name="size" class="NewEcomAi__product-box__color-select-box">
                                 <strong>${product.sizes}</strong>
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

                    updateSliderSettings(); // Call on page load
                    $(window).on('resize', updateSliderSettings); // Call on window resize

                    // Clear the search input (we should clear the input after ajax request is sent so moved it to ajax success response)
                    // $('#NewEcomAi-question').val('');

                    // Initialize slick carousel for newly added question items
                    questionItems.slick({
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
                }
            }

            // // Click event when we click on the discover button
            // $('.js-newcom-search').click(function() {
            //
            // });
        });

        // Function to fetch products
        function getRelatedProducts(responseData) {
            let colors = []
            let sizes = []
            if (Array.isArray(responseData.color)) {
                for (const [key, value] of Object.entries(responseData.color)) {
                    colors.push(value)
                }
            } else {
                colors = responseData.color
            }
            if (Array.isArray(responseData.size)) {
                for (const [key, value] of Object.entries(responseData.size)) {
                    sizes.push(value)
                }
            } else {
                sizes = responseData.size
            }
            return [{
                title: responseData.title,
                imageUrl: responseData.imageUrl,
                price: responseData.price,
                colors: colors,
                sizes: sizes,
                productUrl: responseData.productUrl,
                quantity: 1
            }];
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
