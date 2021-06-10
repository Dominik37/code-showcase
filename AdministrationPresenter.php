<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\Forms\SubjectFormFactory;
use App\Forms\UserFormFactory;
use App\Presenters\BasePresenter;
use Nette\Forms\Form;

/**
 * Handles basic administration of the app
 *
 * @since 1.0.2
 * @package App\CoreModule\Presenters
 */
class AdministrationPresenter extends BasePresenter
{

  private SubjectFormFactory $subjectFactory;
  private UserFormFactory $userFormFactory;

  /**
   * AdministrationPresenter constructor
   *
   * @param  SubjectFormFactory  $subjectFactory
   * @param  UserFormFactory  $userFormFactory
   */
  public function __construct(SubjectFormFactory $subjectFactory, UserFormFactory $userFormFactory)
  {
    parent::__construct();
    $this->subjectFactory = $subjectFactory;
    $this->userFormFactory = $userFormFactory;
  }

  /**
   * Creates form for adding new users
   *
   * @return Form Form for adding new users
   */
  protected function createComponentUserForm(): Form
  {
    return $this->userFormFactory->create(
    //onSuccess
      function () {
        $this->flashMessage('Uživatel byl úspěšně přidán, informace dostane e-mailem.',
          BasePresenter::MSG_SUCCESS);
        $this->redirect(':Core:Homepage:default');
      },
      //onError
      function () {
        $this->flashMessage('Něco se pokazilo, prosím opakuj akci později, nebo kontaktuj admina.',
          BasePresenter::MSG_ERROR);
        $this->redirect(':Core:Homepage:default');
      }
    );
  }

  /**
   * Creates form for adding new subjects
   *
   * @return Form Form for adding new subjects
   */
  protected function createComponentSubjectForm(): Form
  {
    return $this->subjectFactory->create(
    //onSuccess
      function () {
        $this->flashMessage('Firma úspěšně přidána, další postup najdeš ve své e-mailové schránce.',
          BasePresenter::MSG_SUCCESS);
        $this->redirect(':Core:Homepage:default');
      },
      //onError
      function () {
        $this->flashMessage('Něco se nepovedlo, prosím opakuj akci později, nebo kontaktuj admina',
          BasePresenter::MSG_ERROR);
        $this->redirect(':Core:Homepage:default');
      }
    );
  }
}