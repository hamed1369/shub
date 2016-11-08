<?php

class IISEVALUATION_CTRL_Evaluation extends OW_ActionController
{

    public function index($params)
    {
        $service = IISEVALUATION_BOL_Service::getInstance();
        if(!$service->checkUserPermission()){
            $this->redirect(OW_URL_HOME);
        }

        $showAnotherUserAnswer = $params['userId'] && OW::getUser()->isAdmin();
        $categories = $service->getAllCategories();
        $categoriesArray = array();
        foreach ($categories as $category) {
            $categoryInf = array(
                'name'=> $category->name,
                'id' => $category->id,
                'countOfQuestions' => $service->getCountOfQuestionsOfCategory($category->id),
            );

            if($showAnotherUserAnswer){
                $categoryInf['url'] = OW::getRouter()->urlForRoute('iisevaluation.questions.user', array('catId' => $category->id, 'userId' => $params['userId']));
            }else{
                $categoryInf['url'] = OW::getRouter()->urlForRoute('iisevaluation.questions', array('catId' => $category->id));
            }

            if($category->icon!=null) {
                $categoryInf['icon'] = $service->getFile($category->icon);
            }

            if($showAnotherUserAnswer){
                $categoryInf['countOfAnswers'] = $service->getCountOfAnswersOfCategory($category->id, $params['userId']);
            }else{
                $categoryInf['countOfAnswers'] = $service->getCountOfAnswersOfCategory($category->id, OW::getUser()->getId());
            }

            $categoriesArray[] = $categoryInf;
        }

        if($showAnotherUserAnswer){
            $this->assign('returnToAdminUrl', OW::getRouter()->urlForRoute('iisevaluation.admin.users'));
            $this->addComponent('sections',$service->getAdminSections($service->getInstance()->SECTION_INDEX,$params['userId']));
        }else{
            $this->addComponent('sections',$service->getAdminSections($service->getInstance()->SECTION_INDEX, OW::getUser()->getId()));
        }

        $this->assign('categories', $categoriesArray);
        $this->assign('defaultIcon', Ow::getPluginManager()->getPlugin('iisevaluation')->getStaticUrl() . 'images/default.png');
        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");
    }

    public function results($params){
        $service = IISEVALUATION_BOL_Service::getInstance();
        if(!$service->checkUserPermission()){
            $this->redirect(OW_URL_HOME);
        }
        $userId = OW::getUser()->getId();
        if(isset($params['userId']) && OW::getUser()->isAdmin()){
            $userId = $params['userId'];
        }
        $this->addComponent('sections',$service->getAdminSections($service->getInstance()->SECTION_RESULTS, $userId));
        $degree = $service->getAggregateUserResult($userId);
        $this->assign('degree', $degree);
        $arrayJS = $service->getUserResult($userId);

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iisevaluation')->getStaticJsUrl() . 'exporting.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iisevaluation')->getStaticJsUrl() . 'highcharts.js', 'text/javascript');

        $containers = array();
        $counter = 1;
        foreach($arrayJS as $js){
            OW::getDocument()->addScriptDeclaration($js);
            $containers[] = 'container'.$counter;
            $counter++;
        }

        $this->assign('containers', $containers);

        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");

        $js = 'Highcharts.theme = {colors: ["#FF4D4D", "#4DB74D"],lang: {decimalPoint: \'.\',thousandsSep: \'\'}};Highcharts.setOptions(Highcharts.theme);';
        OW::getDocument()->addScriptDeclaration($js);

        $this->assign('degreeClass', $service->getBackgroundColorOfDegree($degree));

    }

