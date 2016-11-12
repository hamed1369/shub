<?php

/**
 * iisevaluation
 */

OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin', 'iisevaluation/admin', 'IISEVALUATION_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.ajax-save-categories-order', 'iisevaluation/admin/ajax-save-categories-order', 'IISEVALUATION_CTRL_Admin', 'ajaxSaveCategoriesOrder'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.ajax-save-questions-order', 'iisevaluation/admin/ajax-save-questions-order', 'IISEVALUATION_CTRL_Admin', 'ajaxSaveQuestionsOrder'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.edit-category', 'iisevaluation/admin/edit-category/:id', 'IISEVALUATION_CTRL_Admin', 'editCategory'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.edit-question', 'iisevaluation/admin/edit-question/:id', 'IISEVALUATION_CTRL_Admin', 'editQuestion'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.edit-value', 'iisevaluation/admin/edit-value/:id', 'IISEVALUATION_CTRL_Admin', 'editValue'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.delete-category', 'iisevaluation/admin/delete-category/:id', 'IISEVALUATION_CTRL_Admin', 'deleteCategory'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.delete-question', 'iisevaluation/admin/delete-question/:id', 'IISEVALUATION_CTRL_Admin', 'deleteQuestion'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.delete-value', 'iisevaluation/admin/delete-value/:id', 'IISEVALUATION_CTRL_Admin', 'deleteValue'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.questions', 'iisevaluation/admin/questions/:catId', 'IISEVALUATION_CTRL_Admin', 'questions'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.users', 'iisevaluation/admin/users', 'IISEVALUATION_CTRL_Admin', 'users'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.unassigned-user', 'evaluation/unassigned/:username', 'IISEVALUATION_CTRL_Admin', 'unassignedUser'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.assign-user', 'evaluation/assign-user/:id', 'IISEVALUATION_CTRL_Admin', 'assignUser'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.admin.lock-user', 'evaluation/lock-user/:username', 'IISEVALUATION_CTRL_Admin', 'lockUser'));


OW::getRouter()->addRoute(new OW_Route('iisevaluation.index', 'evaluation', 'IISEVALUATION_CTRL_Evaluation', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.results', 'evaluation/results', 'IISEVALUATION_CTRL_Evaluation', 'results'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.results.user', 'evaluation/results/:userId', 'IISEVALUATION_CTRL_Evaluation', 'results'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.index.user', 'evaluation/:userId', 'IISEVALUATION_CTRL_Evaluation', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.questions', 'evaluation/questions/:catId', 'IISEVALUATION_CTRL_Evaluation', 'questions'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.questions.user', 'evaluation/questions/:catId/:userId', 'IISEVALUATION_CTRL_Evaluation', 'questions'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.question', 'evaluation/question/:id', 'IISEVALUATION_CTRL_Evaluation', 'question'));
OW::getRouter()->addRoute(new OW_Route('iisevaluation.question.user', 'evaluation/question/:id/:userId', 'IISEVALUATION_CTRL_Evaluation', 'question'));




IISEVALUATION_CLASS_EventHandler::getInstance()->init();