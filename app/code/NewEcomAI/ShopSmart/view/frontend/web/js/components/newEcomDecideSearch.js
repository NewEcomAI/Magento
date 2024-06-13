define([
    'jquery',
    'jquery/ui'
], function ($) {
    "use strict";
    return function decideSearchNewEcom(config) {
        $(document).ready(function() {
            var searchResultsArray = [];
            var decideSearchUrl = config.decideSearchUrl;
            var decideSearchQuestionRateUrl = config.decideSearchQuestionRateUrl;
            var currentProductTitle = config.currentProductTitle;
            var currentProductDescription = config.currentProductDescription;
            var questionId = "";
            let currentSearchQuery = '';
            var responseProductInfo = [];
            var currentSearchId = null; // To track the current search

            $("#NewEcomAi-search").click(function() {
                let searchText = $("#NewEcomAi-question").val().trim();

                if (searchText === "") {
                    alert("Write your question");
                } else {
                    if (questionId === "") {
                        $('body').trigger('processStart');
                        currentSearchId = new Date().getTime(); // Unique ID for each search
                        decideSearchAPICall(searchText, questionId, currentProductTitle, currentProductDescription, currentSearchId);
                    }
                }
            });

            function showLoader() {
                $("#loader").show();
            }

            function hideLoader() {
                $("#loader").hide();
            }

            function searchResult(response, searchId) {
                let searchText = $("#NewEcomAi-question").val().trim();
                let searchResponseData = response;
                responseProductInfo.push(searchResponseData);
                if (searchText !== currentSearchQuery) {
                    currentSearchQuery = searchText;
                    showDecideResponse(searchResponseData, searchId);
                } else {
                    appendShowDecideResponse(searchResponseData, searchId);
                }
            }

            function appendShowDecideResponse(response, searchId) {
                var responseData = response.response;
                var additionalInfo = `<div class="container"><p class="paragraph">${responseData}</p></div>`;
                let searchResult = {
                    searchText: currentSearchQuery,
                    additionalInfo: additionalInfo,
                    feedbackGiven: false
                };
                searchResultsArray.push(searchResult);
                // Append the response to the correct search result div
                $(`#search-result-${searchId} .newcom-query-response`).last().html(additionalInfo);
                if (response.hasNext === false) {
                    $("#NewEcomAi-question").val("");
                }
            }

            function showDecideResponse(response, searchId) {
                let searchResultsDiv = $("#NewEcomAi-search-result");
                let responseData = response.response;
                let additionalInfo = "";
                if (response.error) {
                    additionalInfo = `<div class="container"><p class="paragraph">${response.error}</p></div>`;
                } else {
                    additionalInfo = `<div class="container"><p class="paragraph">${responseData}</p></div>`;
                }
                let searchResult = {
                    searchText: currentSearchQuery,
                    additionalInfo: additionalInfo,
                    feedbackGiven: false
                };
                searchResultsArray.push(searchResult);
                // Create a new div for the new search result with a unique id
                var resultDiv = $(`<div id="search-result-${searchId}">`).addClass("newcom-search-history");
                var searchResultNode = $("<p>").addClass("newcom-search-query");
                var searchResultInner = $("<span>").text(searchResult.searchText);
                var additionalInfoNode = $("<p>").addClass("newcom-query-response").html(searchResult.additionalInfo);
                var iconsContainer = $("<div>").addClass("newcom-icons-container");
                var likeIcon = $("<i>").addClass("newcom-like-icon").html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16"><path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733c.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"></path></svg>');
                var dislikeIcon = $("<i>").addClass("newcom-dislike-icon").html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16"><path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z"></path></svg>');

                iconsContainer.append(likeIcon, dislikeIcon);
                searchResultNode.append(searchResultInner);
                resultDiv.append(searchResultNode, additionalInfoNode, iconsContainer);
                if (searchResult.feedbackGiven) {
                    var feedbackText = $("<p>").addClass("newcom-feedback-text").text("Thanks for your feedback!");
                    resultDiv.append(feedbackText);
                    iconsContainer.css("display", "none");
                } else {
                    let searchQuestionId = response.id;
                    likeIcon.click(function() {
                        let score = "like";
                        questionFeedback(score, searchQuestionId);
                        searchResult.feedbackGiven = true;
                        var feedbackText = $("<p>").addClass("newcom-feedback-text").text("Thanks for your feedback!");
                        resultDiv.append(feedbackText);
                        iconsContainer.css("display", "none");
                    });

                    dislikeIcon.click(function() {
                        let score = "dislike";
                        questionFeedback(score, searchQuestionId);
                        searchResult.feedbackGiven = true;
                        var feedbackText = $("<p>").addClass("newcom-feedback-text").text("Thanks for your feedback!");
                        resultDiv.append(feedbackText);
                        iconsContainer.css("display", "none");
                    });
                }
                searchResultsDiv.prepend(resultDiv);
                $("NewEcomAi-discover-question").val("");
            }

            function decideSearchAPICall(searchQuestion, questionId, currentProductTitle, currentProductDescription, searchId) {
                var url = decideSearchUrl + '?searchKey=' + searchQuestion + '&questionId=' + questionId + '&currentProductTitle=' + currentProductTitle + '&currentProductDescription=' + currentProductDescription;
                $.ajax({
                    url: url,
                    type: "POST",
                    success: function(response) {
                        let searchResponseData = response.response;
                        searchResult(searchResponseData, searchId);
                        $('body').trigger('processStop');
                        if (response.response.hasNext == true) {
                            let qId = response.response.id;
                            decideSearchAPICall(searchQuestion, qId, currentProductTitle, currentProductDescription, searchId);
                        }
                        if (response.response.hasNext == false) {
                            currentSearchQuery = "";
                        }
                    },
                    error: function(error, status) {
                        $('.error_msg').show();
                    }
                });
            }

            function questionFeedback(score, questionId) {
                var url = decideSearchQuestionRateUrl + '?score=' + score + '&questionId=' + questionId;
                $.ajax({
                    url: url,
                    type: "POST",
                    success: function(response) {},
                    error: function(error, status) {
                        $('.error_msg').show().text("An error occurred while processing your request");
                    }
                });
            }
        });

    }
});
