define([
    'jquery',
    'jquery/ui',
    'slick'
], function ($) {
    "use strict";
    return function productGridNewcom() {
        // Function to fetch products
        function getRelatedProducts(searchQuery) {
            // Example implementation: return some hardcoded related products
            return [
                { 
                    title: 'Austin Shirt', 
                    imageUrl: 'https://cdn.shopify.com/s/files/1/0825/7709/6985/products/2014-09-26_Spencer_Look_04_01_72d7948d-362a-4f63-8816-464ae8930b89.jpg?v=1694516502',
                    price: 168.00,
                    colors: ['Black', 'Blue', 'Grey'],
                    sizes: ['Small', 'Medium', 'Large'],
                    quantity: 1
                },
                { 
                    title: 'Poplin Blouse', 
                    imageUrl: 'https://cdn.shopify.com/s/files/1/0825/7709/6985/products/2014-08-02_Lana_Look_31_713.jpg?v=1694515793',
                    price: 328.00,
                    colors: ['Black', 'White'],
                    sizes: ['Small', 'Medium', 'Large'],
                    quantity: 1
                },
                { 
                    title: 'Pau Shirt', 
                    imageUrl: 'https://cdn.shopify.com/s/files/1/0825/7709/6985/products/2014-09-30_Ashley_Look_26_8757.jpg?v=1694516713',
                    price: 14.99,
                    colors: ['Yellow', 'Purple'],
                    sizes: ['Small','Medium', 'Large'],
                    quantity: 1
                },
                { 
                    title: 'Contrast Shirt', 
                    imageUrl: 'https://cdn.shopify.com/s/files/1/0825/7709/6985/products/2015-07-02_Ashley_26_50326_23941.jpg?v=1694521421',
                    price: 14.99,
                    colors: ['Black', 'Blue'],
                    sizes: ['Small','Medium', 'Large'],
                    quantity: 1
                }
            ];
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
                $('#productList').each(function() {
                    $(this).slick({
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        autoplay: false,
                        autoplaySpeed: 1000,
                        infinite: false,
                        draggable: false,
                        arrows: true,
                        speed: 400,
                        responsive: [
                            {
                            breakpoint: 768,
                                settings: {
                                    slidesToShow: 1.3,
                                    slidesToScroll: 1,
                                    speed: 300,
                                    draggable: true,
                                    infinite: false
                                }
                            },
                        ]
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

        $(document).ready(function () {
            // Click event when we click on the discover button
            $('.js-newcom-search').click(function() {
                const searchInput = $('#NewEcomAi-question').val().trim();

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
                    const relatedProducts = getRelatedProducts(searchInput);
                    // Create a new slide in the stacked list for the related products
                    const productCount = $('<div class="NewEcomAi__product-box__product-count">').text(relatedProducts.length);
                    carouselSlide.append(productCount);

                    const questionItems = $('<div id="productList" class="NewEcomAi__product-box__productList product-list js-newcom-product-list"></div>');

                    relatedProducts.forEach(product => {
                        const productText = $(`
                        <div class="products-item">
                            <div class="NewEcomAi__product-box__info product-info">
                                <div class="NewEcomAi__product-box__details product-details">
                                    <div class="NewEcomAi__product-box__image product-image"><a href="#" target="_blank"><img loading="lazy" src="${product.imageUrl}" alt="${product.title}"></a></div>
                                    <div class="NewEcomAi__product-box__title product-title">
                                        <div class="title">
                                            <a href="#" target="_blank">${product.title}</a>
                                        </div>
                                    </div>
                                    <div class="NewEcomAi__product-box__price product-price">$${product.price.toFixed(2)}</div>
                                    <div class="NewEcomAi__product-box__variant product-variant-container">
                                        <div class="NewEcomAi__product-box__variant__type product-variant-color">
                                            <label>Color</label>
                                            <select name="color" class="NewEcomAi__product-box__color-select-box">
                                                ${product.colors.map(color => `<option value="${color}">${color}</option>`).join('')}
                                            </select>
                                        </div>
                                        <div class="NewEcomAi__product-box__variant__type product-variant-size">
                                            <label>Size</label>
                                            <select name="size" class="NewEcomAi__product-box__size-select-box">
                                                ${product.sizes.map(size => `<option value="${size}">${size}</option>`).join('')}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="${product.quantity}" name="quantity" min="1"></div>
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

                    // Clear the search input
                    $('#NewEcomAi-question').val('');

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
                        responsive: [
                            {
                            breakpoint: 768,
                                settings: {
                                    slidesToShow: 1.3,
                                    slidesToScroll: 1,
                                    speed: 300,
                                    draggable: true,
                                    infinite: false
                                }
                            },
                        ]
                    });
                }
            });
        });

        // Function to unslick the slider seetings when we clicked on back/cross button
        $('#NewEcomAi-reset-button, #newComclose').click(function() {
            $('.js-newcom-popup-content').removeClass('newcom-full-width');
            $('.js-newcom-popup-content-inner').removeClass('newcom-post-search');
            $(".js-newcom-heading").html("AI‑Powered Shopping Assistant – What are you looking for?");
            $('#stackedQuestion, #stackedList, #productList').slick('unslick');
            $('#stackedQuestion, #stackedList').empty();
        });
    }
});
