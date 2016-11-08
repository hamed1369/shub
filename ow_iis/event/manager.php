<?php

/**
 * User: Hamed Tahmooresi
 * Date: 1/3/2016
 * Time: 3:17 PM
 */
class IISEventManager
{
    /**
     * این رویداد پس از آغاز نشست فراخوانی می شود. هدف از ایجاد این رویداد  اجرای کد مربوط به مقابله با حمله csrf  بوده است. که با فراخوانی تابع secureOxwallAgainstCSRF واقع در کلاس IISSecurityProvider محقق می گردد.
     */
    const ON_AFTER_SESSION_START = 'iis.on_after_session_start';

    /*
     * goal: Overriding term's field in join form.
     * goal: Password validation should be updated to be a valid password.
     * category: security
     * used by plugins: iischangepasswordinterval,iisterms
     */
    const ON_RENDER_JOIN_FORM = 'iis.on_render_join_form';
    const ON_AFTER_PASSWORD_UPDATE = 'iis.on_after_password_update';

    const ON_USER_AUTH_FAILED = 'iis.on_user_auth_failed';
    const ON_BEFORE_FORM_SIGNIN_RENDER = 'iis.on_before_form_signin_render';
    const ON_CAPTCHA_VALIDATE_FAILED = 'iis.on_captcha_validate_failed';
    const ON_AFTER_SIGNIN_FORM_CREATED = 'iis.on_after_signin_form_created';

    /*
     * goal: Password value should be checked to be a valid password.
     * category: security
     * used by plugins: iispasswordstrengthmeter
     */
    const ON_PASSWORD_VALIDATION_IN_JOIN_FORM = 'iis.on_password_validation_in_join_form';

    /*
     * goal: We should check user's password validation before reset password form renderer. If users are invalid, we should redirect them to change password from token that sent to their email.
     * category: security
     * used by plugins: iischangepasswordinterval
     */
    const ON_BEFORE_RESET_PASSWORD_FORM_RENDERER = 'iis.on_before_reset_password_form_renderer';

    /*
     * goal: Remove data backup using timestamp.
     * category: security
     * parameter: timestamp
     * used by plugins: iisdatabackup
     */
    const ON_DATA_BACKUP_DELETE = 'iis.on_manage_data_backup_time';
    /**
     * goal: change date format to jalali according to iisjalali setting
     * category: date format plugin
     * used by plugins: iisjalali
     */
    const ON_AFTER_DEFAULT_DATE_VALUE_SET = 'iis.on_after_default_date_value_set';

    /**
     * goal: if date is jalali format change it to gregorian  for validating
     * category: date format plugin
     * used by plugins: iisjalali
     */
    const ON_BEFORE_VALIDATING_FIELD = 'iis.on_before_validating_field';
    /**
     * goal: show date format specially jalali format according to admin settings (exmp : only month , FULL DATE WITH HOUR etc)
     * category: date format plugin
     * used by plugins: iisjalali
     */
    const ON_RENDER_FORMAT_DATE_FIELD = 'iis.on_render_format_date_field';

    /*
     * goal: All removed data backup in created tables.
     * category: security
     * used by plugins: core
     */
    const ON_AFTER_SQL_IMPORT_IN_INSTALLING = 'iis.on.after.sql.import.in.install';

    /*
     * goal: Do anything after installation completed
     * category: installation
     * used by plugins:
     */
    const ON_AFTER_INSTALLATION_COMPLETED = 'iis.on.after.installation.completed';

    /*
     * goal: Do anything before update activity timestamp of user
     * category: user
     * used by plugins: iis security essential
     */
    const ON_BEFORE_UPDATE_ACTIVITY_TIMESTAMP = 'iis.on.before.update.activity.timestamp';

    /*
     *goal: render each video file properly instead of showing static thumb picture in main page
     * category: video plugin
     * used by plugins: video
     */
    const ON_AFTER_VIDEO_RENDERED = 'iis.on_after_video_rendered';

    /*
     *goal: Add parent email field in registration form
     * category: kids control
     * used by plugins: iiskidscontrol
     */
    const ON_BEFORE_JOIN_FORM_RENDER = 'iis.on_before_join_form_render';


