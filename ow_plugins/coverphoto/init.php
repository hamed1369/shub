<?php

/**
 * Cover Photo
 */

OW::getRouter()->addRoute(new OW_Route('coverphoto-forms', 'coverphoto/forms', 'COVERPHOTO_CTRL_Forms', 'index'));
OW::getRouter()->addRoute(new OW_Route('coverphoto-forms-delete-item', 'coverphoto/forms/delete/:id', 'COVERPHOTO_CTRL_Forms', 'deleteItem'));
OW::getRouter()->addRoute(new OW_Route('coverphoto-forms-use-item', 'coverphoto/forms/use/:id', 'COVERPHOTO_CTRL_Forms', 'useItem'));
OW::getRouter()->addRoute(new OW_Route('coverphoto-forms-delete-item-float', 'coverphoto/forms/delete/float/:id', 'COVERPHOTO_CTRL_Forms', 'deleteItemFloat'));
OW::getRouter()->addRoute(new OW_Route('coverphoto-forms-use-item-float', 'coverphoto/forms/use/float/:id', 'COVERPHOTO_CTRL_Forms', 'useItemFloat'));
OW::getRouter()->addRoute(new OW_Route('coverphoto-forms-cover-crop', 'coverphoto/forms/cover/crop', 'COVERPHOTO_CTRL_Forms', 'coverCrop'));
OW::getRouter()->addRoute(new OW_Route('coverphoto-index', 'coverphoto', 'COVERPHOTO_CTRL_Forms', 'index'));