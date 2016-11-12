<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin', 'iisupdateserver/admin', 'IISUPDATESERVER_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.add.item', 'iisupdateserver/admin/add-item', 'IISUPDATESERVER_CTRL_Admin', 'addItem'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.items', 'iisupdateserver/admin/items/:type', 'IISUPDATESERVER_CTRL_Admin', 'items'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.delete.item', 'iisupdateserver/admin/delete/item/:id', 'IISUPDATESERVER_CTRL_Admin', 'deleteItem'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.ajax.save.items.order', 'iisupdateserver/admin/ajax-save-item-order', 'IISUPDATESERVER_CTRL_Admin', 'ajaxSaveItemsOrder'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.edit.item', 'iisupdateserver/admin/edit/item/:id', 'IISUPDATESERVER_CTRL_Admin', 'editItem'));
OW::getRouter()->addRoute(new OW_Route('server', 'server', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'index'));
OW::getRouter()->addRoute(new OW_Route('server.get_item_info', 'server/get-item-info', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'getItemInfo'));
OW::getRouter()->addRoute(new OW_Route('server.get_item', 'server/get-item', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'getItem'));
OW::getRouter()->addRoute(new OW_Route('server.platform_info', 'server/platform-info', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'platformInfo'));
OW::getRouter()->addRoute(new OW_Route('server.download_platform', 'server/download-platform', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'downloadUpdatePlatform'));
OW::getRouter()->addRoute(new OW_Route('server.download_full_platform', 'server/download-full-platform', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'downloadFullPlatform'));
OW::getRouter()->addRoute(new OW_Route('server.get_items_update_info', 'server/get-items-update-info', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'getItemsUpdateInfo'));
OW::getRouter()->addRoute(new OW_Route('server.update_static_files', 'server/update-static-files', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'updateStaticFiles'));
OW::getRouter()->addRoute(new OW_Route('server.check_all_for_update', 'server/check-all-for-update', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'checkAllForUpdate'));
OW::getRouter()->addRoute(new OW_Route('server.delete_all_versions', 'server/delete-all-versions', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'deleteAllVersions'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.index', 'download', 'IISUPDATESERVER_CTRL_Iisupdateserver', 'viewDownloadPage'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.delete.by.name.and.version', 'iisupdateserver/admin/delete-item', 'IISUPDATESERVER_CTRL_Admin', 'deleteItemByNameAndBuildNumber'));
OW::getRouter()->addRoute(new OW_Route('iisupdateserver.admin.check.update.by.name', 'iisupdateserver/admin/check-item', 'IISUPDATESERVER_CTRL_Admin', 'checkUpdateItemAvailableByName'));
