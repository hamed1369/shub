<?php

/**
 * IIS Terms
 */

OW::getRouter()->addRoute(new OW_Route('iisterms.admin.delete-item', 'iisterms/admin/delete-item/:id', 'IISTERMS_CTRL_Admin', 'deleteItem'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.deactivate-item', 'iisterms/admin/deactivate-item/:id', 'IISTERMS_CTRL_Admin', 'deactivateItem'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.activate-item', 'iisterms/admin/activate-item/:id', 'IISTERMS_CTRL_Admin', 'activateItem'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.deactivate-section', 'iisterms/admin/deactivate-section/:sectionId', 'IISTERMS_CTRL_Admin', 'deactivateSection'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.activate-section', 'iisterms/admin/activate-section/:sectionId', 'IISTERMS_CTRL_Admin', 'activateSection'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.ajax-save-order', 'iisterms/admin/ajax-save-order', 'IISTERMS_CTRL_Admin', 'ajaxSaveOrder'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.section-id', 'iisterms/admin/:sectionId', 'IISTERMS_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.add-version', 'iisterms/admin/add-version/:sectionId', 'IISTERMS_CTRL_Admin', 'addVersion'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.delete-version', 'iisterms/admin/delete-version/:sectionId/:version', 'IISTERMS_CTRL_Admin', 'deleteVersion'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.add.item', 'iisterms/admin/add-item', 'IISTERMS_CTRL_Admin', 'addItem'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.edit.item', 'iisterms/admin/edit-item', 'IISTERMS_CTRL_Admin', 'editItem'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin', 'iisterms/admin/', 'IISTERMS_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.activate-terms-on-join', 'iisterms/admin/activate-terms-on-join/:sectionId', 'IISTERMS_CTRL_Admin', 'activateTermsOnJoin'));
OW::getRouter()->addRoute(new OW_Route('iisterms.admin.deactivate-terms-on-join', 'iisterms/admin/deactivate-terms-on-join/:sectionId', 'IISTERMS_CTRL_Admin', 'deactivateTermsOnJoin'));

OW::getRouter()->addRoute(new OW_Route('iisterms.view-archives', 'iisterms/view-archives/:sectionId', 'IISTERMS_CTRL_Terms', 'viewArchives'));
OW::getRouter()->addRoute(new OW_Route('iisterms.comparison-archive', 'iisterms/comparison-archive/:sectionId/:version', 'IISTERMS_CTRL_Terms', 'comparisonArchive'));
OW::getRouter()->addRoute(new OW_Route('iisterms.index', 'iisterms', 'IISTERMS_CTRL_Terms', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisterms.index.section-id', 'iisterms/:sectionId', 'IISTERMS_CTRL_Terms', 'index'));
IISTERMS_BOL_Service::getInstance()->importingDefaultItems();
IISTERMS_CLASS_EventHandler::getInstance()->genericInit();