    public function questions($params)
    {
        if(!isset($params['catId'])){
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.index'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        if(!$service->checkUserPermission()){
            $this->redirect(OW_URL_HOME);
        }
        $userId = OW::getUser()->getId();
        $showAnotherUserAnswer = $params['userId'] && OW::getUser()->isAdmin();
        if($showAnotherUserAnswer){
            $userId = $params['userId'];
        }
        $catId = $params['catId'];
        $category = $service->getCategory($catId);
        if($category==null){
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.index'));
        }
        $questions = $service->getQuestions($catId);
        $questionsArray = array();
        $counter = 1;
        foreach ($questions as $question) {
            if(sizeof($service->getValuesOfQuestion($question->id))>0) {
                $answerValue = $service->checkQuestionsAnswered($question->id, $userId);
                $questionsInf= array(
                    'title' => $question->title,
                    'id' => $question->id,
                    'answeredOrNot' =>  $answerValue!= null ? $answerValue : '-',
                    'counter' => $counter
                );

                if($showAnotherUserAnswer){
                    $questionsInf['url'] = OW::getRouter()->urlForRoute('iisevaluation.question.user', array('id' => $question->id, 'userId' => $params['userId']));
                }else{
                    $questionsInf['url'] = OW::getRouter()->urlForRoute('iisevaluation.question', array('id' => $question->id));
                }

                $questionsArray[] = $questionsInf;
                $counter++;
            }
        }
        $this->assign('categoryName', $category->name);
        $this->assign('categoryDescription', $category->description);
        $this->assign('header', OW::getLanguage()->text('iisevaluation', 'category_questions_header'));
        if($showAnotherUserAnswer){
            $this->assign('returnToCategory', OW::getRouter()->urlForRoute('iisevaluation.index.user', array('userId' => $userId)));
        }else{
            $this->assign('returnToCategory', OW::getRouter()->urlForRoute('iisevaluation.index'));
        }
        $this->assign('questions', $questionsArray);
        $this->assign('countOfQuestions', $service->getCountOfQuestionsOfCategory($catId));
        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");
    }

    public function question($params)
    {
        if(!isset($params['id'])){
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.index'));
        }
        $service = IISEVALUATION_BOL_Service::getInstance();
        if(!$service->checkUserPermission()){
            $this->redirect(OW_URL_HOME);
        }
        $question = $service->getQuestion($params['id']);
        $showAnotherUserAnswer = $params['userId'] && OW::getUser()->isAdmin();
        $userId = OW::getUser()->getId();
        if($showAnotherUserAnswer){
            $userId = $params['userId'];
        }
        $answer = $service->getAnswerByQuestionIdAndUserId($question->id, $userId);
        if($question==null || sizeof($service->getValuesOfQuestion($question->id))==0){
            $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.index'));
        }

        $this->assign('header', $question->title);
        if($showAnotherUserAnswer){
            $this->assign('return_to_questions', OW::getRouter()->urlForRoute('iisevaluation.questions.user', array('catId' => $question->categoryId, 'userId' => $userId)));
        }else{
            $this->assign('return_to_questions', OW::getRouter()->urlForRoute('iisevaluation.questions', array('catId' => $question->categoryId)));
        }

        if($question->hasDescribe){
            $this->assign('hasDescribe',true);
        }

        if($question->hasFile){
            $this->assign('hasFile',true);
        }

        $this->assign('questionDescription',$question->description);

        if($question->hasVerification){
            $this->assign('hasVerification',true);
        }

        if($answer!=null && !empty($answer->file)){
            $this->assign('hasFileBefore', $service->getFile($answer->file));
            $this->assign('defaultFileIcon', Ow::getPluginManager()->getPlugin('iisevaluation')->getStaticUrl() . 'images/file.png');
        }

        $this->assign('level', $question->level);

        $this->assign('owner',$service->checkUserPermissionForSubmitAnswer() && $userId==OW::getUser()->getId());

        $form = $service->getQuestionDataForm(OW::getRouter()->urlForRoute('iisevaluation.question', array('id' => $question->id)) , $question, $answer, $userId);
        $this->addForm($form);

        if ( OW::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                $value = $_REQUEST['values'];
                $sign = $_REQUEST['sign'];
                $description = $_REQUEST['description'];
                $file = $service->saveFile('file', false);
                if($file==null && $answer->file!=null){
                    $file = $answer->file;
                }
                if($answer==null) {
                    $service->saveAnswer($sign, $question->id, $description, $file, $value);
                }else{
                    $service->updateAnswer($answer->id, $sign, $question->id, $description, $file, $value);
                }
                OW::getFeedback()->info(OW::getLanguage()->text('iisevaluation', 'saved_answer_successfully'));
                $this->redirect(OW::getRouter()->urlForRoute('iisevaluation.questions', array('catId' => $question->categoryId)));
            }
        }

        $cssDir = OW::getPluginManager()->getPlugin("iisevaluation")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisevaluation.css");
    }
}