    /*
     * goal: checking privacy of object
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_OBJECT_RENDERER = 'iis.on.before.object.renderer';

    /*
     * goal: add privacy field into status form
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_UPDATE_STATUS_FORM_RENDERER = 'iis.on.before.update.status.form.renderer';

    /*
     * goal: assign privacy attribute into status component
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_AFTER_UPDATE_STATUS_FORM_RENDERER = 'iis.on.after.update.status.form.renderer';

    /*
     * goal: add privacy field into photo upload form
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_PHOTO_UPLOAD_FORM_RENDERER = 'iis.on.before.photo.upload.form.renderer';

    /*
     * goal: assign privacy attribute into photo component
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_PHOTO_UPLOAD_COMPONENT_RENDERER = 'iis.on.before.photo.upload.component.renderer';

    /*
     * goal: add privacy field into video upload form
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_VIDEO_UPLOAD_FORM_RENDERER = 'iis.on.before.video.upload.form.renderer';

    /*
     * goal: assign privacy attribute into video component
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_VIDEO_UPLOAD_COMPONENT_RENDERER = 'iis.on.before.video.upload.component.renderer';

    /*
     * goal: checking privacy
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_PRIVACY_CHECK = 'iis.on.before.privacy.value.check';

    /*
     * goal: decide to show update status form
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_UPDATE_STATUS_FORM_CREATE = 'iis.on.before.update.status.form.create';

    /*
     * goal: decide to show update status form
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_UPDATE_STATUS_FORM_CREATE_IN_PROFILE = 'iis.on.before.update.status.form.create.in.profile';

    /*
     * goal: decide to create feed from activity
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_FEED_ACTIVITY_CREATE = 'iis.on.before.feed.activity.create';

    /*
     * goal: change privacy in query
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_QUERY_FEED_CREATE = 'iis.on.query.feed.create';

    /*
     * goal: decide to show feed to viewer
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_FEED_ITEM_RENDERER = 'iis.on.before.feed.item.renderer';

    /*
     * goal: Show feed privacy
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_FEED_ITEM_RENDERER = 'iis.on.feed.item.renderer';

    /*
     * goal: Show album privacy
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_ALBUMS_RENDERER = 'iis.on.before.albums.renderer';

    /*
     * goal: Show album privacy
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_ALBUM_INFO_RENDERER = 'iis.on.before.album.info.renderer';
    /*
    *goal: render each video file properly instead of showing static thumb picture in main page
    * category: video plugin
    * used by plugins: video
    */
    const ON_BEFORE_FEED_RENDERED = 'iis.on_before_feed_rendered';

    /*
     * goal: checking the last photo of album in moving photos
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_AFTER_LAST_PHOTO_FEED_REMOVED = 'iis.on.after.last.photo.feed.removed';

    /*
     * goal: change name of album
     * category: privacy
     * used by plugin: iis-security-essentials
     */
    const ON_BEFORE_ALBUM_CREATE_FOR_STATUS_UPDATE = 'iis.on.before.album.create.for.status.update';

    /*
     * goal: replace base url of item in render
     * category: privacy
     * used by plugin: base
     */
    const ON_AFTER_NEWSFEED_STATUS_STRING_READ = 'iis.on.after.newsfeed.status.string.read';

    /*
     * goal: replace base url of item in writing to db
     * category: privacy
     * used by plugin: base
     */
    const ON_BEFORE_NEWSFEED_STATUS_STRING_WRITE = 'iis.on.before.newsfeed.status.string.write';


    /*
     * goal: replace base url of item in render
     * category: privacy
     * used by plugin: base
     */
    const ON_AFTER_NOTIFICATION_STRING_READ = 'iis.on.after.notification.string.read';

    /*
     * goal: replace base url of item in writing to db
     * category: privacy
     * used by plugin: base
     */
    const ON_BEFORE_NOTIFICATION_STRING_WRITE = 'iis.on.before.notification.string.write';

    /*
     * goal: add item to video
     * category: video
     * used by plugin: iisvideoplus
     */
    const ADD_LIST_TYPE_TO_VIDEO = 'iis.add.list.type.to.video';

    /*
     * goal: return list of video for some listType
     * category: video
     * used by plugin: iisvideoplus
     */
    const GET_RESULT_FOR_LIST_ITEM_VIDEO = 'iis.get.result.for.list.item.video';

