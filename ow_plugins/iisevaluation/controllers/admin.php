<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisevaluation.controllers
 * @since 1.0
 */
class IISEVALUATION_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index($params)
    {
        OW::getDocument()->setTitle(OW::getLanguage()->text('iisevaluation', 'admin_evaluation_settings_heading'));
        $service = IISEVALUATION_BOL_Service::getInstance();

        $form = $service->getCategoryForm(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        $this->addForm($form);

        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $name = $_REQUEST['name'];
                $iconName = null;
                $description = $_REQUEST['description'];
                $iconName = $service->saveFile('icon', true);
                $service->saveCategory($name, $description, $iconName);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_successfully'));
                $this->redirect();
            }
        }

        $categories = $service->getAllCategories();
        $categoriesArray = array();
        foreach ($categories as $category) {
            $categoryInf = array(
                'name' => $category->name,
                'id' => $category->id,
                'count' => $service->getCountOfQuestionsOfCategory($category->id, false),
                'editUrl' => OW::getRouter()->urlForRoute('iisevaluation.admin.edit-category', array('id' => $category->id)),
                'questionsUrl' => OW::getRouter()->urlForRoute('iisevaluation.admin.questions', array('catId' => $category->id)),
                'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','delete_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.delete-category', array('id' => $category->id)) . "';}",
            );

            if ($category->icon != null) {
                $categoryInf['icon'] = $service->getFile($category->icon);
            }

            $categoriesArray[] = $categoryInf;
        }
        $this->assign('users', OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
        $this->assign('categories', $categoriesArray);
        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");
    }

    public function users($params)
    {
        $service = IISEVALUATION_BOL_Service::getInstance();
        $formFindUserByUsername = $service->getUserForm(OW::getRouter()->urlForRoute('iisevaluation.admin.users'), 'form_find_user_by_username', 'username_find');
        $this->addForm($formFindUserByUsername);

        $formAssignUserByUsername = $service->getUserForm(OW::getRouter()->urlForRoute('iisevaluation.admin.users'), 'form_assign_user_by_username', 'username_assign');
        $this->addForm($formAssignUserByUsername);

        $formUnassignedUserByUsername = $service->getUserForm(OW::getRouter()->urlForRoute('iisevaluation.admin.users'), 'form_unassigned_user_by_username', 'username_unassign');
        $this->addForm($formUnassignedUserByUsername);

        $this->assign('returnToCategory', OW::getRouter()->urlForRoute('iisevaluation.admin'));
        if (OW::getRequest()->isPost()) {
            if ($formFindUserByUsername->isValid($_POST)) {
                $username = $_REQUEST['username_find'];
                $user = null;
                if ($username != null) {
                    $user = BOL_UserService::getInstance()->findByUsername($username);
                }
                if ($user == null) {
                    OW::getFeedback()->error(OW::getLanguage()->text('iisevaluation', 'no_user_found'));
                } else {
                    $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.index.user', array('userId' => $user->id)));
                }
            }

            if ($formAssignUserByUsername->isValid($_POST)) {
                $username = $_REQUEST['username_assign'];
                $user = null;
                if ($username != null) {
                    $user = BOL_UserService::getInstance()->findByUsername($username);
                }
                if ($user != null) {
                    $service->assignUser($user->id, $username);
                    OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'assign_successfully'));
                } else {
                    OW::getFeedback()->error(OW::getLanguage()->text('iisevaluation', 'no_user_found'));
                }
            }

            if ($formUnassignedUserByUsername->isValid($_POST)) {
                $service->unassignUser($_REQUEST['username_unassign']);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'unassigned_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
            }

            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
        }

        $lockedUsers = $service->getLockedUsers();
        $lockedUsersArray = array();
        foreach ($lockedUsers as $user) {
            $lockedUsersArray[] = array(
                'id' => $user->id,
                'username' => $user->username,
                'activeUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','active_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.assign-user', array('id' => $user->userId)) . "';}",
                'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','delete_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.unassigned-user', array('username' => $user->username)) . "';}",
                'questionsUrl' => OW::getRouter()->urlForRoute('iisevaluation.index.user', array('userId' => $user->userId)),
            );
        }
        $this->assign('lockedUsers', $lockedUsersArray);

        $activeUsers = $service->getActiveUsers();
        $activeUsersArray = array();
        foreach ($activeUsers as $user) {
            $activeUsersArray[] = array(
                'id' => $user->id,
                'username' => $user->username,
                'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','delete_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.unassigned-user', array('username' => $user->username)) . "';}",
                'lockUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','lockUrl_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.lock-user', array('username' => $user->username)) . "';}",
                'questionsUrl' => OW::getRouter()->urlForRoute('iisevaluation.index.user', array('userId' => $user->userId)),
            );
        }
        $this->assign('activeUsers', $activeUsersArray);
    }


    public function assignUser($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
        }

        $service = IISEVALUATION_BOL_Service::getInstance();
        $user = BOL_UserService::getInstance()->findUserById($params['id']);
        $service->assignUser($params['id'], $user->username);
        OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'assign_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
    }

    public function unassignedUser($params)
    {
        if (!isset($params['username'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
        }

        $service = IISEVALUATION_BOL_Service::getInstance();
        $service->unassignUser($params['username']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'unassigned_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
    }

    public function lockUser($params)
    {
        if (!isset($params['username'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
        }

        $service = IISEVALUATION_BOL_Service::getInstance();
        $user = BOL_UserService::getInstance()->findByUsername($params['username']);
        $service->assignUser($user->id, $params['username'], 1);
        OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'locked_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
    }


    public function questions($params)
    {
        if (!isset($params['catId'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $catId = $params['catId'];
        $service = IISEVALUATION_BOL_Service::getInstance();
        $category = $service->getCategory($catId);
        $this->assign('categoryName', $category->name);
        $questions = $service->getQuestions($catId);
        $questionsArray = array();
        $counter = 1;
        foreach ($questions as $question) {
            $values = $service->getValuesOfQuestion($question->id);
            $countOfValues = sizeof($values);
            $questionsArray[] = array(
                'title' => $question->title,
                'id' => $question->id,
                'counter' => $counter,
                'countOfValues' => $countOfValues,
                'editUrl' => OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $question->id)),
                'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','delete_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.delete-question', array('id' => $question->id)) . "';}",
            );
            $counter++;
        }

        $form = $service->getQuestionForm(OW::getRouter()->urlForRoute('iisevaluation.admin.questions', array('catId' => $catId)), $catId);
        $this->addForm($form);
        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $title = $_REQUEST['title'];
                $description = $_REQUEST['description'];
                $weight = $_REQUEST['weight'];
                $level = $_REQUEST['level'];
                $hasDescribe = $_REQUEST['hasDescribe'] == 'on' ? true : false;
                $hasFile = $_REQUEST['hasFile'] == 'on' ? true : false;
                $hasVerification = $_REQUEST['hasVerification'] == 'on' ? true : false;
                $categoryId = $_REQUEST['categoryId'];
                $service->saveQuestion($categoryId, $title, $description, $hasDescribe, $hasFile, $hasVerification, $weight, $level);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.questions', array('catId' => $catId)));
            }
        }
        $this->assign('questions', $questionsArray);
        $this->assign('returnToCategory', OW::getRouter()->urlForRoute('iisevaluation.admin'));
        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");
    }

    public function editQuestion($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        $questionId = $params['id'];
        $question = $service->getQuestion($questionId);
        $questionForm = $service->getQuestionForm(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $questionId)), $question->categoryId, $question->title, $question->description, $question->hasDescribe, $question->hasFile, $question->hasVerification,$question->weight, $question->level);
        $this->addForm($questionForm);

        $valueForm = $service->getValueForm(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $questionId)));
        $this->addForm($valueForm);

        $this->assign('returnToQuestions', OW::getRouter()->urlForRoute('iisevaluation.admin.questions', array('catId' => $question->categoryId)));

        if (OW::getRequest()->isPost()) {
            if ($questionForm->isValid($_POST)) {
                $title = $_REQUEST['title'];
                $description = $_REQUEST['description'];
                $weight = $_REQUEST['weight'];
                $level = $_REQUEST['level'];
                $hasDescribe = $_REQUEST['hasDescribe'] == 'on' ? true : false;
                $hasFile = $_REQUEST['hasFile'] == 'on' ? true : false;
                $hasVerification = $_REQUEST['hasVerification'] == 'on' ? true : false;
                $categoryId = $_REQUEST['categoryId'];
                $service->updateQuestion($questionId, $categoryId, $title, $description, $hasDescribe, $hasFile, $hasVerification, $weight, $level);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $questionId)));
            }

            if ($valueForm->isValid($_POST)) {
                $name = $_REQUEST['name'];
                $value = $_REQUEST['value'];
                $service->saveValue($name, $value, $questionId);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $questionId)));
            }
        }


        $values = $service->getValuesOfQuestion($questionId);
        $valuesArray = array();
        foreach ($values as $value) {
            $valuesArray[] = array(
                'name' => $value->name,
                'value' => $value->value,
                'editUrl' => OW::getRouter()->urlForRoute('iisevaluation.admin.edit-value', array('id' => $value->id)),
                'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisevaluation','delete_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisevaluation.admin.delete-value', array('id' => $value->id)) . "';}",
            );
        }
        $this->assign('values', $valuesArray);
        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");
    }

    public function editCategory($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        $id = $params['id'];
        $category = $service->getCategory($id);
        $form = $service->getCategoryForm(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-category', array('id' => $id)), $category->name, $category->description, $category->icon);
        $this->addForm($form);
        $this->assign('returnToCategory', OW::getRouter()->urlForRoute('iisevaluation.admin'));
        if (!empty($category->icon)) {
            $this->assign('iconImage', $service->getFile($category->icon));
        }
        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $name = $_REQUEST['name'];
                $description = $_REQUEST['description'];
                $iconName = $service->saveFile('icon', true);
                if ($iconName == null && $category->icon != null) {
                    $iconName = $category->icon;
                }
                $service->updateCategory($id, $name, $description, $iconName);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-category', array('id' => $id)));
            }
        }
    }

    public function editValue($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        $id = $params['id'];
        $value = $service->getValue($id);
        $form = $service->getValueForm(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-value', array('id' => $id)), $value->name, $value->value);
        $this->addForm($form);
        $this->assign('returnToQuestion', OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $value->questionId)));
        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $name = $_REQUEST['name'];
                $value = $_REQUEST['value'];
                $service->updateValue($id, $name, $value);
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-value', array('id' => $id)));
            }
        }
    }

    public function deleteCategory($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        $service->deleteCategory($params['id']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'deleted_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
    }

    public function deleteValue($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        $value = $service->getValue($params['id']);
        $service->deleteValue($params['id']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'deleted_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.edit-question', array('id' => $value->questionId)));
    }

    public function deleteQuestion($params)
    {
        if (!isset($params['id'])) {
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        $service->deleteQuestion($params['id']);
        $question = $service->getQuestion($params['id']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'deleted_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.admin.questions', array('catId' => $question->categoryId)));
    }

    public function ajaxSaveCategoriesOrder()
    {
        if (!empty($_POST['category']) && is_array($_POST['category'])) {
            $service = IISEVALUATION_BOL_Service::getInstance();
            foreach ($_POST['category'] as $index => $id) {
                $category = $service->getCategory($id);
                $category->order = $index + 1;
                $service->saveCategoryByObject($category);
            }
        }
    }

    public function ajaxSaveQuestionsOrder()
    {
        if (!empty($_POST['question']) && is_array($_POST['question'])) {
            $service = IISEVALUATION_BOL_Service::getInstance();
            foreach ($_POST['question'] as $index => $id) {
                $question = $service->getQuestion($id);
                $question->order = $index + 1;
                $service->saveQuestionByObject($question);
            }
        }
    }

}