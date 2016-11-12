function showAjaxFloatBoxForChangePrivacy($id, $change_privacy_label, $actionType, $feedId) {
    privacyChangeFloatBox = OW.ajaxFloatBox('IISSECURITYESSENTIALS_CMP_PrivacyFloatBox', {
        objectId: $id,
        actionType: $actionType,
        feedId: $feedId
    }, {
        iconClass: 'ow_ic_add', title: $change_privacy_label
    });
}

function privacyChangeComplete($cmp, $id, $src, $title){
    var object = jQuery($id);
    var child = object.children();
    if(child[0] instanceof HTMLImageElement){
        var image = child;
    }else{
        child = child.children();
        var image = child;
    }
    image[0].src = $src;
    image[0].title = $title;
    image.removeData('owTip');
    $cmp.close();
}
setInterval(function(){ var toolTips = $('div[class="ow_tip ow_tip_top"]'); for(i=0; i< toolTips.length;i++){toolTips[i].style.display = 'none';} }, 10000);