    /*
     * goal: set title of page
     * category: video
     * used by plugin: iisvideoplus
     */
    const SET_TILE_HEADER_LIST_ITEM_VIDEO = 'iis.set.title.header.list.item.video';
    
    
        /*
     * goal: add aparat video provider to oxwall video providers
     * category: video
     * used by plugin: iisaparatsupport
     */
    const ON_AFTER_VIDEO_PROVIDERS_DEFINED = 'iis.on.after.video.providers.defined';

    /*
    * goal: add privacy field to questions data
    * category: privacy
    * used by plugin: iis-security-essentials
    */
    const ON_BEFORE_QUESTIONS_DATA_PROFILE_RENDER = 'iis.on.before.questions.data.profile.render';


    /*
    * goal: add extra validation on video code
    * category: video
    * used by plugin: iisaparatsupport
    */
    const ON_VIDEO_URL_VALIDATION = 'iis.on.video.url.validation';


    /*
    * goal: add extra validation on video code
    * category: video
    * used by plugin: iisaparatsupport
    */
    const ON_BEFORE_VIDEO_ADD = 'iis.on.before.video.add';

    /*
    * goal: add verify later text into email verify page
    * category: login
    * used by plugin: iis-security-essentials
    */
    const ON_BEFORE_EMAIL_VERIFY_FORM_RENDER = 'iis.before.email.verify.form.render';

    /*
    * goal: check diff of extensions
    * category: install
    * used by plugin:
    */
    const ON_BEFORE_INSTALL_EXTENSIONS_CHECK = 'iis.on.before.install.extensions.check';

    /*
    * goal: set default cover of album
    * category: photo
    * used by plugin:
    */
    const ON_ALBUM_DEFAULT_COVER_SET = 'iis.on.album.default.cover.set';

    /*
    * goal: set error
    * category: error
    * used by plugin:
    */
    const ON_BEFORE_ERROR_RENDER = 'iis.on.before.error.render';

    /*
    * goal: get passwrod requirement password strength label
    * category: security
    * used by plugin: iispasswordstrengthmeter
    */
    const GET_PASSWORD_REQUIREMENT_PASSWORD_STRENGTH_INFORMATION = 'iis.get.password.requirement.password.strength.information';

    /*
    * goal: alter query must executed for backup tables
    * category: security
    * used by plugin: core
    */
    const BEFORE_ALTER_QUERY_EXECUTED = 'iis.before.alter.query.executed';

    /*
    * goal: set default value for privacy items
    * category: security
    * used by plugin: iis-security-essentials
    */
    const ON_BEFORE_PRIVACY_ITEM_ADD = 'iis.on.before.privacy.item.add';

    /*
    * goal: checking load more functionality
    * category: performance
    * used by plugin:
    */
    const ON_BEFORE_ACTIONS_LIST_RETURN = 'iis.on.before.actions.list.return';

    /*
    * goal: change date format from gregorian to jalali for iisnews plugin and blog
    * category: core
    * used by plugin: iisjalali
    */
    const CHANGE_DATE_FORMAT_TO_JALALI_FOR_BLOG_AND_NEWS = 'iis.change.date.format.to.jalali.for.blog.and.news;';

    /*
    * goal: change date format from gregorian to jalali for iisnews plugin and blog
    * category: core
    * used by plugin: iisjalali
    */
    const CHANGE_DATE_FORMAT_TO_GREGORIAN_FOR_BLOG_AND_NEWS = 'iis.change.date.format.to.gregorian.for.blog.and.news;';
    
    /*
    * goal: change birthday range from gregorian format to jalali
    * category: core
    * used by plugin: iisjalali
    */
    const SET_BIRTHDAY_RANGE_TO_JALALI = 'iis.set.birthday.range.to.jalali';

    /*
    * goal: change date range from gregorian format to jalali
    * category: core
    * used by plugin: iisjalali
    */
    const CHANGE_DATE_RANGE_TO_JALALI = 'iis.change.date.range.to.jalali;';

    /*
    * goal: show user's information to other users or not
    * category: privacy
    * used by plugin: iis-security-essentials
    */
    const ON_BEFORE_USER_INFORMATION_RENDER = 'iis.on.before.user.information.render';
    
    /*
   * goal: calculate maximum day of a jalali month
   * category: core
   * used by plugin: iisjalali
   */
    const CALCULATE_JALALI_MONTH_LAST_DAY = 'iis.calculate.jalali.month.last.day;';

