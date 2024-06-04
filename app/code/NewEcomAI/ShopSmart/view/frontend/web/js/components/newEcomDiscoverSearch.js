define([
    'jquery',
    'jquery/ui',
    'slick',
    'mage/url',
    'Magento_Ui/js/modal/alert'
], function ($,urlBuilder, alert) {
    "use strict";
    var checkShowProduct = 1;
    return function discoverNewEcom(config) {
        var discoverUrl = config.discoverUrl;
        var productAddToCartUrl = config.productAddToCartUrl;
        var discoverImageUpload = config.discoverImageUpload;
        var productGridLayout = config.productGridLayout;
        var questionId = "";
        var contextId = "";
        var allProducts = [];
        let currentSearchQuery = '';
        var questionItems = "";
        var previousSearchTerms = [];

        $("#NewEcomAi-search").click(function (e) {
            e.preventDefault();
            currentSearchQuery = "";
            let searchImageQuestion = "";
            let searchQuestion = $('#NewEcomAi-discover-question').val();
            // Save the new search term to the array
            previousSearchTerms.push(searchQuestion);
            discoverAPICall(searchQuestion,questionId);
            let checkImage = $('#image-upload').val()
            allProducts = [];
            if (checkImage) {
                let fileInput = $('#image-upload');

                searchImageQuestion = searchQuestion ? searchQuestion : 'I need something similar';
                discoverImageApi(fileInput, searchImageQuestion, questionId);
            }
            if (searchQuestion) {
                discoverAPICall(searchQuestion, questionId);
            }
        });
        function discoverAPICall(searchQuestion,questionId) {
            // Convert array to JSON string to send as a parameter
            let searchQuestions = JSON.stringify(previousSearchTerms);

            var url = discoverUrl + '?searchKeys=' + searchQuestions + '&questionId=' + questionId;
            if (contextId !== "") {
                url += '&contextId=' + contextId;
            }

            $.ajax({
                url: url,
                type: "POST",
                success: function (response) {
                    allProducts = [];
                    if (response.error == undefined) {
                        response.products.forEach(function(product) {
                            allProducts.push(product);
                        });
                        getProductGrid(response);
                        if(response.response.hasNext === true) {
                            let qId = response.response.id;
                            discoverAPICall(searchQuestion, qId);
                        }
                        if(response.response.contextId !== undefined) {
                            contextId = response.response.contextId;
                        }
                    } else {
                        getProductGrid(response);
                    }

                },
                error: function (error, status) {
                    $('.error_msg').show();
                }
            })
        }

        // Image upload functionality
        function discoverImageApi(fileInput, searchImageQuestion, questionId) {
            var discoverImageUrl = discoverImageUpload + '?searchKey=' + searchImageQuestion + '&questionId=' + questionId;
            if (contextId !== "") {
                discoverImageUrl += '&contextId=' + contextId;
            }
            var formData = new FormData();
                formData.append('image', fileInput[0].files[0]);
            $.ajax({
                url: discoverImageUrl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    allProducts = [];
                    if (response.error === undefined) {
                        response.products.forEach(function(product) {
                            allProducts.push(product);
                        });
                        getProductGrid(response);
                        if(response.response.hasNext === true) {
                            let qId = response.response.id;
                            discoverImageApi(fileInput, searchImageQuestion, qId);
                        }
                        if(response.response.contextId !== undefined) {
                            contextId = response.response.contextId;
                        }
                    } else {
                        getProductGrid(response);
                    }
                },
                error: function () {
                    $('.error_msg').show();
                }
            });
        }

        function getProductGrid(responseData) {
            const searchInput = $('#NewEcomAi-discover-question').val().trim();
            let searchImageQuestion = searchInput ? searchInput : 'I need something similar';
            if (searchImageQuestion !== currentSearchQuery) {
                // If the search query has changed, create a new slide
                currentSearchQuery = searchImageQuestion;
                addNewSlide(responseData);
            } else {
                appendProductsToExistingSlide(responseData);
            }
        }

        function addNewSlide(response) {
            var searchInput = $('#NewEcomAi-discover-question').val().trim();
            if (!searchInput)
            {
                searchInput = 'I need something similar';
                $('#NewEcomAi-discover-question').val(searchInput);
            }
            if(response.error === "No product found") {
                initializeSlick();
                $('.js-newcom-popup-content').addClass('newcom-full-width');
                $('.js-newcom-popup-content-inner').addClass('newcom-post-search');
                if ($('.js-newcom-popup-content-inner').hasClass('newcom-post-search')) {
                    $(".js-newcom-heading").html("Refine your need");
                }
                const searchInput = $('#NewEcomAi-discover-question').val().trim();
                const carouselSlide = $('<div></div>').addClass('question-item slick-slide').html(`<div class="NewEcomAi__product-box__search-query">${searchInput}</div>`);
                const stackedSlide = $('<div></div>').addClass('stack-item slick-slide');
                questionItems = $('<div id="productList" class="NewEcomAi__product-box__productList product-list js-newcom-product-list"></div>');

                $('#stackedQuestion').slick('slickAdd', carouselSlide, true);
                const productCount = $('<div class="NewEcomAi__product-box__product-count">').text("0");
                carouselSlide.append(productCount);
                const feedbackLine = $('<div class="NewEcomAi__product-box__feedback"></div>').text(response.error);
                stackedSlide.append(feedbackLine,questionItems);
                $('#stackedList').slick('slickAdd', stackedSlide, true);

                updateSliderSettings(); // Call on page load
                $(window).on('resize', updateSliderSettings); // Call on window resize

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
            } else {
                var responseData = response.products;
                initializeSlick();
                $('.js-newcom-popup-content').addClass('newcom-full-width');
                $('.js-newcom-popup-content-inner').addClass('newcom-post-search');
                if ($('.js-newcom-popup-content-inner').hasClass('newcom-post-search')) {
                    $(".js-newcom-heading").html("Refine your need");
                }
                var productColors;
                var productSizes;
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
                            colors.push(value);
                        }
                    } else {
                        colors = product.color;
                    }
                    if ($.isArray(product.size)) {
                        for (let [key, value] of Object.entries(product.size)) {
                            sizes.push(value)
                        }
                    } else {
                        sizes = product.size;
                    }

                    productColors = ($.isArray(colors) && colors.length > 0)
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
                    var sizesArray = [];
                    $.each(sizes, function(index, value) {
                        sizesArray.push(value);
                    });

                    productSizes = ($.isArray(sizesArray) && sizesArray.length > 0)
                        ? `<div class="NewEcomAi__product-box__variant__type product-variant-size obj">
                             <label>Size</label>
                             <select name="size" class="NewEcomAi__product-box__size-select-box">
                             ${sizesArray.map(size => `<option value="${size}">${size}</option>`).join('')}
                             </select>
                           </div>`
                        : `<div class="NewEcomAi__product-box__variant__type product-variant-size simple">
                             <label>Size</label>
                             <div class="NewEcomAi__product-box__color-select-box">
                             <strong>${product.size}</strong>
                             </div>
                           </div>`;
                    if (colors === null || colors === undefined) {
                        productColors = "";
                    }
                    if (sizes === null || sizes === undefined) {
                        productSizes = "";
                    }
                    const productText = $(`
                    <div class="products-item">
                        <input type="hidden" class="product-id" value="${product.id}">
                        <input type="hidden" class="question-id" value="${product.questionId}">
                        <div class="NewEcomAi__product-box__info product-info">
                            <div class="NewEcomAi__product-box__details product-details">
                                <div class="NewEcomAi__product-box__image product-image">
                                    <a href="${product.productUrl}" target="_blank">
                                        <img loading="lazy" src="${product.imageUrl}" alt="${product.title}">
                                    </a>
                                </div>
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
                                <div class="NewEcomAi__product-box__quantity">
                                    <input class="item-qty" type="number" value="1" name="quantity" min="1">
                            </div>
                            </div>
                            <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="1" name="quantity" min="1"></div>
                                </div>
                            <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="1" name="quantity" min="1"></div>
                                <div class="NewEcomAi__product-box__add-cart">
                                    <button class="NewEcomAi__popup-content__button NewEcomAi__add-to-cart">Add to cart</button>
                                </div>
                            </div>
                        </div>
                    </div>`);
                    questionItems.append(productText);
                });
                const feedbackLine = $('<div class="NewEcomAi__product-box__feedback"></div>').text(response.feedback);
                stackedSlide.append(feedbackLine, questionItems);
                $('#stackedList').slick('slickAdd', stackedSlide, true);
                $('#stackedList').slick('slickGoTo', $('#stackedList').slick('slickCurrentSlide') - 1);

                updateSliderSettings(); // Call on page load
                $(window).on('resize', updateSliderSettings); // Call on window resize

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
        }

