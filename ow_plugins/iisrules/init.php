<?php

/**
 * IIS Rules
 */
OW::getRouter()->addRoute(new OW_Route('iisrules.admin', 'iisrules/admin/', 'IISRULES_CTRL_Admin', 'index'));

OW::getRouter()->addRoute(new OW_Route('iisrules.admin.delete-item', 'iisrules/admin/delete-item/:id', 'IISRULES_CTRL_Admin', 'deleteItem'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.delete-category', 'iisrules/admin/delete-category/:id', 'IISRULES_CTRL_Admin', 'deleteCategory'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.ajax-save-items-order', 'iisrules/admin/ajax-save-items-order', 'IISRULES_CTRL_Admin', 'ajaxSaveItemsOrder'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.section-id', 'iisrules/admin/:sectionId', 'IISRULES_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.add-category', 'iisrules/admin/add-category/:sectionId', 'IISRULES_CTRL_Admin', 'addCategory'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.add-item', 'iisrules/admin/add-item/:sectionId', 'IISRULES_CTRL_Admin', 'addItem'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.edit-item', 'iisrules/admin/edit-item/:id', 'IISRULES_CTRL_Admin', 'editItem'));
OW::getRouter()->addRoute(new OW_Route('iisrules.admin.edit-category', 'iisrules/admin/edit-category/:id', 'IISRULES_CTRL_Admin', 'editCategory'));
OW::getRouter()->addRoute(new OW_Route('iisrules.index.section-id', 'rules/:sectionId', 'IISRULES_CTRL_Rules', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisrules.index', 'rules', 'IISRULES_CTRL_Rules', 'index'));