    /*
    * goal: check if image upload form rendered to upload image ina rich textbox change template to blank.htm
    * category: core
    * used by plugin:
    */
    const CHECK_MASTER_PAGE_BLANK_HTML_FOR_UPLOAD_IMAGE_FORM = 'iis.check.master.page.blank.html.for.upload.image.form';

    /*
    * goal: Decide to show currency field in setting or not
    * category: usability
    * used by plugin:
    */
    const ON_BEFORE_CURRENCY_FIELD_APPEAR = 'iis.on.before.currency.field.appear';

    /*
     * correct sentences contain multiple language alignment
     */
    const CORRECT_MULTIPLE_LANGUAGE_SENTENCE_ALIGNMENT = 'iis.correct.multiple.language.sentence.alignment';

    /*
    * goal: validate html content
    * category: security
    * used by plugin:
    */
    const ON_VALIDATE_HTML_CONTENT = 'iis.on.validate.html.content';

    /*
    * goal: check tidy extension is enabled
    * category: security
    * used by plugin:
    */
    const BEFORE_ALLOW_CUSTOMIZATION_CHANGED = 'iis.on.before.allow.customization.changed';

    /*
    * goal: disabled allow customization when tidy extension is not enabled
    * category: security
    * used by plugin:
    */
    const BEFORE_CUSTOMIZATION_PAGE_RENDERER = 'iis.on.before.customization.page.renderer';

    /*
    * goal: correct display of partial hafspace code do to truncated sentences
    * category: core
    * used by plugin:
    */
    const PARTIAL_HALF_SPACE_CODE_DISPLAY_CORRECTION = 'iis.partial.half.space.code.display.correction';
    /*
    * goal: disable index_status checkbox in admin in newsfeed configuration
    * category: newsfeed
    * used by plugin: iis-security-essentials
    */
    const ON_BEFORE_INDEX_STATUS_ENABLED= 'iis.on.before.index.status.enabled';
    /*
    * goal: add class with name: ow_required_star for required fields
    * category: base
    * used by plugin:
    */
    const DISTINGUISH_REQUIRED_FIELD= 'iis.distinguish.required.field';
    /*
     * goal: add item to photo
     * category: photo
     * used by plugin: iisphotoplus
     */
    const ADD_LIST_TYPE_TO_PHOTO = 'iis.add.list.type.to.photo';
    /*
     * goal: return list of video for some listType
     * category: video
     * used by plugin: iisphotoplus
     */
    const GET_RESULT_FOR_LIST_ITEM_PHOTO = 'iis.get.result.for.list.item.photo';
    /*
     * goal: set title of page
     * category: photo
     * used by plugin: iisphotoplus
     */
    const SET_TILE_HEADER_LIST_ITEM_PHOTO = 'iis.set.title.header.list.item.photo';
    /*
     * goal: add friend_photo to valid list
     * category: photo
     * used by plugin: iisphotoplus
     */
    const GET_VALID_LIST_FOR_PHOTO = 'iis.get.valid.list.for.photo';

    /*
    * goal: check if config of iis-security-essentials exist and return it's value
    * category: base
    * used by plugin: iis-security-essentials
    */
    const CHECK_VIEW_USER_COMMENT_WIDGET_STATUS= 'iis.check.view.user.comment.widget.status';
    /*
     * goal: prevent to show plugins that don't have mobile version
     * category: mobile
     * used by plugin: newsfeed
     */
    const ON_AFTER_GET_TPL_DATA = 'iis.on.after.get.tpl.data';
    /*
    * goal: create privacy field
    * category: security
    * used by plugin: iis-security-essentials
    */
    const ON_BEFORE_CREATE_FORM_USING_FIELD_PRIVACY = 'iis.on.before.create.form.using.field.privacy';

    /*
     * goal: show privacy button
     * category: mobile
     * used by plugin: video
     */
    const ON_BEFORE_VIDEO_RENDER = 'on.before.video.render';

    /*
     * goal: show privacy button
     * category: mobile
     * used by plugin: photo
     */
    const ON_BEFORE_PHOTO_RENDER = 'on.before.photo.render';
    /*
     * goal: check if mobile version is used
     * category: mobile
     * used by plugin: base
     */
    const IS_MOBILE_VERSION = 'iis.is.mobile.version';
}