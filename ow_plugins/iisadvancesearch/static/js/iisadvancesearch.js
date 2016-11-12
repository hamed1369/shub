function doSearch(url, id){
    searchValue = document.getElementById(id).value;
    if(searchValue.length>1) {
        loadingForResults();
        var data = {"searchValue": searchValue};
        $.ajax({
            url: url,
            type: 'post',
            dataType: "json",
            data: data,
            success: function (results) {
                searchedValue = results['searchedValue'];
                if(searchedValue == document.getElementById(id).value) {
                    $('#div_result_search_users_items').fadeOut(400, function() {
                        $('#div_result_search_users_items').empty();

                        users = results['users'];
                        $('#div_result_search_users_items').fadeIn(400);

                        resultItem = '<div id="result_search_section_users_title" class="result_search_item_title";">' + OW.getLanguageText('iisadvancesearch', 'users') + '</div>';
                        $(resultItem).appendTo($('#div_result_search_users_items'));
                        $('#result_search_section_users_title').fadeIn(400);

                        if (users.length == 0) {
                            resultItem = '<div class="result_search_item">' + OW.getLanguageText('iisadvancesearch', 'no_data_found') + '</div>';
                            $(resultItem).appendTo($('#div_result_search_users_items'));
                        }else{
                            for (i = 0; i < users.length; i++) {
                                resultItem = '<div style="display: none;" id="search_user_item_'+i+'" class="result_search_item"><a class="avatar" href="' + users[i]['url'] + '"><img src="' + users[i]['src'] + '" title="' + users[i]['title'] + '" /></a><a href="' + users[i]['url'] + '">' + users[i]['title'] + '</a></div>';
                                $(resultItem).appendTo($('#div_result_search_users_items'));
                                $('#search_user_item_'+i).fadeIn(400);
                            }
                        }

                    });


                    $('#div_result_search_forums_items').fadeOut(400, function() {
                        $('#div_result_search_forums_items').empty();

                        forum_posts = results['forum_posts'];
                        $('#div_result_search_forums_items').fadeIn(400);

                        resultItem = '<div id="result_search_section_forum_posts_title" class="result_search_item_title";">' + OW.getLanguageText('iisadvancesearch', 'forum_posts_title') + '</div>';
                        $(resultItem).appendTo($('#div_result_search_forums_items'));
                        $('#result_search_section_forum_posts_title').fadeIn(400);

                        if (forum_posts.length == 0) {
                            resultItem = '<div class="result_search_item">' + OW.getLanguageText('iisadvancesearch', 'no_data_found') + '</div>';
                            $(resultItem).appendTo($('#div_result_search_forums_items'));
                        }else{
                            for (i = 0; i < forum_posts.length; i++) {
                                resultItem = '<div style="display: none;" id="search_forum_post_item_'+i+'" class="result_search_item"><span>' + OW.getLanguageText('iisadvancesearch', 'forum_post_title') + ' <a href="' + forum_posts[i]['topicUrl'] + '">' + forum_posts[i]['title'] + '</a></span><br/><span>' + OW.getLanguageText('iisadvancesearch', 'forum_post_section_name') + ' ' + forum_posts[i]['sectionName'] +'</span><br/><span>' + OW.getLanguageText('iisadvancesearch', 'forum_post_group_name') + ' ' + forum_posts[i]['groupName'] +'</span></div>';
                                $(resultItem).appendTo($('#div_result_search_forums_items'));
                                $('#search_forum_post_item_'+i).fadeIn(400);
                            }
                        }

                    });

                    $('#div_result_search_items_information').empty();
                }
            }
        });
    }else{
        $('#div_result_search_items_information').fadeOut(400, function() {
            hideAllResult();
            $('#div_result_search_items_information').fadeIn(400);

            resultItem = '<div class="result_search_item">' + OW.getLanguageText('iisadvancesearch', 'minimum_two_character') + '</div>';
            $(resultItem).appendTo($('#div_result_search_items_information'));
        });
    }
}

function hideAllResult(){
    $('#div_result_search_users_items').empty();
    $('#div_result_search_forums_items').empty();
    $('#div_result_search_items_information').empty();
}

function createSearchElements(){
    OW.ajaxFloatBox('IISADVANCESEARCH_CMP_Search', {} , {width:700, iconClass: 'ow_ic_add'});
}

function loadingForResults(){
    if($('#div_result_search_spinner').length == 0) {
        $('<div>').attr({
            class: 'spinner',
            id: 'div_result_search_spinner'
        }).append('<div class="double-bounce1"></div><div class="double-bounce2"></div>').prependTo($('#div_result_search_items_information')[0]);
    }
}