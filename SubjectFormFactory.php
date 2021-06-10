<?php

declare(strict_types=1);

namespace App\Forms;

use App\Constants\ArticleSkillConstants as ArticleSkill;
use App\Curl\CurlException;
use App\Model\SubjectManager;
use App\Model\UserManager;
use Nette\Application\UI\Form;
use Nette\Mail\SendException;
use App\Constants\RoleConstants as Role;

/**
 * Creates a form for registration of new subjects
 *
 * @since 1.0.1
 * @package App\Forms
 */
class SubjectFormFactory
{

  private FormFactory $formFactory;
  private UserFormFactory $userFormFactory;
  private SubjectManager $subjectManager;
  private UserManager $userManager;

  /**
   * SubjectFormFactory constructor
   *
   * @param  FormFactory  $formFactory
   * @param  UserFormFactory  $userFormFactory
   * @param  SubjectManager  $subjectManager
   * @param  UserManager  $userManager
   */
  public function __construct(
    FormFactory $formFactory,
    UserFormFactory $userFormFactory,
    SubjectManager $subjectManager,
    UserManager $userManager
  ) {
    $this->formFactory = $formFactory;
    $this->userFormFactory = $userFormFactory;
    $this->subjectManager = $subjectManager;
    $this->userManager = $userManager;
  }

  /**
   * Creates registration form for new subjects
   *
   * @param $onSuccess - Function used when form is successfully sent
   * @param $onError - Function used when form error occurs
   * @return Form Registration form
   */
  public function create($onSuccess, $onError): Form
  {
    $form = $this->formFactory->create();
    $this->userFormFactory->userContainer($form);
    $subjectContainer = $form->addContainer('subjectContainer');
    $subjectContainer->addText('displayName', 'Zadej jméno firmy, jak se bude zobrazovat')
      ->setRequired();

    $subjectContainer->addText('name', 'Zadej celé jméno firmy')
      ->setRequired()
      ->addRule(Form::MIN_LENGTH, 'Jméno firmy musí být delší než %d', 3);

    $subjectContainer->addText('street', 'Ulice')
      ->setRequired();

    $subjectContainer->addText('city', 'Město')
      ->setRequired();

    $subjectContainer->addText('zip', 'PSČ')
      ->setRequired()
      ->addRule(Form::PATTERN, 'Prosím, zadej poštovní číslo v požadovaném formátu bez mezer', '^[0-9]{5}$');

    $subjectContainer->addText('bankAccount', 'Bankovní účet')
      ->setRequired()
      ->addRule(Form::PATTERN, 'Prosím, zadej číslo bankovního účtu v požadovaném formátu',
        '\d{0,6}\-{0,1}\d{10,16}\/\d{4}');

    $subjectContainer->addText('country', 'Stát')
      ->setRequired();

    $subjectContainer->addText('idNumber', 'Zadej IČ')
      ->setRequired()
      ->addRule(Form::PATTERN, 'Prosím, zadej IČO v požadovaném formátu', '^[0-9]{8}$');

    $subjectContainer->addCheckbox('vatPayer', 'Jste plátcem DPH')
      ->addCondition(Form::EQUAL, true)
      ->toggle('vatPayer');

    $subjectContainer->addText('vatNumber', 'DIČ')
      ->setOption('id', 'vatPayer')
      ->addConditionOn($subjectContainer['vatPayer'], Form::EQUAL, true)
      ->setRequired()
      ->addRule(Form::PATTERN, 'Prosím, zadej DIČ v požadovaném formátu', '^[A-Z]{2}[0-9]{8}$');

    $subjectContainer->addMultiSelect('articleSkill', 'ArticleSkill', ArticleSkill::ALL)
      ->setRequired();

    $form->addSubmit('send', 'Zaregistrovat se');

    $form->onSuccess[] = function ($form, $values) use ($onSuccess, $onError) {
      $this->onSuccessForm($form, $values, $onSuccess, $onError);
    };

    return $form;
  }

  /**
   * When the form is successfully sent, the endpoints are called
   *
   * @param Form $form Registration form for new subjects
   * @param array $values Form values
   * @param $onSuccess - Function used when form is successfully sent
   * @param $onError - Function used when form error occurs
   */
  public function onSuccessForm(Form $form, array $values, $onSuccess, $onError): void
  {
    $userData = $values['userContainer'];
    $subjectData = $values['subjectContainer'];
    try {
      $userResponse = $this->userManager->addUser($userData);
      $subjectResponse = $this->subjectManager->addSubject($subjectData);
      $this->subjectManager->addUserToSubject($userResponse['id'], $subjectResponse['id'], Role::OWNER);
      $onSuccess($form, $values);
    } catch (CurlException | SendException $ex) {
      $onError($form, $values);
    }
  }
}