// Function to get selected color and size options
        function getSelectedOptions(productElement) {
            let color = productElement.find('.NewEcomAi__product-box__color-select-box option:selected').val();
            let size = productElement.find('.NewEcomAi__product-box__size-select-box option:selected').val();

            let colorOption = null;
            let sizeOption = null;

            if (color) {
                colorOption = {
                    option_id: 'color', // Replace with actual option ID
                    value: color
                };
            }

            if (size) {
                sizeOption = {
                    option_id: 'size', // Replace with actual option ID
                    value: size
                };
            }

            return { colorOption, sizeOption };
        }

        $(document).on('click', '.NewEcomAi__popup-content__button.NewEcomAi__add-to-cart', function() {
            console.log("on add to acrt click");
            let productElement = $(this).closest('.products-item');
            let productId = productElement.find('.product-id').val();
            let questionId = productElement.find('.question-id').val();
            let { colorOption, sizeOption } = getSelectedOptions(productElement);
            addToCartViaAjax(productId, colorOption, sizeOption,questionId);
        });

        function addToCartViaAjax(productId, colorOption, sizeOption,questionId) {
            $.ajax({
                url: productAddToCartUrl, // URL to your custom controller
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    productId: productId,
                    questionId: questionId,
                    colorOption: colorOption,
                    sizeOption: sizeOption
                }),
                success: function(response) {
                    if (response.success) {
                        // alert('Product added to cart successfully.');
                        // Refresh the page to update the mini cart
                        require(['Magento_Customer/js/customer-data'], function (customerData) {
                            var sections = ['cart'];
                            customerData.invalidate(sections);
                            customerData.reload(sections, true);
                        });
                        location.reload();
                    } else {
                        console.log('Error adding product to cart: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('An error occurred: ' + error);
                }
            });
        }



        function appendProductsToExistingSlide(responseData) {
            if(responseData !== "undefined" ){
                let totalProductCount = responseData.length;
                checkShowProduct++;
                $(".NewEcomAi__product-box__product-count").text(checkShowProduct);
            }
            if (responseData.error !== "No product found") {
                // Create new product items from the response data
                const newProductItems = createProductItems(responseData);

                // Iterate over each new product item and add it as a new slide
                newProductItems.each(function() {
                    const newSlide = $('<div></div>').addClass('products-item slick-slide').append($(this).html());
                    $('#productList').slick('slickAdd', newSlide);
                });
            }
        }

        function createProductItems(response) {
            if (response.error !== "No product found")
            {
                var responseData = response.products;
                const productItems = $('<div></div>'); // Create a container for product items
                var productColors;
                var productSizes;
                responseData.forEach(product => {
                    let colors = [];
                    let sizes = [];
                    if ($.isArray(product.color)) {
                        for (const [key, value] of Object.entries(product.color)) {
                            colors.push(value);
                        }
                    } else {
                        colors = product.color;
                    }
                    if ($.isArray(product.size)) {
                        for (const [key, value] of Object.entries(product.size)) {
                            sizes.push(value);
                        }
                    } else {
                        sizes = product.size;
                    }

                    productColors = ($.isArray(colors) && colors.length > 0)
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
                    var sizesArray = [];
                    $.each(sizes, function(index, value) {
                        sizesArray.push(value);
                    });

                    productSizes = ($.isArray(sizesArray) && sizesArray.length > 0)
                        ? `<div class="NewEcomAi__product-box__variant__type product-variant-size">
                            <label>Size</label>
                            <select name="size" class="NewEcomAi__product-box__size-select-box">
                            ${sizesArray.map(size => `<option value="${size}">${size}</option>`).join('')}
                            </select>
                        </div>`
                        : `<div class="NewEcomAi__product-box__variant__type product-variant-size">
                            <label>Size</label>
                            <div class="NewEcomAi__product-box__color-select-box">
                            <strong>${product.size}</strong>
                            </div>
                        </div>`;
                    if (colors === null || colors === undefined) {
                        productColors = "";
                    }
                    if (sizes === null || sizes === undefined) {
                        productSizes = "";
                    }
                    const productText = $(`<div class="products-item">
                     <input type="hidden" class="product-id" value="${product.id}">
                     <input type="hidden" class="question-id" value="${product.questionId}">
                    <div class="NewEcomAi__product-box__info product-info">
                        <div class="NewEcomAi__product-box__details product-details">
                            <div class="NewEcomAi__product-box__image product-image">
                                <a href="${product.productUrl}" target="_blank">
                                    <img loading="lazy" src="${product.imageUrl}" alt="${product.title}">
                                </a>
                            </div>
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
                            <div class="NewEcomAi__product-box__quantity">
                                <input class="item-qty" type="number" value="1" name="quantity" min="1">
                    </div>
                    </div>
                    <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="1" name="quantity" min="1"></div>
                            </div>
                    <div class="NewEcomAi__product-box__quantity"><input class="item-qty" type="number" value="1" name="quantity" min="1"></div>
                            <div class="NewEcomAi__product-box__add-cart">
                                <button class="NewEcomAi__popup-content__button NewEcomAi__add-to-cart">Add to cart</button>
                            </div>
                        </div>
                   </div>
                </div>
            </div>`);
                    productItems.append(productText);
                });

                return productItems.children(); // Return the individual product items
            }
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
            $('#NewEcomAi-discover-question').val('');
        });
    }
});
